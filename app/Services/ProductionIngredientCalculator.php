<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\Product;
use App\Models\ProductionPlan;
use App\Models\ProductionPlanDetail;

class ProductionIngredientCalculator
{
    public function __construct(
        public UnitConverter $unitConverter
    ) {}

    /**
     * Menghitung kebutuhan bahan baku untuk rencana produksi yang sudah tersimpan.
     *
     * @return array{
     *     items: list<array<string, mixed>>,
     *     summary: array<string, mixed>,
     *     missing_recipes: list<Product>
     * }
     */
    public function calculateForPlan(ProductionPlan $plan): array
    {
        if (! $plan->relationLoaded('details')) {
            $plan->load('details.product.recipeItems.ingredient');
        }

        $items = $plan->details->map(function (ProductionPlanDetail $detail) {
            return [
                'product' => $detail->product,
                'product_id' => $detail->product_id,
                'planned_quantity' => (int) $detail->planned_quantity,
            ];
        })->all();

        return $this->computeCalculations($items);
    }

    /**
     * Menghitung kebutuhan bahan baku berdasarkan daftar menu dan target porsi (untuk simulasi/preview).
     *
     * @param  list<array{product_id: int, planned_quantity: int}>  $items
     * @return array{
     *     items: list<array<string, mixed>>,
     *     summary: array<string, mixed>,
     *     missing_recipes: list<Product>
     * }
     */
    public function calculateForItems(array $items): array
    {
        $productIds = collect($items)->pluck('product_id')->filter()->unique()->all();
        $products = Product::with('recipeItems.ingredient')->whereIn('id', $productIds)->get()->keyBy('id');

        $preparedItems = [];
        foreach ($items as $item) {
            $productId = $item['product_id'] ?? null;
            $product = $productId ? $products->get($productId) : null;

            if ($product) {
                $preparedItems[] = [
                    'product' => $product,
                    'product_id' => $product->id,
                    'planned_quantity' => (int) ($item['planned_quantity'] ?? 0),
                ];
            }
        }

        return $this->computeCalculations($preparedItems);
    }

    /**
     * Inti algoritma perhitungan agregasi bahan baku, perbandingan stok gudang, dan status logistik.
     *
     * @param  list<array{product: ?Product, product_id: int, planned_quantity: int}>  $entries
     * @return array{
     *     items: list<array<string, mixed>>,
     *     summary: array<string, mixed>,
     *     missing_recipes: list<Product>
     * }
     */
    protected function computeCalculations(array $entries): array
    {
        $ingredientsMap = [];
        $missingRecipes = [];
        $totalPortions = 0;

        foreach ($entries as $entry) {
            /** @var Product|null $product */
            $product = $entry['product'];
            $portion = $entry['planned_quantity'];
            $totalPortions += $portion;

            if (! $product) {
                continue;
            }

            // Jika menu belum memiliki racikan resep (UC-14 Alt 6a)
            if ($product->recipeItems->isEmpty()) {
                if (! collect($missingRecipes)->contains('id', $product->id)) {
                    $missingRecipes[] = $product;
                }

                continue;
            }

            foreach ($product->recipeItems as $recipeItem) {
                /** @var Ingredient|null $ingredient */
                $ingredient = $recipeItem->ingredient;
                if (! $ingredient) {
                    continue;
                }

                $recipeQty = (float) $recipeItem->quantity_per_unit;
                $recipeUnit = $recipeItem->unit;
                $stockUnit = $ingredient->unit;

                // Rumus: kebutuhan_total_menu = jumlah_porsi × takaran_resep_per_porsi
                $subtotalInRecipeUnit = $portion * $recipeQty;

                // Konversi satuan resep ke satuan baku persediaan gudang
                $convertedQty = $this->unitConverter->convert($subtotalInRecipeUnit, $recipeUnit, $stockUnit);
                $hasUnitMismatch = ($convertedQty === null);

                $effectiveQty = $hasUnitMismatch ? $subtotalInRecipeUnit : $convertedQty;
                $effectiveUnit = $hasUnitMismatch ? $recipeUnit : $stockUnit;

                if (! isset($ingredientsMap[$ingredient->id])) {
                    $ingredientsMap[$ingredient->id] = [
                        'ingredient' => $ingredient,
                        'ingredient_id' => $ingredient->id,
                        'ingredient_name' => $ingredient->name,
                        'ingredient_category' => $ingredient->category ?: 'Bahan Umum',
                        'unit' => $effectiveUnit,
                        'stock_unit' => $stockUnit,
                        'recipe_unit' => $recipeUnit,
                        'required_quantity' => 0.0,
                        'current_stock' => $ingredient->getCurrentStock(),
                        'minimum_stock' => $ingredient->minimum_stock !== null ? (float) $ingredient->minimum_stock : 0.0,
                        'has_unit_mismatch' => false,
                        'unit_mismatch_notes' => [],
                        'menu_breakdown' => [],
                    ];
                }

                $ingredientsMap[$ingredient->id]['required_quantity'] += $effectiveQty;

                if ($hasUnitMismatch) {
                    $ingredientsMap[$ingredient->id]['has_unit_mismatch'] = true;
                    $ingredientsMap[$ingredient->id]['unit_mismatch_notes'][] = "Satuan resep {$product->name} ({$recipeUnit}) tidak cocok dengan satuan gudang ({$stockUnit}).";
                }

                $ingredientsMap[$ingredient->id]['menu_breakdown'][] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'planned_quantity' => $portion,
                    'recipe_quantity_per_unit' => $recipeQty,
                    'recipe_unit' => $recipeUnit,
                    'subtotal_quantity' => $effectiveQty,
                    'display_subtotal' => $this->unitConverter->formatQuantity($effectiveQty, $effectiveUnit),
                ];
            }
        }

