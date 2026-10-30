<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\ProductionMaterialUsage;
use App\Models\ProductionPlan;
use App\Services\ProductionIngredientCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductionMaterialUsageController extends Controller
{
    public function __construct(
        protected ProductionIngredientCalculator $calculator
    ) {}

    /**
     * Menampilkan riwayat atau perbandingan pemakaian bahan riil untuk rencana produksi.
     */
    public function show(ProductionPlan $productionPlan): View
    {
        $productionPlan->load([
            'details.product',
            'materialUsages.ingredient',
            'materialUsages.recorder',
        ]);

        $calculation = $this->calculator->calculateForPlan($productionPlan);

        return view('production-plans.material-usages.show', [
            'plan' => $productionPlan,
            'usages' => $productionPlan->materialUsages,
            'calculatedItems' => $calculation['items'],
        ]);
    }

    /**
     * Menampilkan formulir input pencatatan pemakaian bahan riil saat proses memasak.
     */
    public function create(ProductionPlan $productionPlan): View
    {
        $productionPlan->load(['details.product.recipeItems.ingredient', 'materialUsages']);

        $calculation = $this->calculator->calculateForPlan($productionPlan);
        $existingUsages = $productionPlan->materialUsages->keyBy('ingredient_id');

        // Satukan data terhitung dengan data yang mungkin sudah pernah disimpan
        $items = collect($calculation['items'])->map(function ($item) use ($existingUsages) {
            $existing = $existingUsages->get($item['ingredient']->id);

            return [
                'ingredient_id' => $item['ingredient']->id,
                'ingredient_name' => $item['ingredient']->name,
                'unit' => $item['unit'] ?? $item['ingredient']->unit,
                'planned_quantity' => (float) ($item['required_quantity'] ?? 0),
                'actual_quantity' => $existing ? (float) $existing->actual_quantity : (float) ($item['required_quantity'] ?? 0),
                'notes' => $existing ? $existing->notes : '',
            ];
        })->values()->all();

        // Jika resep kosong, sediakan opsi bahan baku aktif untuk input manual
        $allIngredients = Ingredient::active()->orderBy('name')->get(['id', 'name', 'unit']);

        return view('production-plans.material-usages.create', [
            'plan' => $productionPlan,
            'items' => $items,
            'allIngredients' => $allIngredients,
        ]);
    }

    /**
     * Menyimpan data pemakaian bahan riil dan menghitung selisih efisiensi dapur.
     */
    public function store(Request $request, ProductionPlan $productionPlan): RedirectResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.ingredient_id' => ['required', 'exists:ingredients,id'],
            'items.*.planned_quantity' => ['required', 'numeric', 'min:0'],
            'items.*.actual_quantity' => ['required', 'numeric', 'min:0'],
            'items.*.unit' => ['required', 'string', 'max:50'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($validated, $productionPlan, $request) {
            foreach ($validated['items'] as $item) {
                $planned = (float) $item['planned_quantity'];
                $actual = (float) $item['actual_quantity'];
                $difference = round($actual - $planned, 3);

                ProductionMaterialUsage::updateOrCreate(
                    [
                        'production_plan_id' => $productionPlan->id,
                        'ingredient_id' => (int) $item['ingredient_id'],
                    ],
                    [
                        'planned_quantity' => $planned,
                        'actual_quantity' => $actual,
                        'difference_quantity' => $difference,
                        'unit' => $item['unit'],
                        'notes' => $item['notes'] ?? null,
                        'recorded_by' => $request->user()->id,
                    ]
                );
            }
        });

        return redirect()
            ->route('production-plans.material-usages.show', $productionPlan)
            ->with('success', "Catatan pemakaian bahan riil untuk jadwal masak {$productionPlan->plan_code} berhasil disimpan.");
    }
}
