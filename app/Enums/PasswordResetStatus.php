<?php

namespace App\Enums;

enum PasswordResetStatus: string
{
    case Diajukan = 'diajukan';
    case Diproses = 'diproses';
    case Selesai = 'selesai';
    case Gagal = 'gagal';

    public function label(): string
    {
        return match ($this) {
            self::Diajukan => 'Diajukan',
            self::Diproses => 'Diproses',
            self::Selesai => 'Selesai',
            self::Gagal => 'Gagal / Ditolak',
        };
    }
}
