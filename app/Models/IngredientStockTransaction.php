<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IngredientStockTransaction extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ingredient_stock_transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ingredient_id',
        'transaction_type',
        'quantity',
        'transaction_date',
        'reference_type',
        'reference_id',
        'notes',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'transaction_date' => 'date',
        ];
    }

    /**
     * Bahan baku yang terpengaruh transaksi.
     *
     * @return BelongsTo<Ingredient, $this>
     */
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id');
    }

    /**
     * Pengguna yang mencatat transaksi.
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Permintaan bahan jika transaksi ini berasal dari material request.
     *
     * @return BelongsTo<MaterialRequest, $this>
     */
    public function materialRequest(): BelongsTo
    {
        return $this->belongsTo(MaterialRequest::class, 'reference_id');
    }
}
