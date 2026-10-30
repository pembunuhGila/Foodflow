<?php

namespace App\Http\Controllers\Owner;

use App\Enums\ProductionPlanStatus;
use App\Http\Controllers\Controller;
use App\Models\ProductionMaterialUsage;
use App\Models\ProductionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ProductionMonitoringController extends Controller
{
    /**
     * Menampilkan dashboard monitoring live capaian produksi dapur untuk Owner dan Admin (FR-37, UC-10).
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        if (! $user->isOwner() && ! $user->isAdmin()) {
            abort(403, 'Akses ditolak: Hanya Owner dan Admin yang memiliki otorisasi mengakses dashboard monitoring produksi.');
        }

        $period = $request->input('period', 'today');
        $status = $request->input('status');
        $search = trim((string) $request->input('search'));

        $today = Carbon::today();

        // 1. Query Agregasi Ringkasan Eksekutif (Berdasarkan Periode Waktu)
        $summaryQuery = ProductionPlan::query()
            ->whereNotIn('status', [
                ProductionPlanStatus::Draft->value,
                ProductionPlanStatus::Cancelled->value,
            ]);

        if ($period === 'today') {
            $summaryQuery->whereDate('production_date', $today->toDateString());
        } elseif ($period === 'this_week') {
            $summaryQuery->whereBetween('production_date', [
                $today->copy()->startOfWeek()->toDateString(),
                $today->copy()->endOfWeek()->toDateString(),
            ]);
        } elseif ($period === 'this_month') {
            $summaryQuery->whereMonth('production_date', $today->month)
                ->whereYear('production_date', $today->year);
        }

        $summaryPlans = (clone $summaryQuery)->with(['details.production'])->get();

        $totalPlannedPortions = (int) $summaryPlans->sum(fn ($plan) => $plan->totalPlannedQuantity());
        $totalActualPortions = (int) $summaryPlans->sum(fn ($plan) => $plan->totalActualQuantity());
        $overallEfficiencyRate = $totalPlannedPortions > 0
            ? round(($totalActualPortions / $totalPlannedPortions) * 100, 1)
            : 0.0;

        $activeBatchCount = ProductionPlan::query()
            ->whereIn('status', [
                ProductionPlanStatus::Scheduled->value,
                ProductionPlanStatus::InProgress->value,
            ])
            ->count();

        $completedBatchCount = (clone $summaryQuery)
            ->where('status', ProductionPlanStatus::Completed->value)
            ->count();

        // 2. Metrik Efisiensi Pemakaian Bahan Baku Riil Dapur (FR-35 & FR-37)
        $summaryPlanIds = $summaryPlans->pluck('id');
        $materialUsages = ProductionMaterialUsage::query()
            ->whereIn('production_plan_id', $summaryPlanIds)
            ->get();

        $totalPlannedMaterial = (float) $materialUsages->sum('planned_quantity');
        $totalActualMaterial = (float) $materialUsages->sum('actual_quantity');
        $materialVariance = (float) $materialUsages->sum('difference_quantity');

        // 3. Query Tabel Pemantauan Batch Produksi Realtime
        $plansQuery = ProductionPlan::query()
            ->with([
                'cateringOrder.customer',
                'creator',
                'details.product',
                'details.production',
                'materialUsages.ingredient',
            ])
            ->orderByDesc('production_date')
            ->orderByDesc('id');

        if ($period === 'today') {
            $plansQuery->whereDate('production_date', $today->toDateString());
        } elseif ($period === 'this_week') {
            $plansQuery->whereBetween('production_date', [
                $today->copy()->startOfWeek()->toDateString(),
                $today->copy()->endOfWeek()->toDateString(),
            ]);
        } elseif ($period === 'this_month') {
            $plansQuery->whereMonth('production_date', $today->month)
                ->whereYear('production_date', $today->year);
        }

        if ($status && $status !== 'all') {
            $plansQuery->where('status', $status);
        }

        if ($search !== '') {
            $plansQuery->where(function ($q) use ($search) {
                $q->where('plan_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('cateringOrder', function ($orderQ) use ($search) {
                        $orderQ->where('order_number', 'LIKE', "%{$search}%")
                            ->orWhereHas('customer', function ($custQ) use ($search) {
                                $custQ->where('name', 'LIKE', "%{$search}%");
                            });
                    });
            });
        }

        $plans = $plansQuery->paginate(10)->withQueryString();

        return view('owner.production-monitoring.index', [
            'plans' => $plans,
            'period' => $period,
            'currentStatus' => $status,
            'currentSearch' => $search,
            'totalPlannedPortions' => $totalPlannedPortions,
            'totalActualPortions' => $totalActualPortions,
            'overallEfficiencyRate' => $overallEfficiencyRate,
            'activeBatchCount' => $activeBatchCount,
            'completedBatchCount' => $completedBatchCount,
            'totalPlannedMaterial' => $totalPlannedMaterial,
            'totalActualMaterial' => $totalActualMaterial,
            'materialVariance' => $materialVariance,
        ]);
    }
}
