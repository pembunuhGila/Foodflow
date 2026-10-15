<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProductRecipeRequest;
use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductRecipeController extends Controller
{
    /**
     * Show the recipe builder form for the specified product.
     */
    public function edit(Product $product): View
    {
        $product->load(['recipeItems.ingredient']);

        $availableIngredients = Ingredient::query()
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'unit', 'category']);

        $initialItems = $product->recipeItems->map(function ($item) {
            return [
                'ingredient_id' => $item->ingredient_id,
                'quantity_per_unit' => (float) $item->quantity_per_unit,
                'unit' => $item->unit,
            ];
        })->values();

        return view('products.recipe', [
            'product' => $product,
            'availableIngredients' => $availableIngredients,
            'initialItems' => $initialItems,
        ]);
    }

    /**
     * Update the recipe composition for the specified product.
     */
    public function update(UpdateProductRecipeRequest $request, Product $product): RedirectResponse
    {
        $items = $request->validated('items') ?? [];

        DB::transaction(function () use ($product, $items) {
            $syncData = [];
            foreach ($items as $item) {
                $syncData[$item['ingredient_id']] = [
                    'quantity_per_unit' => $item['quantity_per_unit'],
                    'unit' => $item['unit'],
                ];
            }

            $product->ingredients()->sync($syncData);
        });

        $count = count($items);
        $message = $count > 0
            ? "Komposisi resep untuk \"{$product->name}\" berhasil disimpan dengan {$count} jenis bahan baku."
            : "Komposisi resep untuk \"{$product->name}\" telah dikosongkan.";

        return redirect()
            ->route('products.show', $product)
            ->with('success', $message);
    }
}
