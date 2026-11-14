<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Product
 */
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array (Materi Pertemuan 10 - Langkah 3).
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category ?? 'Menu Utama',
            'unit' => $this->unit,
            'price' => (float) $this->price,
            'nutrition' => [
                'calories' => (float) $this->calories,
                'protein' => (float) $this->protein,
                'fat' => (float) $this->fat,
                'carbohydrates' => (float) $this->carbohydrates,
                'dietary_fiber' => (float) $this->dietary_fiber,
            ],
            'is_active' => (bool) $this->is_active,
            'recipe_items' => $this->whenLoaded('recipeItems', function () {
                return $this->recipeItems->map(fn ($item) => [
                    'ingredient_id' => $item->ingredient_id,
                    'ingredient_name' => $item->ingredient?->name,
                    'quantity_per_unit' => (float) $item->quantity_per_unit,
                    'unit' => $item->unit,
                ]);
            }),
        ];
    }
}
