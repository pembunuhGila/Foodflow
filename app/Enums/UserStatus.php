<?php

namespace App\Enums;

enum UserStatus: string
{
    case Aktif = 'aktif';
    case TidakAktif = 'tidak_aktif';

    public function label(): string
    {
        return match ($this) {
            self::Aktif => 'Aktif',
            self::TidakAktif => 'Tidak Aktif',
        };
    }
}
