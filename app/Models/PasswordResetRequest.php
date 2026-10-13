<?php

namespace App\Models;

use App\Enums\PasswordResetStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordResetRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'email',
        'status',
        'processed_by',
        'admin_notes',
        'processed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PasswordResetStatus::class,
            'processed_at' => 'datetime',
        ];
    }

    /**
     * User yang meminta reset password.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Admin yang memproses permohonan.
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function isPending(): bool
    {
        return $this->status === PasswordResetStatus::Diajukan;
    }

    public function isCompleted(): bool
    {
        return $this->status === PasswordResetStatus::Selesai;
    }

    public function isRejected(): bool
    {
        return $this->status === PasswordResetStatus::Gagal;
    }
}
