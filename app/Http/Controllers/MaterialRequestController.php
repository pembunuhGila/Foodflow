<?php

namespace App\Http\Controllers;

use App\Enums\ProductionPlanStatus;
use App\Models\Ingredient;
use App\Models\IngredientStockTransaction;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestDetail;
use App\Models\ProductionPlan;
use App\Services\ProductionIngredientCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MaterialRequestController extends Controller
{
    /**
     * Menampilkan daftar permintaan bahan baku (default: pending).
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->input('status', 'pending');

        $query = MaterialRequest::query()
            ->with(['productionPlan', 'requestedBy', 'verifiedBy', 'details.ingredient'])
            ->withCount('details');

        if ($statusFilter === 'pending') {
            $query->pending();
        } elseif ($statusFilter === 'approved') {
            $query->approved();
        } elseif ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhereHas('productionPlan', function ($planQuery) use ($search) {
                        $planQuery->where('plan_number', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('requestedBy', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        $materialRequests = $query->orderByDesc('request_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $pendingCount = MaterialRequest::pending()->count();

        return view('material-requests.index', [
            'materialRequests' => $materialRequests,
            'currentStatus' => $statusFilter,
            'pendingCount' => $pendingCount,
            'currentSearch' => $request->input('search'),
        ]);
    }

    /**
     * Menampilkan formulir pembuatan permintaan bahan dari dapur (khusus Produksi & Admin).
     */
    public function create(): View
    {
        $user = auth()->user();
        if (! $user->isProduksi() && ! $user->isAdmin()) {
            abort(403, 'Akses Ditolak: Hanya staf produksi dan admin yang dapat membuat permintaan bahan.');
        }

        $productionPlans = ProductionPlan::query()
            ->whereIn('status', [ProductionPlanStatus::Scheduled, ProductionPlanStatus::InProgress, 'scheduled', 'in_progress', 'draft'])
            ->with(['details.product'])
            ->orderByDesc('production_date')
            ->get();

        $ingredients = Ingredient::active()->orderBy('name')->get();

        return view('material-requests.create', [
            'productionPlans' => $productionPlans,
            'ingredients' => $ingredients,
        ]);
    }

    /**
     * Mengajukan permintaan bahan baku secara otomatis dari kalkulasi rencana produksi (1-Click dari Rencana Produksi).
     */
    public function storeFromPlan(Request $request, ProductionPlan $productionPlan, ProductionIngredientCalculator $calculator): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isProduksi() && ! $user->isAdmin()) {
            abort(403, 'Akses Ditolak: Hanya staf produksi dan admin yang dapat mengajukan permintaan bahan.');
        }

        // Cek jika sudah ada permintaan pending untuk rencana ini
        $existingPending = $productionPlan->materialRequests()->pending()->first();
        if ($existingPending) {
            return redirect()
                ->route('material-requests.show', $existingPending)
                ->with('info', "Rencana produksi {$productionPlan->plan_number} sudah memiliki pengajuan permintaan bahan yang sedang menunggu verifikasi gudang (#MR-".str_pad($existingPending->id, 4, '0', STR_PAD_LEFT).').');
        }

        $calculation = $calculator->calculateForPlan($productionPlan);
        $items = $calculation['items'] ?? [];

        if (empty($items)) {
            return redirect()->back()->with('error', 'Tidak ada kebutuhan bahan baku terdaftar pada menu rencana produksi ini.');
        }

        $materialRequest = DB::transaction(function () use ($productionPlan, $items, $request, $user) {
            $req = MaterialRequest::create([
                'production_plan_id' => $productionPlan->id,
                'requested_by' => $user->id,
                'request_date' => Carbon::today()->toDateString(),
                'status' => 'pending',
                'notes' => $request->input('notes', "Permintaan bahan baku dapur untuk rencana produksi {$productionPlan->plan_number}"),
            ]);

            foreach ($items as $item) {
                $requiredQty = max(0.001, round((float) ($item['required_quantity'] ?? 0), 3));
                $req->details()->create([
                    'ingredient_id' => $item['ingredient_id'],
                    'requested_quantity' => $requiredQty,
                    'approved_quantity' => 0,
                    'issued_quantity' => 0,
                    'unit' => $item['unit'] ?? 'satuan',
                    'notes' => null,
                ]);
            }

            return $req;
        });

        return redirect()
            ->route('material-requests.show', $materialRequest)
            ->with('success', "Permintaan bahan baku #MR-".str_pad($materialRequest->id, 4, '0', STR_PAD_LEFT)." untuk rencana produksi {$productionPlan->plan_number} berhasil diajukan ke bagian gudang! Petugas gudang dapat langsung memeriksa ketersediaan stok fisik dan melakukan verifikasi penyerahan.");
    }

    /**
     * Menyimpan formulir pengajuan permintaan bahan baku manual.
     */
    public function store(Request $request, ProductionIngredientCalculator $calculator): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isProduksi() && ! $user->isAdmin()) {
            abort(403, 'Akses Ditolak: Hanya staf produksi dan admin yang dapat mengajukan permintaan bahan.');
        }

        $validated = $request->validate([
            'production_plan_id' => ['nullable', 'exists:production_plans,id'],
            'request_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['nullable', 'array'],
            'items.*.ingredient_id' => ['required_with:items', 'exists:ingredients,id'],
            'items.*.requested_quantity' => ['required_with:items', 'numeric', 'min:0.001'],
            'items.*.unit' => ['required_with:items', 'string', 'max:50'],
        ]);

        $plan = ! empty($validated['production_plan_id'])
            ? ProductionPlan::find($validated['production_plan_id'])
            : null;

        // Jika items tidak disertakan secara manual tetapi ada production_plan_id, kalkulasi otomatis dari resep
        $items = $validated['items'] ?? [];
        if (empty($items) && $plan) {
            $calculation = $calculator->calculateForPlan($plan);
            foreach ($calculation['items'] ?? [] as $calcItem) {
                $items[] = [
                    'ingredient_id' => $calcItem['ingredient_id'],
                    'requested_quantity' => max(0.001, round((float) ($calcItem['required_quantity'] ?? 0), 3)),
                    'unit' => $calcItem['unit'] ?? 'satuan',
                ];
            }
        }

        if (empty($items)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['items' => 'Minimal satu bahan baku wajib dimasukkan dalam pengajuan permintaan.']);
        }

        $materialRequest = DB::transaction(function () use ($validated, $items, $user, $plan) {
            $defaultNotes = $plan
                ? "Permintaan bahan baku dapur untuk rencana produksi {$plan->plan_number}"
                : 'Permintaan bahan baku tambahan dapur';

            $req = MaterialRequest::create([
                'production_plan_id' => $plan?->id,
                'requested_by' => $user->id,
                'request_date' => $validated['request_date'],
                'status' => 'pending',
                'notes' => $validated['notes'] ?? $defaultNotes,
            ]);

            foreach ($items as $item) {
                $req->details()->create([
                    'ingredient_id' => $item['ingredient_id'],
                    'requested_quantity' => $item['requested_quantity'],
                    'approved_quantity' => 0,
                    'issued_quantity' => 0,
                    'unit' => $item['unit'],
                    'notes' => null,
                ]);
            }

            return $req;
        });

        return redirect()
            ->route('material-requests.show', $materialRequest)
            ->with('success', "Permintaan bahan baku #MR-".str_pad($materialRequest->id, 4, '0', STR_PAD_LEFT).' berhasil diajukan ke Gudang! Petugas gudang dapat langsung memverifikasi penyerahan.');
    }

    /**
     * Menampilkan detail permintaan bahan baku dan form verifikasi.
     */
    public function show(MaterialRequest $materialRequest): View
    {
        $materialRequest->load([
            'productionPlan',
            'requestedBy',
            'verifiedBy',
            'details.ingredient',
            'stockTransactions.ingredient',
        ]);

        return view('material-requests.show', [
            'materialRequest' => $materialRequest,
        ]);
    }

    /**
     * Memverifikasi permintaan bahan dan mencatat transaksi stok keluar (Stok OUT - BR-05).
     */
    public function verify(Request $request, MaterialRequest $materialRequest): RedirectResponse
    {
        // Pastikan hanya role gudang dan admin yang dapat melakukan verifikasi
        $user = $request->user();
        if (! $user->isGudang() && ! $user->isAdmin()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk memverifikasi permintaan bahan.');
        }

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.detail_id' => ['required', 'integer'],
            'items.*.issued_quantity' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'items.required' => 'Daftar bahan yang diverifikasi wajib ada.',
            'items.*.issued_quantity.required' => 'Jumlah diserahkan wajib diisi.',
            'items.*.issued_quantity.numeric' => 'Jumlah diserahkan harus berupa angka.',
            'items.*.issued_quantity.min' => 'Jumlah diserahkan tidak boleh bernilai negatif.',
        ]);

        DB::transaction(function () use ($materialRequest, $validated, $user) {
            // Penguncian baris (lockForUpdate) untuk mencegah konkurensi / verifikasi ganda
            /** @var MaterialRequest $lockedRequest */
            $lockedRequest = MaterialRequest::query()
                ->where('id', $materialRequest->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedRequest->isPending()) {
                throw ValidationException::withMessages([
                    'status' => 'Permintaan bahan ini sudah pernah diverifikasi sebelumnya.',
                ]);
            }

            $itemsByDetailId = collect($validated['items'])->keyBy('detail_id');
            $details = $lockedRequest->details()->with('ingredient')->get();

            // Keterangan transaksi stok keluar sesuai nomor rencana produksi jika ada
            $planNumber = $lockedRequest->productionPlan?->plan_number;
            $transactionNote = $planNumber
                ? "Dikeluarkan untuk rencana produksi {$planNumber}, menunggu pencatatan pemakaian"
                : "Dikeluarkan untuk permintaan bahan #{$lockedRequest->id}, menunggu pencatatan pemakaian";

            $today = Carbon::today()->toDateString();

            foreach ($details as $detail) {
                if (! $itemsByDetailId->has($detail->id)) {
                    throw ValidationException::withMessages([
                        'items' => "Rincian bahan ID {$detail->id} tidak ditemukan dalam input verifikasi.",
                    ]);
                }

                $issuedQty = (float) $itemsByDetailId[$detail->id]['issued_quantity'];
                $requestedQty = (float) $detail->requested_quantity;

                // 1. Validasi: tidak boleh negatif
                if ($issuedQty < 0) {
                    throw ValidationException::withMessages([
                        "items.{$detail->id}.issued_quantity" => "Jumlah diserahkan untuk {$detail->ingredient->name} tidak boleh bernilai negatif.",
                    ]);
                }

                // 2. Validasi: tidak melebihi jumlah yang diminta
                if ($issuedQty > $requestedQty) {
                    throw ValidationException::withMessages([
                        "items.{$detail->id}.issued_quantity" => "Jumlah diserahkan ({$issuedQty} {$detail->unit}) melebihi jumlah yang diminta ({$requestedQty} {$detail->unit}) untuk bahan {$detail->ingredient->name}.",
                    ]);
                }

                // 3. Penguncian dan validasi stok bahan baku
                /** @var Ingredient $ingredient */
                $ingredient = Ingredient::query()
                    ->where('id', $detail->ingredient_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $availableStock = (float) ($ingredient->current_stock ?? 0);

                if ($issuedQty > $availableStock) {
                    throw ValidationException::withMessages([
                        "items.{$detail->id}.issued_quantity" => "Stok gudang bahan {$ingredient->name} tidak mencukupi (Tersedia: {$availableStock} {$ingredient->unit}, Diserahkan: {$issuedQty} {$detail->unit}).",
                    ]);
                }

                // Simpan jumlah aktual yang diserahkan ke detail permintaan
                $detail->issued_quantity = $issuedQty;
                $detail->approved_quantity = $issuedQty;
                $detail->save();

                // Kurangi stok bahan jika ada bahan yang diserahkan
                if ($issuedQty > 0) {
                    $ingredient->current_stock = max(0.0, $availableStock - $issuedQty);
                    $ingredient->save();

                    // Catat transaksi stok keluar (Stok OUT) pada ingredient_stock_transactions
                    IngredientStockTransaction::create([
                        'ingredient_id' => $ingredient->id,
                        'transaction_type' => 'out',
                        'quantity' => $issuedQty,
                        'transaction_date' => $today,
                        'reference_type' => 'material_request',
                        'reference_id' => $lockedRequest->id,
                        'notes' => $transactionNote,
                        'created_by' => $user->id,
                    ]);
                }
            }

            // Update status permintaan menjadi approved dan catat petugas verifikasi
            $lockedRequest->status = 'approved';
            $lockedRequest->verified_by = $user->id;
            if (! empty($validated['notes'])) {
                $lockedRequest->notes = $lockedRequest->notes
                    ? $lockedRequest->notes."\nCatatan Verifikasi: ".$validated['notes']
                    : 'Catatan Verifikasi: '.$validated['notes'];
            }
            $lockedRequest->save();
        });

        return redirect()
            ->route('material-requests.show', $materialRequest)
            ->with('success', "Permintaan bahan #{$materialRequest->id} berhasil diverifikasi dan transaksi stok keluar telah dicatat.");
    }
}
