<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CateringOrder extends Model
{
    use HasFactory;

    protected $table = 'catering_orders';

    protected $fillable = [
        'order_number',
        'customer_id',
        'created_by',
        'order_date',
        'delivery_date',
        'status',
        'total_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'delivery_date' => 'date',
            'total_amount' => 'decimal:2',
        ];
    }

    /**
     * Alias for delivery_date to support required_date from Use Case spec.
     */
    public function getRequiredDateAttribute()
    {
        return $this->delivery_date;
    }

    /**
     * Mutator for required_date alias.
     */
    public function setRequiredDateAttribute($value): void
    {
        $this->attributes['delivery_date'] = $value;
    }

    /**
     * Boot method to generate a unique order number on creation.
     */
    protected static function booted(): void
    {
        static::creating(function (self $order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }

    /**
     * Generate unique order number with format ORD-YYYYMMDD-XXXX.
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD-'.now()->format('Ymd').'-';

        $latestOrder = self::where('order_number', 'like', $prefix.'%')
            ->orderByDesc('order_number')
            ->first();

        $nextSequence = 1;
        if ($latestOrder && preg_match('/-(\d{4})$/', $latestOrder->order_number, $matches)) {
            $nextSequence = ((int) $matches[1]) + 1;
        }

        $candidate = sprintf('%s%04d', $prefix, $nextSequence);

        while (self::where('order_number', $candidate)->exists()) {
            $nextSequence++;
            $candidate = sprintf('%s%04d', $prefix, $nextSequence);
        }

        return $candidate;
    }

    /**
     * Customer relation.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Creator (user) relation.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Creator (user) relation alias.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Order details relation.
     */
    public function details(): HasMany
    {
        return $this->hasMany(CateringOrderDetail::class);
    }

    /**
     * Order details alias.
     */
    public function orderDetails(): HasMany
    {
        return $this->hasMany(CateringOrderDetail::class);
    }

    /**
     * Format total_amount as Indonesian Rupiah.
     */
    public function formattedTotal(): string
    {
        return 'Rp '.number_format((float) $this->total_amount, 0, ',', '.');
    }

    /**
     * Dapatkan label status bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'confirmed' => 'Terkonfirmasi',
            'processing' => 'Sedang Diproses',
            'delivered' => 'Terkirim',
            'cancelled' => 'Dibatalkan',
            default => 'Menunggu Konfirmasi',
        };
    }

    /**
     * Dapatkan badge style Tailwind CSS sesuai status.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'confirmed' => 'bg-blue-100 text-blue-700 border-blue-200',
            'processing' => 'bg-purple-100 text-purple-700 border-purple-200',
            'delivered' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'cancelled' => 'bg-red-100 text-red-700 border-red-200',
            default => 'bg-amber-100 text-amber-700 border-amber-200',
        };
    }
}
