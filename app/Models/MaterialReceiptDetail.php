<?php

namespace App\Models;

use App\Enums\MaterialCondition;
use Database\Factories\MaterialReceiptDetailFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialReceiptDetail extends Model
{
    /** @use HasFactory<MaterialReceiptDetailFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $table = 'material_receipt_details';

    protected $fillable = [
        'material_receipt_id',
        'ingredient_id',
        'quantity',
        'unit',
        'condition',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'condition' => MaterialCondition::class,
        ];
    }

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(MaterialReceipt::class, 'material_receipt_id');
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id');
    }
}
