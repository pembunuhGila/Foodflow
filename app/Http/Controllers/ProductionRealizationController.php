<?php

namespace App\Http\Controllers;

use App\Enums\ProductionPlanStatus;
use App\Http\Requests\StoreProductionRealizationRequest;
use App\Models\Production;
use App\Models\ProductionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ProductionRealizationController extends Controller
{
    /**
     * Menampilkan daftar rencana produksi aktif yang siap / sedang dicatat realisasinya.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->input('status', 'in_progress');

        $query = ProductionPlan::query()
            ->with(['creator', 'details.product', 'details.production'])
            ->withCount('details');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where('plan_number', 'LIKE', "%{$search}%");
        }

        if ($request->filled('production_date')) {
            $query->productionDate($request->input('production_date'));
        }

        if ($statusFilter === 'all') {
            $query->whereIn('status', [
                ProductionPlanStatus::InProgress->value,
                ProductionPlanStatus::Completed->value,
            ]);
        } elseif ($statusFilter === 'completed') {
            $query->status(ProductionPlanStatus::Completed);
        } else {
            // Default: tampilkan yang sedang dimasak (in_progress)
            $query->status(ProductionPlanStatus::InProgress);
        }

        $plans = $query->orderByDesc('production_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        // Rencana produksi in_progress yang tersedia untuk tombol cepat
        $inProgressCount = ProductionPlan::query()
            ->status(ProductionPlanStatus::InProgress)
            ->count();

        return view('productions.index', [
            'plans' => $plans,
            'currentSearch' => $request->input('search'),
            'currentStatus' => $statusFilter,
            'currentDate' => $request->input('production_date'),
            'inProgressCount' => $inProgressCount,
        ]);
    }

    /**
     * Menampilkan formulir pencatatan realisasi porsi masak untuk rencana produksi terpilih.
     */
    public function create(Request $request, ?ProductionPlan $productionPlan = null): View|RedirectResponse
    {
        // Jika parameter model binding tidak ada, cari lewat query parameter ?plan_id=
        if (! $productionPlan && $request->filled('plan_id')) {
            $productionPlan = ProductionPlan::find($request->input('plan_id'));
        }

        // Jika masih belum ada rencana terpilih, arahkan ke daftar rencana
        if (! $productionPlan) {
            $firstInProgress = ProductionPlan::query()
                ->status(ProductionPlanStatus::InProgress)
                ->orderBy('production_date')
                ->first();

            if ($firstInProgress) {
                return redirect()->route('production-plans.realization.create', $firstInProgress);
            }

            return redirect()
                ->route('productions.index')
                ->with('info', 'Silakan pilih salah satu rencana produksi yang sedang berjalan untuk mencatat realisasi masakan.');
        }

        // Periksa status rencana produksi
        $statusValue = $productionPlan->status instanceof ProductionPlanStatus
            ? $productionPlan->status->value
            : $productionPlan->status;

        if ($statusValue !== ProductionPlanStatus::InProgress->value) {
            if ($statusValue === ProductionPlanStatus::Completed->value) {
                return redirect()
                    ->route('productions.index')
                    ->with('warning', "Rencana produksi {$productionPlan->plan_number} sudah berstatus Selesai dan tidak menerima pencatatan baru.");
            }

            return redirect()
                ->route('productions.index')
                ->with('warning', "Pencatatan realisasi hanya dapat dilakukan pada rencana produksi yang sedang berjalan (In Progress). Status saat ini: {$productionPlan->status->label()}.");
        }

        $productionPlan->load([
            'creator',
            'details.product',
            'details.production',
        ]);

        return view('productions.create', [
            'plan' => $productionPlan,
            'today' => Carbon::today()->format('Y-m-d'),
        ]);
    }

    /**
     * Menyimpan atau memperbarui data realisasi porsi masak dan mengevaluasi status penyelesaian rencana.
     */
    public function store(StoreProductionRealizationRequest $request, ProductionPlan $productionPlan): RedirectResponse
    {
        $validated = $request->validated();
        $productionDate = $validated['production_date'] ?? Carbon::today()->format('Y-m-d');
        $userId = $request->user()->id;

        // Cek apakah kolom notes tersedia pada tabel productions di database aktif
        $hasNotesColumn = Schema::hasColumn('productions', 'notes');

        // Deteksi apakah pengguna memasukkan catatan (umum maupun per menu)
        $hasUserNotes = ! empty(trim((string) ($validated['notes'] ?? '')));

        $result = DB::transaction(function () use ($validated, $productionPlan, $productionDate, $userId, $hasNotesColumn, &$hasUserNotes) {
            // Peta detail yang valid
            $details = $productionPlan->details()->with('product')->get()->keyBy('id');

            foreach ($validated['items'] as $item) {
                $detailId = (int) $item['detail_id'];
                $detail = $details->get($detailId);

                if (! $detail) {
                    continue;
                }

                if (! empty(trim((string) ($item['notes'] ?? '')))) {
                    $hasUserNotes = true;
                }

                $actualQty = (int) $item['actual_quantity'];
                $plannedQty = (int) $detail->planned_quantity;

                // Tentukan status realisasi per menu
                // completed: realisasi memenuhi atau melampaui rencana
                // partial: realisasi masih di bawah target rencana
                $itemStatus = ($actualQty >= $plannedQty) ? 'completed' : 'partial';

                $existing = Production::where('production_plan_detail_id', $detail->id)->first();

                if ($existing) {
                    $updatePayload = [
                        'actual_quantity' => $actualQty,
                        'production_date' => $productionDate,
                        'status' => $itemStatus,
                    ];

                    if ($hasNotesColumn) {
                        $updatePayload['notes'] = $item['notes'] ?? null;
                    }

                    $existing->update($updatePayload);
                } else {
                    $createPayload = [
                        'production_number' => Production::generateProductionNumber(Carbon::parse($productionDate)),
                        'production_plan_detail_id' => $detail->id,
                        'created_by' => $userId,
                        'production_date' => $productionDate,
                        'actual_quantity' => $actualQty,
                        'status' => $itemStatus,
                    ];

                    if ($hasNotesColumn) {
                        $createPayload['notes'] = $item['notes'] ?? null;
                    }

                    Production::create($createPayload);
                }
            }

            // Muat ulang data terbaru untuk mengevaluasi apakah seluruh menu telah selesai
            $productionPlan->load('details.production');

            $allCompleted = $productionPlan->details->every(function ($d) {
                $prod = $d->production;

                return $prod !== null && $prod->actual_quantity >= $d->planned_quantity;
            });

            if ($allCompleted) {
                $productionPlan->update(['status' => ProductionPlanStatus::Completed]);

                $message = "Realisasi berhasil disimpan! Seluruh target menu telah terpenuhi, sehingga rencana produksi {$productionPlan->plan_number} otomatis ditandai 'Selesai'.";
            } else {
                // Tetap in_progress jika masih ada menu yang belum selesai
                $productionPlan->update(['status' => ProductionPlanStatus::InProgress]);

                $message = "Realisasi porsi masakan untuk rencana produksi {$productionPlan->plan_number} berhasil diperbarui (Status: Sedang Berjalan / Parsial).";
            }

            return $message;
        });

        $redirect = redirect()
            ->route('productions.index')
            ->with('success', $result);

        if (! $hasNotesColumn && $hasUserNotes) {
            $redirect->with(
                'warning',
                'Catatan realisasi belum tersimpan permanen karena kolom catatan pada database sistem belum diaktifkan oleh Administrator. Porsi dan status realisasi tetap berhasil dicatat.'
            );
        }

        return $redirect;
    }
}
