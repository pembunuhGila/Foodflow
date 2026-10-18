<?php

namespace App\Models;

use Database\Factories\MaterialRequestFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaterialRequest extends Model
{
    /** @use HasFactory<MaterialRequestFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'material_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'production_plan_id',
        'requested_by',
        'verified_by',
        'request_date',
        'status',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'request_date' => 'date',
        ];
    }

    /**
     * Relasi ke rencana produksi terkait.
     *
     * @return BelongsTo<ProductionPlan, $this>
     */
    public function productionPlan(): BelongsTo
    {
        return $this->belongsTo(ProductionPlan::class, 'production_plan_id');
    }

    /**
     * Pengguna yang mengajukan permintaan bahan.
     *
     * @return BelongsTo<User, $this>
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Petugas gudang / admin yang memverifikasi permintaan bahan.
     *
     * @return BelongsTo<User, $this>
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Rincian bahan baku yang diminta.
     *
     * @return HasMany<MaterialRequestDetail, $this>
     */
    public function details(): HasMany
    {
        return $this->hasMany(MaterialRequestDetail::class, 'material_request_id');
    }

    /**
     * Transaksi kartu stok keluar terkait permintaan ini.
     *
     * @return HasMany<IngredientStockTransaction, $this>
     */
    public function stockTransactions(): HasMany
    {
        return $this->hasMany(IngredientStockTransaction::class, 'reference_id')
            ->where('reference_type', 'material_request');
    }

    /**
     * Cek apakah status permintaan masih pending.
     */
    public function isPending(): bool
    {
        return strcasecmp((string) $this->status, 'pending') === 0;
    }

    /**
     * Cek apakah permintaan sudah disetujui / diverifikasi.
     */
    public function isApproved(): bool
    {
        return strcasecmp((string) $this->status, 'approved') === 0;
    }

    /**
     * Scope filter untuk permintaan berstatus pending.
     *
     * @param  Builder<MaterialRequest>  $query
     * @return Builder<MaterialRequest>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('status', 'pending')
                ->orWhere('status', 'Pending');
        });
    }

    /**
     * Scope filter untuk permintaan berstatus approved.
     *
     * @param  Builder<MaterialRequest>  $query
     * @return Builder<MaterialRequest>
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('status', 'approved')
                ->orWhere('status', 'Approved');
        });
    }
}
