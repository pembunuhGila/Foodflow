<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductIngredient extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_ingredients';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'ingredient_id',
        'quantity_per_unit',
        'unit',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity_per_unit' => 'decimal:3',
        ];
    }

    /**
     * Menu produk yang memiliki resep ini.
     *
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Bahan baku yang digunakan dalam resep ini.
     *
     * @return BelongsTo<Ingredient, $this>
     */
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    /**
     * Format jumlah takaran dan satuannya dengan rapi.
     */
    public function formattedQuantity(): string
    {
        $amount = rtrim(rtrim(number_format((float) $this->quantity_per_unit, 3, ',', '.'), '0'), ',');

        return "{$amount} {$this->unit}";
    }
}
