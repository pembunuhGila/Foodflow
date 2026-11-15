<?php

namespace App\Http\Resources;

use App\Models\CateringOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CateringOrder
 */
class CateringOrderResource extends JsonResource
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
            'order_number' => $this->order_number,
            'order_date' => $this->order_date?->format('Y-m-d'),
            'delivery_date' => $this->delivery_date?->format('Y-m-d'),
            'status' => $this->status,
            'status_label' => $this->status_label,
            'total_amount' => (float) $this->total_amount,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
            'customer' => $this->whenLoaded('customer', function () {
                return [
                    'id' => $this->customer->id,
                    'name' => $this->customer->name,
                    'phone' => $this->customer->phone,
                    'email' => $this->customer->email,
                    'address' => $this->customer->address,
                ];
            }),
            'creator' => $this->whenLoaded('creator', function () {
                return [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                    'email' => $this->creator->email,
                    'role' => $this->creator->role?->value ?? (string) $this->creator->role,
                ];
            }),
            'items' => CateringOrderDetailResource::collection($this->whenLoaded('details')),
        ];
    }
}
