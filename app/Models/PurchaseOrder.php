<?php

namespace App\Models;

use App\Enums\PurchaseOrderStatus;
use Database\Factories\PurchaseOrderFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class PurchaseOrder extends Model
{
    /** @use HasFactory<PurchaseOrderFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'purchase_orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'vendor_id',
        'created_by',
        'order_number',
        'order_date',
        'expected_date',
        'status',
        'notes',
    ];

    protected $dateFormat = 'Y-m-d';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        // Ensure dates are stored without time component in SQLite/in-memory DB

        return [
            'order_date' => 'date',
            'expected_date' => 'date',
            'status' => PurchaseOrderStatus::class,
        ];
    }

    /**
     * Vendor/supplier tempat PO ini dikirimkan.
     *
     * @return BelongsTo<Supplier, $this>
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'vendor_id');
    }

    /**
     * Pengguna yang membuat purchase order ini.
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Rincian bahan baku yang dipesan dalam PO ini.
     *
     * @return HasMany<PurchaseOrderDetail, $this>
     */
    public function details(): HasMany
    {
        return $this->hasMany(PurchaseOrderDetail::class, 'purchase_order_id');
    }

    /**
     * Menghitung grand total semua item dalam PO ini.
     */
    public function grandTotal(): float
    {
        return (float) $this->details->sum('subtotal');
    }

    /**
     * Scope filter berdasarkan status PO.
     *
     * @param  Builder<PurchaseOrder>  $query
     * @return Builder<PurchaseOrder>
     */
    public function scopeStatus(Builder $query, string|PurchaseOrderStatus|null $status): Builder
    {
        if (empty($status)) {
            return $query;
        }

        $value = $status instanceof PurchaseOrderStatus ? $status->value : $status;

        return $query->where('status', $value);
    }

    /**
     * Scope filter berdasarkan tanggal pesan.
     *
     * @param  Builder<PurchaseOrder>  $query
     * @return Builder<PurchaseOrder>
     */
    public function scopeOrderDate(Builder $query, ?string $date): Builder
    {
        if (empty($date)) {
            return $query;
        }

        return $query->whereDate('order_date', $date);
    }

    /**
     * Menghasilkan nomor PO baru otomatis dengan format: PO-YYYYMMDD-XXXX.
     */
    public static function generateOrderNumber(?Carbon $date = null): string
    {
        $date = $date ?? Carbon::now();
        $datePrefix = 'PO-'.$date->format('Ymd').'-';

        $lastOrder = static::query()
            ->where('order_number', 'LIKE', $datePrefix.'%')
            ->orderByDesc('id')
            ->first();

        if (! $lastOrder) {
            return $datePrefix.'0001';
        }

        $lastSeq = (int) substr($lastOrder->order_number, -4);
        $nextSeq = str_pad((string) ($lastSeq + 1), 4, '0', STR_PAD_LEFT);

        return $datePrefix.$nextSeq;
    }
}
