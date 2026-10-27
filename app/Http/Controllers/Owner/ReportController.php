<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\IngredientStockTransaction;
use App\Models\Product;
use App\Models\ProductionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Monitoring Evaluasi Kepatuhan Profil Gizi Menu Hidangan (UC-11, FR-39).
     */
    public function nutrition(Request $request): View
    {
        $products = Product::query()
            ->active()
            ->withCount('recipeItems')
            ->orderBy('name')
            ->get();

        $averageCalories = round($products->avg('calories') ?? 0, 1);
        $averageProtein = round($products->avg('protein') ?? 0, 1);
        $averageFat = round($products->avg('fat') ?? 0, 1);
        $averageCarbs = round($products->avg('carbohydrates') ?? 0, 1);
        $averageFiber = round($products->avg('dietary_fiber') ?? 0, 1);

        return view('owner.nutrition.index', [
            'products' => $products,
            'avgCalories' => $averageCalories,
            'avgProtein' => $averageProtein,
            'avgFat' => $averageFat,
            'avgCarbs' => $averageCarbs,
            'avgFiber' => $averageFiber,
        ]);
    }

    /**
     * Halaman Hub Pusat Cetak Laporan Resmi Eksekutif Owner (UC-12, FR-40).
     */
    public function index(): View
    {
        return view('owner.reports.index', [
            'currentMonth' => Carbon::now()->format('Y-m'),
            'today' => Carbon::now()->format('Y-m-d'),
        ]);
    }

    /**
     * Laporan Resmi Rekapitulasi Produksi Masak Dapur (Print Ready Layout).
     */
    public function production(Request $request): View
    {
        $fromDate = $request->input('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->input('to_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $plans = ProductionPlan::query()
            ->with(['details.product', 'creator'])
            ->whereDate('plan_date', '>=', $fromDate)
            ->whereDate('plan_date', '<=', $toDate)
            ->orderBy('plan_date')
            ->get();

        $totalPlanned = 0;
        $totalActual = 0;

        foreach ($plans as $plan) {
            $totalPlanned += $plan->totalPlannedQuantity();
            $totalActual += $plan->totalActualQuantity();
        }

        $achievementRate = $totalPlanned > 0
            ? round(($totalActual / $totalPlanned) * 100, 1)
            : 0;

        return view('owner.reports.production-print', [
            'plans' => $plans,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'totalPlanned' => $totalPlanned,
            'totalActual' => $totalActual,
            'achievementRate' => $achievementRate,
            'printedAt' => Carbon::now(),
        ]);
    }

    /**
     * Laporan Resmi Mutasi & Persediaan Bahan Baku Gudang (Print Ready Layout).
     */
    public function inventory(Request $request): View
    {
        $fromDate = $request->input('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->input('to_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $ingredients = Ingredient::query()
            ->active()
            ->orderBy('name')
            ->get();

        // Ambil agregat mutasi per bahan baku dalam rentang periode
        $transactions = IngredientStockTransaction::query()
            ->whereDate('transaction_date', '>=', $fromDate)
            ->whereDate('transaction_date', '<=', $toDate)
            ->get();

        $reportData = $ingredients->map(function ($ing) use ($transactions) {
            $ingTx = $transactions->where('ingredient_id', $ing->id);

            $totalIn = (float) $ingTx->where('transaction_type', 'in')->sum('quantity');
            $totalOut = (float) $ingTx->where('transaction_type', 'out')->sum('quantity');
            $totalAdj = (float) $ingTx->where('transaction_type', 'adjustment')->sum('quantity');
            $currentStock = (float) ($ing->current_stock ?? 0);

            return [
                'ingredient' => $ing,
                'total_in' => $totalIn,
                'total_out' => $totalOut,
                'total_adj' => $totalAdj,
                'current_stock' => $currentStock,
            ];
        });

        return view('owner.reports.inventory-print', [
            'reportData' => $reportData,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'printedAt' => Carbon::now(),
        ]);
    }
}
