<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Gudang = 'gudang';
    case Produksi = 'produksi';
    case Owner = 'owner';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin / Staff Operasional',
            self::Gudang => 'Staff Gudang / Inventory',
            self::Produksi => 'Staff Produksi / Ahli Gizi',
            self::Owner => 'Owner / Manager',
        };
    }
}
