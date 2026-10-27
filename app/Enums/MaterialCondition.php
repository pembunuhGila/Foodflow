<?php

namespace App\Enums;

enum MaterialCondition: string
{
    case Baik = 'baik';
    case Rusak = 'rusak';
    case Kedaluwarsa = 'kedaluwarsa';
    case TidakSesuai = 'tidak_sesuai';

    public function label(): string
    {
        return match ($this) {
            self::Baik => 'Baik (Layak)',
            self::Rusak => 'Rusak',
            self::Kedaluwarsa => 'Kedaluwarsa',
            self::TidakSesuai => 'Tidak Sesuai',
        };
    }

    public function isAccepted(): bool
    {
        return $this === self::Baik;
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Baik => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            self::Rusak => 'bg-red-100 text-red-700 border-red-200',
            self::Kedaluwarsa => 'bg-amber-100 text-amber-700 border-amber-200',
            self::TidakSesuai => 'bg-orange-100 text-orange-700 border-orange-200',
        };
    }
}
