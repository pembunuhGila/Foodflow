<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CateringOrderDetail extends Model
{
    use HasFactory;

    protected $table = 'catering_order_details';

    protected $fillable = [
        'catering_order_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    /**
     * The catering order this detail belongs to.
     */
    public function cateringOrder(): BelongsTo
    {
        return $this->belongsTo(CateringOrder::class, 'catering_order_id');
    }

    /**
     * Alias for cateringOrder.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(CateringOrder::class, 'catering_order_id');
    }

    /**
     * The product associated with this detail.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
