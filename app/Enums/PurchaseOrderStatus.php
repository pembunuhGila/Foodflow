<?php

namespace App\Enums;

enum PurchaseOrderStatus: string
{
    case Draft = 'draft';
    case Ordered = 'ordered';
    case Received = 'received';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Ordered => 'Dipesan',
            self::Received => 'Diterima',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'bg-gray-100 text-gray-700 border-gray-200',
            self::Ordered => 'bg-blue-100 text-blue-700 border-blue-200',
            self::Received => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            self::Cancelled => 'bg-red-100 text-red-700 border-red-200',
        };
    }
}