        // Hitung selisih stok gudang, defisit/surplus, dan status ketersediaan
        foreach ($ingredientsMap as $id => $item) {
            $required = round((float) $item['required_quantity'], 3);
            $currentStock = $item['current_stock'] !== null ? round((float) $item['current_stock'], 3) : null;
            $minStock = round((float) $item['minimum_stock'], 3);
            $unit = $item['unit'];
            $stockUnit = $item['stock_unit'];
            $hasMismatch = $item['has_unit_mismatch'];

            $isStockAvailable = ($currentStock !== null && ! $hasMismatch);

            if ($isStockAvailable) {
                // 3. Proyeksi stok akhir = stok aktual - kebutuhan total
                $projectedEndingStock = round($currentStock - $required, 3);

                // 4. Kekurangan kebutuhan produksi = max(kebutuhan total - stok aktual, 0)
                $productionShortage = round(max($required - $currentStock, 0.0), 3);

                // 5. Sisa stok akhir terhadap minimum_stock
                $endingStockVsMinimum = round($projectedEndingStock - $minStock, 3);

                // Status ketersediaan kebutuhan produksi
                if ($currentStock < $required) {
                    // Kondisi 1: Kebutuhan tidak terpenuhi (stok kurang)
                    $availabilityStatus = 'shortage';
                    $availabilityLabel = 'Kurang';
                    $availabilityBadge = 'bg-rose-50 text-rose-800 border-rose-200';

                    $endingStockStatus = 'deficit';
                    $endingStockLabel = 'Defisit';
                    $endingStockBadge = 'bg-rose-50 text-rose-800 border-rose-200';

                    $readinessStatus = 'procurement_needed';
                    $readinessLabel = 'Perlu Pengadaan';
                    $readinessBadge = 'bg-rose-100 text-rose-800';

                    $isSufficient = false;
                } else {
                    // Kebutuhan produksi terpenuhi (currentStock >= required)
                    $availabilityStatus = 'fulfilled';
                    $availabilityLabel = 'Terpenuhi';
                    $availabilityBadge = 'bg-emerald-50 text-emerald-800 border-emerald-200';
                    $isSufficient = true;

                    // Evaluasi proyeksi stok akhir terhadap batas minimum
                    if ($projectedEndingStock < $minStock) {
                        // Kondisi 2: Kebutuhan terpenuhi tetapi proyeksi stok akhir di bawah batas minimum
                        $endingStockStatus = 'below_minimum';
                        $endingStockLabel = 'Di Bawah Minimum';
                        $endingStockBadge = 'bg-amber-50 text-amber-800 border-amber-200';

                        $readinessStatus = 'restock_needed';
                        $readinessLabel = 'Kebutuhan produksi terpenuhi, tetapi restock diperlukan';
                        $readinessBadge = 'bg-amber-100 text-amber-800';
                    } elseif ($projectedEndingStock == $minStock) {
                        // Kondisi 4: Kebutuhan terpenuhi tetapi proyeksi stok akhir tepat di batas minimum (kritis)
                        $endingStockStatus = 'at_minimum';
                        $endingStockLabel = 'Tepat di Batas Minimum';
                        $endingStockBadge = 'bg-amber-50 text-amber-800 border-amber-200';

                        $readinessStatus = 'critical_minimum';
                        $readinessLabel = 'Kebutuhan terpenuhi, stok kritis (batas minimum)';
                        $readinessBadge = 'bg-amber-100 text-amber-800';
                    } else {
                        // Kondisi 3: Kebutuhan terpenuhi dan proyeksi stok akhir di atas minimum (Aman)
                        $endingStockStatus = 'safe';
                        $endingStockLabel = 'Aman';
                        $endingStockBadge = 'bg-emerald-50 text-emerald-800 border-emerald-200';

                        $readinessStatus = 'ready';
                        $readinessLabel = 'Siap Produksi';
                        $readinessBadge = 'bg-emerald-100 text-emerald-800';
                    }
                }

                $formattedCurrentStock = $this->unitConverter->formatQuantity($currentStock, $stockUnit);
                $formattedProjected = ($projectedEndingStock >= 0 ? '+' : '').$this->unitConverter->formatQuantity($projectedEndingStock, $unit);
                $formattedShortage = $productionShortage > 0 ? $this->unitConverter->formatQuantity($productionShortage, $unit) : '0 '.$unit;
                $formattedSurplus = $projectedEndingStock >= 0 ? $this->unitConverter->formatQuantity($projectedEndingStock, $unit) : '0 '.$unit;
                $formattedDifference = ($projectedEndingStock >= 0 ? '+' : '').$this->unitConverter->formatQuantity($projectedEndingStock, $unit);
            } else {
                // Data stok tidak tersedia di database atau satuan resep tidak kompatibel
                $projectedEndingStock = null;
                $productionShortage = null;
                $endingStockVsMinimum = null;
                $isSufficient = null;

                $availabilityStatus = 'unverified';
                $availabilityLabel = 'Data Tidak Tersedia';
                $availabilityBadge = 'bg-gray-100 text-gray-700 border-gray-200';

                $endingStockStatus = 'unverified';
                $endingStockLabel = 'Perlu Verifikasi';
                $endingStockBadge = 'bg-gray-100 text-gray-700 border-gray-200';

                $readinessStatus = 'unverified';
                $readinessLabel = $hasMismatch ? 'Perlu Verifikasi Satuan' : 'Perlu Verifikasi Data Stok';
                $readinessBadge = 'bg-gray-100 text-gray-700';

                $formattedCurrentStock = $hasMismatch ? 'Satuan Tidak Cocok' : 'Data Tidak Tersedia';
                $formattedProjected = '-';
                $formattedShortage = '-';
                $formattedSurplus = '-';
                $formattedDifference = '-';
            }

            $ingredientsMap[$id]['required_quantity'] = $required;
            $ingredientsMap[$id]['current_stock'] = $currentStock;
            $ingredientsMap[$id]['projected_ending_stock'] = $projectedEndingStock;
            $ingredientsMap[$id]['production_shortage'] = $productionShortage;
            $ingredientsMap[$id]['ending_stock_vs_minimum'] = $endingStockVsMinimum;

            // Backward compatibility aliases
            $ingredientsMap[$id]['difference'] = $projectedEndingStock;
            $ingredientsMap[$id]['shortage_quantity'] = $productionShortage ?? 0.0;
            $ingredientsMap[$id]['surplus_quantity'] = ($projectedEndingStock !== null && $projectedEndingStock >= 0) ? $projectedEndingStock : 0.0;
            $ingredientsMap[$id]['status'] = ($availabilityStatus === 'shortage') ? 'shortage' : (($availabilityStatus === 'fulfilled') ? 'sufficient' : 'unverified');
            $ingredientsMap[$id]['status_label'] = $availabilityLabel;

            // Atribut status presisi
            $ingredientsMap[$id]['availability_status'] = $availabilityStatus;
            $ingredientsMap[$id]['availability_label'] = $availabilityLabel;
            $ingredientsMap[$id]['availability_badge'] = $availabilityBadge;

            $ingredientsMap[$id]['ending_stock_status'] = $endingStockStatus;
            $ingredientsMap[$id]['ending_stock_label'] = $endingStockLabel;
            $ingredientsMap[$id]['ending_stock_badge'] = $endingStockBadge;

            $ingredientsMap[$id]['readiness_status'] = $readinessStatus;
            $ingredientsMap[$id]['readiness_label'] = $readinessLabel;
            $ingredientsMap[$id]['readiness_badge'] = $readinessBadge;

            $ingredientsMap[$id]['is_sufficient'] = $isSufficient;
            $ingredientsMap[$id]['is_stock_available'] = $isStockAvailable;
            $ingredientsMap[$id]['is_low_stock_after_production'] = in_array($endingStockStatus, ['below_minimum', 'at_minimum', 'deficit'], true);

            // Formatted string helpers
            $ingredientsMap[$id]['formatted_required'] = $this->unitConverter->formatQuantity($required, $unit);
            $ingredientsMap[$id]['formatted_current_stock'] = $formattedCurrentStock;
            $ingredientsMap[$id]['formatted_minimum_stock'] = $this->unitConverter->formatQuantity($minStock, $stockUnit);
            $ingredientsMap[$id]['formatted_projected_ending_stock'] = $formattedProjected;
            $ingredientsMap[$id]['formatted_production_shortage'] = $formattedShortage;
            $ingredientsMap[$id]['formatted_difference'] = $formattedDifference;
            $ingredientsMap[$id]['formatted_shortage'] = $formattedShortage;
            $ingredientsMap[$id]['formatted_surplus'] = $formattedSurplus;
        }

