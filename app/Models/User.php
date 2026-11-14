<?php

namespace App\Models;

use App\Enums\UserRole;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'google_id', 'avatar', 'password', 'role', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    use SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => UserStatus::class,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isGudang(): bool
    {
        return $this->role === UserRole::Gudang;
    }

    public function isProduksi(): bool
    {
        return $this->role === UserRole::Produksi;
    }

    public function isOwner(): bool
    {
        return $this->role === UserRole::Owner;
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::Aktif;
    }

    /**
     * The model's default values for attributes.
     * Default role sengaja tidak di-set agar tidak ada user yang
     * mendapatkan akses Admin secara tidak sengaja. Role wajib
     * di-set secara eksplisit saat membuat user baru.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => UserStatus::Aktif,
    ];

    /**
     * Dapatkan rute dashboard sesuai role user.
     */
    public function getDashboardRoute(): string
    {
        return match ($this->role) {
            UserRole::Gudang => 'gudang.dashboard',
            UserRole::Produksi => 'produksi.dashboard',
            UserRole::Owner => 'owner.dashboard',
            default => 'admin.dashboard',
        };
    }

    /**
     * Permintaan reset password yang diajukan oleh pengguna ini.
     *
     * @return HasMany<PasswordResetRequest, $this>
     */
    public function passwordResetRequests(): HasMany
    {
        return $this->hasMany(PasswordResetRequest::class);
    }
}
