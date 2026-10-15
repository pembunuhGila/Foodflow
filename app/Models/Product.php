<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'unit',
        'selling_price',
        'is_active',
        'calories',
        'protein',
        'fat',
        'carbohydrates',
        'dietary_fiber',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'selling_price' => 'decimal:2',
            'calories' => 'decimal:2',
            'protein' => 'decimal:2',
            'fat' => 'decimal:2',
            'carbohydrates' => 'decimal:2',
            'dietary_fiber' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope query to only include active products.
     *
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if the product is active.
     */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * Get formatted Indonesian rupiah price.
     */
    public function formattedPrice(): string
    {
        return 'Rp '.number_format((float) $this->selling_price, 0, ',', '.');
    }

    /**
     * Calculate total macronutrients in grams (protein + fat + carbohydrates).
     */
    public function totalMacroNutrients(): float
    {
        return (float) $this->protein + (float) $this->fat + (float) $this->carbohydrates;
    }

    /**
     * Detail rincian komposisi resep bahan baku.
     *
     * @return HasMany<ProductIngredient, $this>
     */
    public function recipeItems(): HasMany
    {
        return $this->hasMany(ProductIngredient::class);
    }

    /**
     * Cek apakah menu sudah memiliki komposisi resep bahan baku.
     */
    public function hasRecipe(): bool
    {
        return $this->recipeItems()->exists();
    }

    /**
     * Bahan baku yang terhubung dengan menu ini melalui resep.
     *
     * @return BelongsToMany<Ingredient, $this>
     */
    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'product_ingredients')
            ->withPivot(['id', 'quantity_per_unit', 'unit'])
            ->withTimestamps();
    }

    /**
     * Rincian rencana produksi yang memuat menu ini.
     *
     * @return HasMany<ProductionPlanDetail, $this>
     */
    public function productionPlanDetails(): HasMany
    {
        return $this->hasMany(ProductionPlanDetail::class, 'product_id');
    }

    /**
     * Rencana produksi yang memuat menu ini.
     *
     * @return BelongsToMany<ProductionPlan, $this>
     */
    public function productionPlans(): BelongsToMany
    {
        return $this->belongsToMany(ProductionPlan::class, 'production_plan_details', 'product_id', 'production_plan_id')
            ->withPivot(['id', 'planned_quantity', 'notes'])
            ->withTimestamps();
    }
}
