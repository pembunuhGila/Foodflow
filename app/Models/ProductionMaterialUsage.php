<?php

namespace App\Models;

use Database\Factories\ProductionMaterialUsageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionMaterialUsage extends Model
{
    /** @use HasFactory<ProductionMaterialUsageFactory> */
    use HasFactory;

    protected $table = 'production_material_usages';

    protected $fillable = [
        'production_plan_id',
        'ingredient_id',
        'planned_quantity',
        'actual_quantity',
        'difference_quantity',
        'unit',
        'notes',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'planned_quantity' => 'decimal:3',
            'actual_quantity' => 'decimal:3',
            'difference_quantity' => 'decimal:3',
        ];
    }

    public function productionPlan(): BelongsTo
    {
        return $this->belongsTo(ProductionPlan::class, 'production_plan_id');
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
