<?php

namespace App\Http\Controllers;

use App\Enums\ProductionPlanStatus;
use App\Http\Requests\StoreProductionPlanRequest;
use App\Http\Requests\UpdateProductionPlanRequest;
use App\Models\Product;
use App\Models\ProductionPlan;
use App\Services\ProductionIngredientCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ProductionPlanController extends Controller
{
    /**
     * Menampilkan daftar seluruh rencana produksi.
     */
    public function index(Request $request): View
    {
        $query = ProductionPlan::query()
            ->with(['creator', 'details.product'])
            ->withCount('details');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where('plan_number', 'LIKE', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->status($request->input('status'));
        }

        if ($request->filled('production_date')) {
            $query->productionDate($request->input('production_date'));
        }

        $plans = $query->orderByDesc('production_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('production-plans.index', [
            'plans' => $plans,
            'statuses' => ProductionPlanStatus::cases(),
            'currentSearch' => $request->input('search'),
            'currentStatus' => $request->input('status'),
            'currentDate' => $request->input('production_date'),
        ]);
    }

    /**
     * Menampilkan formulir pembuatan rencana produksi baru.
     */
    public function create(): View
    {
        $products = Product::query()
            ->active()
            ->withCount('recipeItems')
            ->orderBy('name')
            ->get();

        $suggestedPlanNumber = ProductionPlan::generatePlanNumber();
        $today = Carbon::today()->format('Y-m-d');

        return view('production-plans.create', [
            'products' => $products,
            'suggestedPlanNumber' => $suggestedPlanNumber,
            'today' => $today,
        ]);
    }

    /**
     * Menyimpan rencana produksi dan rincian menu hidangan ke database.
     */
    public function store(StoreProductionPlanRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $plan = DB::transaction(function () use ($request, $validated) {
            $planNumber = ProductionPlan::generatePlanNumber(
                Carbon::parse($validated['plan_date'])
            );

            $productionPlan = ProductionPlan::create([
                'plan_number' => $planNumber,
                'plan_date' => $validated['plan_date'],
                'production_date' => $validated['production_date'],
                'status' => ProductionPlanStatus::Scheduled,
                'notes' => $validated['notes'] ?? null,
                'created_by' => $request->user()->id,
            ]);

            foreach ($validated['items'] as $item) {
                $productionPlan->details()->create([
                    'product_id' => $item['product_id'],
                    'planned_quantity' => $item['planned_quantity'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            return $productionPlan;
        });

        return redirect()
            ->route('production-plans.show', $plan)
            ->with('success', "Rencana produksi {$plan->plan_number} berhasil dibuat dan dijadwalkan.");
    }

    /**
     * Menampilkan detail rencana produksi, komposisi menu, dan kalkulasi kebutuhan bahan baku otomatis.
     */
    public function show(ProductionPlan $productionPlan, ProductionIngredientCalculator $calculator): View
    {
        $productionPlan->load([
            'creator',
            'details.product.recipeItems.ingredient',
        ]);

        $calculation = $calculator->calculateForPlan($productionPlan);

        return view('production-plans.show', [
            'plan' => $productionPlan,
            'calculation' => $calculation,
        ]);
    }

    /**
     * Menampilkan lembar kerja / laporan kebutuhan bahan baku rencana produksi untuk dapur & logistik gudang.
     */
    public function ingredients(ProductionPlan $productionPlan, ProductionIngredientCalculator $calculator): View
    {
        $productionPlan->load([
            'creator',
            'details.product.recipeItems.ingredient',
        ]);

        $calculation = $calculator->calculateForPlan($productionPlan);

        return view('production-plans.ingredients', [
            'plan' => $productionPlan,
            'calculation' => $calculation,
        ]);
    }

    /**
     * Preview kalkulasi kebutuhan bahan baku secara interaktif untuk simulasi menu & porsi.
     */
    public function calculatePreview(Request $request, ProductionIngredientCalculator $calculator): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.planned_quantity' => ['required', 'integer', 'min:1'],
        ]);

        $calculation = $calculator->calculateForItems($validated['items']);

        return response()->json($calculation);
    }

    /**
     * Menampilkan formulir edit rencana produksi.
     */
    public function edit(ProductionPlan $productionPlan): View
    {
        $productionPlan->load('details.product');

        $products = Product::query()
            ->active()
            ->withCount('recipeItems')
            ->orderBy('name')
            ->get();

        return view('production-plans.edit', [
            'plan' => $productionPlan,
            'products' => $products,
            'statuses' => ProductionPlanStatus::cases(),
        ]);
    }

    /**
     * Memperbarui rencana produksi beserta rinciannya.
     */
    public function update(UpdateProductionPlanRequest $request, ProductionPlan $productionPlan): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $productionPlan) {
            $productionPlan->update([
                'plan_date' => $validated['plan_date'],
                'production_date' => $validated['production_date'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Hapus detail lama dan ganti dengan yang baru
            $productionPlan->details()->delete();

            foreach ($validated['items'] as $item) {
                $productionPlan->details()->create([
                    'product_id' => $item['product_id'],
                    'planned_quantity' => $item['planned_quantity'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('production-plans.show', $productionPlan)
            ->with('success', "Rencana produksi {$productionPlan->plan_number} berhasil diperbarui.");
    }

    /**
     * Menghapus rencana produksi.
     */
    public function destroy(ProductionPlan $productionPlan): RedirectResponse
    {
        // Cek jika sudah memiliki relasi ke pelaksanaan produksi fisik atau pengeluaran bahan
        $hasExecutions = false;

        if (Schema::hasTable('productions')) {
            $detailIds = $productionPlan->details()->pluck('id');
            if ($detailIds->isNotEmpty() && DB::table('productions')->whereIn('production_plan_detail_id', $detailIds)->exists()) {
                $hasExecutions = true;
            }
        }

        if (Schema::hasTable('material_requests') && DB::table('material_requests')->where('production_plan_id', $productionPlan->id)->exists()) {
            $hasExecutions = true;
        }

        if ($hasExecutions) {
            $productionPlan->update(['status' => ProductionPlanStatus::Cancelled]);

            return redirect()
                ->route('production-plans.index')
                ->with('warning', "Rencana produksi {$productionPlan->plan_number} sudah memiliki catatan operasional terkait sehingga statusnya dialihkan ke 'Dibatalkan'.");
        }

        $planNumber = $productionPlan->plan_number;
        $productionPlan->delete();

        return redirect()
            ->route('production-plans.index')
            ->with('success', "Rencana produksi {$planNumber} berhasil dihapus.");
    }
}
