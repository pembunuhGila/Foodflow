<?php

namespace App\Models;

use App\Enums\ProductionPlanStatus;
use App\Services\ProductionIngredientCalculator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class ProductionPlan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'production_plans';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'plan_number',
        'catering_order_id',
        'plan_date',
        'production_date',
        'status',
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
            'plan_date' => 'date',
            'production_date' => 'date',
            'status' => ProductionPlanStatus::class,
        ];
    }

    /**
     * Pengguna yang membuat rencana produksi.
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Rincian menu hidangan yang direncanakan.
     *
     * @return HasMany<ProductionPlanDetail, $this>
     */
    public function details(): HasMany
    {
        return $this->hasMany(ProductionPlanDetail::class, 'production_plan_id');
    }

    /**
     * Relasi many-to-many ke produk menu melalui tabel detail rencana produksi.
     *
     * @return BelongsToMany<Product, $this>
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'production_plan_details', 'production_plan_id', 'product_id')
            ->withPivot(['id', 'planned_quantity', 'notes'])
            ->withTimestamps();
    }

    /**
     * Menghitung total porsi yang direncanakan dalam rencana produksi ini.
     */
    public function totalPlannedQuantity(): int
    {
        if (isset($this->total_portions)) {
            return (int) $this->total_portions;
        }

        return (int) $this->details->sum('planned_quantity');
    }

    /**
     * Menghitung kalkulasi otomatis seluruh kebutuhan bahan baku dan ketersediaan stok gudang.
     *
     * @return array{
     *     items: list<array<string, mixed>>,
     *     summary: array<string, mixed>,
     *     missing_recipes: list<Product>
     * }
     */
    public function calculateIngredientRequirements(): array
    {
        return app(ProductionIngredientCalculator::class)->calculateForPlan($this);
    }

    /**
     * Scope filter status rencana produksi.
     *
     * @param  Builder<ProductionPlan>  $query
     * @return Builder<ProductionPlan>
     */
    public function scopeStatus(Builder $query, string|ProductionPlanStatus|null $status): Builder
    {
        if (empty($status)) {
            return $query;
        }

        $value = $status instanceof ProductionPlanStatus ? $status->value : $status;

        return $query->where('status', $value);
    }

    /**
     * Scope filter tanggal produksi.
     *
     * @param  Builder<ProductionPlan>  $query
     * @return Builder<ProductionPlan>
     */
    public function scopeProductionDate(Builder $query, ?string $date): Builder
    {
        if (empty($date)) {
            return $query;
        }

        return $query->whereDate('production_date', $date);
    }

    /**
     * Menghasilkan nomor rencana produksi baru otomatis dengan format: PLN-YYYYMMDD-XXXX.
     */
    public static function generatePlanNumber(?Carbon $date = null): string
    {
        $date = $date ?? Carbon::now();
        $datePrefix = 'PLN-'.$date->format('Ymd').'-';

        $lastPlan = static::query()
            ->where('plan_number', 'LIKE', $datePrefix.'%')
            ->orderByDesc('id')
            ->first();

        if (! $lastPlan) {
            return $datePrefix.'0001';
        }

        $lastSeq = (int) substr($lastPlan->plan_number, -4);
        $nextSeq = str_pad((string) ($lastSeq + 1), 4, '0', STR_PAD_LEFT);

        return $datePrefix.$nextSeq;
    }
}
