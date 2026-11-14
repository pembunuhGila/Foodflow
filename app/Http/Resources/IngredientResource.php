<?php

namespace App\Http\Resources;

use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Ingredient
 */
class IngredientResource extends JsonResource
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
            'category' => $this->category ?? 'Umum',
            'unit' => $this->unit,
            'current_stock' => (float) $this->current_stock,
            'minimum_stock' => (float) $this->minimum_stock,
            'is_low_stock' => $this->current_stock <= $this->minimum_stock,
            'price_per_unit' => $this->price_per_unit ? (float) $this->price_per_unit : null,
            'notes' => $this->notes,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
