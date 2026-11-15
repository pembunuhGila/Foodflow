<?php

namespace App\Http\Resources;

use App\Models\CateringOrderDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CateringOrderDetail
 */
class CateringOrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array (Materi Pertemuan 10 - Langkah 3 & 4).
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $this->product?->name,
            'quantity' => (int) $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'subtotal' => (float) $this->subtotal,
            'notes' => $this->notes,
        ];
    }
}
