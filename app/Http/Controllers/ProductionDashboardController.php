<?php

namespace App\Http\Controllers;

use App\Enums\ProductionPlanStatus;
use App\Models\MaterialRequest;
use App\Models\ProductionPlan;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ProductionDashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = Carbon::today();
        $startDate = $today->copy()->subDays(6);

        $excludedStatuses = [
            ProductionPlanStatus::Draft->value,
            ProductionPlanStatus::Cancelled->value,
        ];

        // Rencana yang masih berjalan.
        $activePlans = ProductionPlan::query()
            ->whereIn('status', [
                ProductionPlanStatus::Scheduled->value,
                ProductionPlanStatus::InProgress->value,
            ])
            ->with(['details.product', 'details.production'])
            ->orderBy('production_date')
            ->orderBy('id')
            ->get();

        // Rencana yang dijadwalkan untuk hari ini.
        $todayPlans = ProductionPlan::query()
            ->whereDate('production_date', $today->toDateString())
            ->whereNotIn('status', $excludedStatuses)
            ->with(['details.product', 'details.production'])
            ->get();

        $todayTarget = (int) $todayPlans->sum(
            fn ($plan) => $plan->totalPlannedQuantity()
        );

        $todayActual = (int) $todayPlans->sum(
            fn ($plan) => $plan->totalActualQuantity()
        );

        $completionRate = $todayTarget > 0
            ? round(($todayActual / $todayTarget) * 100, 1)
            : 0;

        // Permintaan bahan yang belum diverifikasi.
        $pendingMaterials = MaterialRequest::pending()->count();

        // Data target dan realisasi untuk grafik tujuh hari.
        $weeklyPlans = ProductionPlan::query()
            ->whereBetween('production_date', [
                $startDate->toDateString(),
                $today->toDateString(),
            ])
            ->whereNotIn('status', $excludedStatuses)
            ->with(['details.production'])
            ->get();

        $weeklyChart = collect(range(0, 6))->map(
            function ($offset) use ($startDate, $weeklyPlans) {
                $date = $startDate->copy()->addDays($offset);

                $plans = $weeklyPlans->filter(
                    fn ($plan) => $plan->production_date->toDateString()
                        === $date->toDateString()
                );

                return [
                    'date' => $date->format('d/m'),
                    'label' => $date->translatedFormat('D'),
                    'target' => (int) $plans->sum(
                        fn ($plan) => $plan->totalPlannedQuantity()
                    ),
                    'actual' => (int) $plans->sum(
                        fn ($plan) => $plan->totalActualQuantity()
                    ),
                ];
            }
        );

        return view('dashboard.produksi', [
            'today' => $today,
            'activePlanCount' => $activePlans->count(),
            'todayTarget' => $todayTarget,
            'todayActual' => $todayActual,
            'pendingMaterials' => $pendingMaterials,
            'completionRate' => $completionRate,
            'activePlans' => $activePlans->take(5),
            'weeklyChart' => $weeklyChart,
        ]);
    }
}
