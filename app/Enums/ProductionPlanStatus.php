<?php

namespace App\Enums;

enum ProductionPlanStatus: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Scheduled => 'Dijadwalkan',
            self::InProgress => 'Sedang Dimasak',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'bg-gray-100 text-gray-700 border-gray-200',
            self::Scheduled => 'bg-blue-100 text-blue-700 border-blue-200',
            self::InProgress => 'bg-amber-100 text-amber-700 border-amber-200',
            self::Completed => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            self::Cancelled => 'bg-red-100 text-red-700 border-red-200',
        };
    }
}
