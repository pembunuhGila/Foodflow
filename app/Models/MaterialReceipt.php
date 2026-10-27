<?php

namespace App\Models;

use Database\Factories\MaterialReceiptFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class MaterialReceipt extends Model
{
    /** @use HasFactory<MaterialReceiptFactory> */
    use HasFactory;

    protected $table = 'material_receipts';

    protected $fillable = [
        'purchase_order_id',
        'received_by',
        'receipt_number',
        'receipt_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'receipt_date' => 'date',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(MaterialReceiptDetail::class, 'material_receipt_id');
    }

    /**
     * Menghasilkan nomor tanda terima penerimaan bahan: RC-YYYYMMDD-XXXX.
     */
    public static function generateReceiptNumber(?Carbon $date = null): string
    {
        $date = $date ?? Carbon::now();
        $datePrefix = 'RC-'.$date->format('Ymd').'-';

        $last = static::query()
            ->where('receipt_number', 'LIKE', $datePrefix.'%')
            ->orderByDesc('id')
            ->first();

        if (! $last) {
            return $datePrefix.'0001';
        }

        $lastSeq = (int) substr($last->receipt_number, -4);
        $nextSeq = str_pad((string) ($lastSeq + 1), 4, '0', STR_PAD_LEFT);

        return $datePrefix.$nextSeq;
    }
}