        // Urutkan: Prioritas 1 Bahan kurang (Perlu Pengadaan), 2 Restock (<= min), 3 Data tidak tersedia, 4 Aman
        $sortedItems = collect($ingredientsMap)
            ->sortBy([
                function ($a, $b) {
                    $order = [
                        'shortage' => 1,
                        'unverified' => 2,
                        'at_minimum' => 3,
                        'below_minimum' => 4,
                        'safe' => 5,
                    ];
                    $rankA = $order[$a['ending_stock_status']] ?? 99;
                    $rankB = $order[$b['ending_stock_status']] ?? 99;

                    return $rankA <=> $rankB;
                },
                ['ingredient_name', 'asc'],
            ])
            ->values()
            ->all();

        $totalIngredients = count($sortedItems);
        $shortageCount = collect($sortedItems)->where('availability_status', 'shortage')->count();
        $fulfilledCount = collect($sortedItems)->where('availability_status', 'fulfilled')->count();
        $unverifiedCount = collect($sortedItems)->where('availability_status', 'unverified')->count();
        $belowMinimumCount = collect($sortedItems)->whereIn('ending_stock_status', ['below_minimum', 'at_minimum'])->count();
        $safeCount = collect($sortedItems)->where('ending_stock_status', 'safe')->count();

        // Status kesiapan produksi keseluruhan
        if ($shortageCount > 0) {
            $overallReadiness = [
                'status' => 'procurement_needed',
                'label' => 'Perlu Pengadaan',
                'badge_color' => 'red',
                'description' => "Terdapat {$shortageCount} bahan baku yang stok fisiknya tidak mencukupi untuk produksi.",
            ];
        } elseif ($unverifiedCount > 0) {
            $overallReadiness = [
                'status' => 'unverified',
                'label' => 'Perlu Verifikasi',
                'badge_color' => 'gray',
                'description' => "Terdapat {$unverifiedCount} bahan baku yang data persediaannya belum diverifikasi.",
            ];
        } else {
            $overallReadiness = [
                'status' => 'ready',
                'label' => 'Siap Produksi',
                'badge_color' => 'green',
                'description' => 'Seluruh kebutuhan bahan baku masakan terpenuhi oleh stok gudang.',
            ];
        }

