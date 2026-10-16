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
        'current_stock',
        'minimum_stock',
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
            'current_stock' => 'decimal:2',
            'minimum_stock' => 'decimal:2',
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
     * Scope query to ingredients where current_stock is below or equal to minimum_stock (BR-06).
     */
    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('current_stock', '<=', 'minimum_stock');
    }

    /**
     * Scope alias for low stock ingredients.
     */
    public function scopeUnderMinimumStock(Builder $query): Builder
    {
        return $this->scopeLowStock($query);
    }

    /**
     * Scope alias for ingredients needing restock.
     */
    public function scopeNeedsRestock(Builder $query): Builder
    {
        return $this->scopeLowStock($query);
    }

    /**
     * Scope query to ingredients that are completely out of stock (current_stock <= 0).
     */
    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->where('current_stock', '<=', 0);
    }

    /**
     * Scope query to ingredients with sufficient stock (> minimum_stock).
     */
    public function scopeSufficientStock(Builder $query): Builder
    {
        return $query->whereColumn('current_stock', '>', 'minimum_stock');
    }

    /**
     * Determine if the ingredient is active.
     */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * Check if current stock (or provided stock) is below or equal to the minimum stock threshold (BR-06).
     */
    public function isLowStock(float|int|string|null $currentStock = null): bool
    {
        $stock = $currentStock !== null ? (float) $currentStock : (float) ($this->current_stock ?? 0);

        return $stock <= (float) $this->minimum_stock;
    }

    /**
     * Check if the ingredient is at or below its minimum stock threshold (alias for isLowStock).
     */
    public function isUnderMinimumStock(float|int|string|null $currentStock = null): bool
    {
        return $this->isLowStock($currentStock);
    }

    /**
     * Check if the ingredient is completely out of stock.
     */
    public function isOutOfStock(float|int|string|null $currentStock = null): bool
    {
        $stock = $currentStock !== null ? (float) $currentStock : (float) ($this->current_stock ?? 0);

        return $stock <= 0;
    }

    /**
     * Check if the ingredient needs restock (alias for isLowStock).
     */
    public function needsRestock(float|int|string|null $currentStock = null): bool
    {
        return $this->isLowStock($currentStock);
    }

    /**
     * Calculate stock deficit below minimum stock threshold.
     */
    public function getStockDeficit(float|int|string|null $currentStock = null): float
    {
        $stock = $currentStock !== null ? (float) $currentStock : (float) ($this->current_stock ?? 0);

        return max(0.0, (float) $this->minimum_stock - $stock);
    }

    /**
     * Get stock status level: 'out_of_stock', 'low_stock', or 'sufficient'.
     */
    public function getStockStatusLevel(float|int|string|null $currentStock = null): string
    {
        $stock = $currentStock !== null ? (float) $currentStock : (float) ($this->current_stock ?? 0);

        if ($stock <= 0) {
            return 'out_of_stock';
        }

        if ($stock <= (float) $this->minimum_stock) {
            return 'low_stock';
        }

        return 'sufficient';
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
}
