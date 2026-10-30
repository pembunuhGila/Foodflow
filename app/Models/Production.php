<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Production extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'productions';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'production_number',
        'production_plan_detail_id',
        'created_by',
        'production_date',
        'actual_quantity',
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
            'production_date' => 'date',
            'actual_quantity' => 'integer',
        ];
    }

    /**
     * Detail rencana produksi yang direalisasikan.
     *
     * @return BelongsTo<ProductionPlanDetail, $this>
     */
    public function planDetail(): BelongsTo
    {
        return $this->belongsTo(ProductionPlanDetail::class, 'production_plan_detail_id');
    }

    /**
     * Pengguna yang mencatat realisasi produksi.
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Memeriksa apakah realisasi masakan ini sudah berstatus selesai/memenuhi rencana.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Memeriksa apakah realisasi masakan ini masih parsial/belum memenuhi rencana.
     */
    public function isPartial(): bool
    {
        return $this->status === 'partial';
    }

    /**
     * Menghasilkan nomor realisasi produksi baru secara otomatis dengan format: PRD-YYYYMMDD-XXXX.
     */
    public static function generateProductionNumber(?Carbon $date = null): string
    {
        $date = $date ?? Carbon::now();
        $datePrefix = 'PRD-'.$date->format('Ymd').'-';

        $lastProduction = static::query()
            ->where('production_number', 'LIKE', $datePrefix.'%')
            ->orderByDesc('id')
            ->first();

        if (! $lastProduction) {
            return $datePrefix.'0001';
        }

        $lastSeq = (int) substr((string) $lastProduction->production_number, -4);
        $nextSeq = str_pad((string) ($lastSeq + 1), 4, '0', STR_PAD_LEFT);

        return $datePrefix.$nextSeq;
    }
}