        // Kondisi persediaan akhir keseluruhan (dipisahkan secara tegas dari kesiapan produksi)
        if ($shortageCount > 0) {
            $overallInventory = [
                'status' => 'deficit',
                'label' => 'Defisit Stok',
                'badge_color' => 'red',
                'description' => "Stok gudang mengalami defisit pada {$shortageCount} jenis bahan.",
            ];
        } elseif ($belowMinimumCount > 0) {
            $overallInventory = [
                'status' => 'restock_needed',
                'label' => 'Restock Diperlukan',
                'badge_color' => 'yellow',
                'description' => "Kebutuhan produksi terpenuhi, tetapi {$belowMinimumCount} bahan akan berada pada/di bawah batas minimum.",
            ];
        } elseif ($unverifiedCount > 0) {
            $overallInventory = [
                'status' => 'unverified',
                'label' => 'Perlu Verifikasi',
                'badge_color' => 'gray',
                'description' => 'Sebagian data stok akhir belum dapat diproyeksikan.',
            ];
        } else {
            $overallInventory = [
                'status' => 'safe',
                'label' => 'Persediaan Aman',
                'badge_color' => 'green',
                'description' => 'Seluruh sisa persediaan pasca produksi aman di atas batas minimum.',
            ];
        }

        return [
            'items' => $sortedItems,
            'summary' => [
                'total_ingredients' => $totalIngredients,
                'fulfilled_count' => $fulfilledCount,
                'sufficient_count' => $fulfilledCount, // alias kompatibilitas
                'shortage_count' => $shortageCount,
                'below_minimum_count' => $belowMinimumCount,
                'safe_count' => $safeCount,
                'unverified_count' => $unverifiedCount,
                'has_shortage' => $shortageCount > 0,
                'has_restock_warning' => $belowMinimumCount > 0,
                'total_portions' => $totalPortions,
                'total_menus' => count($entries),
                'missing_recipes_count' => count($missingRecipes),
                'production_readiness' => $overallReadiness,
                'inventory_condition' => $overallInventory,
            ],
            'missing_recipes' => $missingRecipes,
        ];
    }
}
