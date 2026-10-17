<?php

namespace App\Models;

use Database\Factories\IngredientFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingredient extends Model
{
    /** @use HasFactory<IngredientFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'category',
        'unit',
        'minimum_stock',
        'current_stock',
        'calories',
        'protein',
        'fat',
        'carbohydrates',
        'notes',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'minimum_stock' => 'decimal:2',
            'current_stock' => 'decimal:2',
            'calories' => 'decimal:2',
            'protein' => 'decimal:2',
            'fat' => 'decimal:2',
            'carbohydrates' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope a query to only include active ingredients.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Determine if the ingredient is active.
     */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * Check if a given stock quantity is below or equal to the minimum stock threshold.
     */
    public function isLowStock(float|int|string $currentStock): bool
    {
        return (float) $currentStock <= (float) $this->minimum_stock;
    }

    /**
     * Menu masakan yang menggunakan bahan baku ini dalam resepnya.
     *
     * @return BelongsToMany<Product, $this>
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_ingredients')
            ->withPivot(['id', 'quantity_per_unit', 'unit'])
            ->withTimestamps();
    }

    /**
     * Detail item resep terkait.
     *
     * @return HasMany<ProductIngredient, $this>
     */
    public function recipeItems(): HasMany
    {
        return $this->hasMany(ProductIngredient::class);
    }

    /**
     * Mengambil nilai stok gudang saat ini (mengembalikan null jika belum tercatat/tidak tersedia).
     */
    public function getCurrentStock(): ?float
    {
        return $this->current_stock !== null ? (float) $this->current_stock : null;
    }

    /**
     * Format nilai stok gudang dengan satuan (strip '-' jika data tidak tersedia).
     */
    public function formattedCurrentStock(): string
    {
        $stock = $this->getCurrentStock();

        if ($stock === null) {
            return '-';
        }

        $amount = rtrim(rtrim(number_format($stock, 2, ',', '.'), '0'), ',');

        return "{$amount} {$this->unit}";
    }
}
