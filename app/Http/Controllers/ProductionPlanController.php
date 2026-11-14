<?php

namespace App\Http\Controllers;

use App\Enums\ProductionPlanStatus;
use App\Http\Requests\StoreProductionPlanRequest;
use App\Http\Requests\UpdateProductionPlanRequest;
use App\Models\Product;
use App\Models\ProductionPlan;
use App\Services\ProductionIngredientCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ProductionPlanController extends Controller
{
    /**
     * Menampilkan daftar seluruh rencana produksi dapur dan jadwal memasak.
     */
    public function index(Request $request): View
    {
        $statusMap = [
            'scheduled' => 'scheduled',
            'in_progress' => 'in_progress',
            'completed' => 'completed',
            'draft' => 'draft',
            'cancelled' => 'cancelled',
            'dijadwalkan' => 'scheduled',
            'sedang dimasak' => 'in_progress',
            'selesai' => 'completed',
            'dibatalkan' => 'cancelled',
        ];

        $filterWarning = null;
        $productionDate = $request->input('production_date');
        $statusInput = $request->input('status');

        $query = ProductionPlan::query()
            ->with(['creator', 'details.product'])
            ->withCount('details')
            ->withSum('details as total_portions', 'planned_quantity');

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where('plan_number', 'LIKE', "%{$search}%");
        }

        if ($request->filled('production_date')) {
            try {
                $parsedDate = Carbon::createFromFormat('Y-m-d', (string) $productionDate);
                if ($parsedDate && $parsedDate->format('Y-m-d') === $productionDate) {
                    $query->productionDate($productionDate);
                } else {
                    $filterWarning = 'Format tanggal produksi tidak valid. Gunakan format YYYY-MM-DD.';
                }
            } catch (\Throwable) {
                $filterWarning = 'Format tanggal produksi tidak valid. Gunakan format YYYY-MM-DD.';
            }
        }

        $activeStatus = null;
        if ($request->filled('status')) {
            $normalizedStatus = strtolower(str_replace(['-', ' '], '_', trim((string) $statusInput)));
            if (isset($statusMap[$normalizedStatus])) {
                $activeStatus = $statusMap[$normalizedStatus];
                $query->where('status', $activeStatus);
            } else {
                $filterWarning = $filterWarning
                    ? $filterWarning.' Status rencana yang dipilih tidak valid.'
                    : 'Status rencana produksi yang dipilih tidak valid.';
            }
        }

        $statusCounts = [
            'all' => ProductionPlan::count(),
            'scheduled' => ProductionPlan::where('status', ProductionPlanStatus::Scheduled->value)->count(),
            'in_progress' => ProductionPlan::where('status', ProductionPlanStatus::InProgress->value)->count(),
            'completed' => ProductionPlan::where('status', ProductionPlanStatus::Completed->value)->count(),
        ];

        $plans = $query->orderByDesc('production_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('production-plans.index', [
            'plans' => $plans,
            'statuses' => ProductionPlanStatus::cases(),
            'statusCounts' => $statusCounts,
            'currentSearch' => $request->input('search'),
            'currentStatus' => $activeStatus ?? $request->input('status'),
            'currentDate' => $productionDate,
            'filterWarning' => $filterWarning,
        ]);
    }

    /**
     * Menampilkan kalender visual jadwal masak dapur katering terpadu.
     */
    public function calendar(Request $request): View
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        if ($month < 1 || $month > 12) {
            $month = (int) now()->month;
        }
        if ($year < 2020 || $year > 2040) {
            $year = (int) now()->year;
        }

        $currentDate = Carbon::createFromDate($year, $month, 1);
        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth = $currentDate->copy()->endOfMonth();

        // Rentang grid kalender (Senin s/d Minggu)
        $gridStart = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        $plans = ProductionPlan::query()
            ->with([
                'cateringOrder.customer',
                'details.product',
                'creator',
            ])
            ->whereBetween('production_date', [
                $gridStart->toDateString(),
                $gridEnd->toDateString(),
            ])
            ->orderBy('production_date')
            ->get();

        $plansByDate = $plans->groupBy(function ($plan) {
            return $plan->production_date ? $plan->production_date->format('Y-m-d') : '';
        });

        // Metrik ringkasan bulan terpilih
        $monthPlans = $plans->filter(function ($plan) use ($month, $year) {
            return $plan->production_date &&
                $plan->production_date->month === $month &&
                $plan->production_date->year === $year;
        });

        $totalPlans = $monthPlans->count();
        $totalPortions = (int) $monthPlans->sum(fn ($plan) => $plan->totalPlannedQuantity());
        $activeBatches = $monthPlans->whereIn('status', [
            ProductionPlanStatus::Scheduled,
            ProductionPlanStatus::InProgress,
        ])->count();
        $completedBatches = $monthPlans->where('status', ProductionPlanStatus::Completed)->count();

        // Susun slot hari kalender
        $calendarDays = [];
        $cursor = $gridStart->copy();
        while ($cursor->lte($gridEnd)) {
            $dateString = $cursor->format('Y-m-d');
            $calendarDays[] = [
                'date' => $cursor->copy(),
                'date_string' => $dateString,
                'is_current_month' => $cursor->month === $month,
                'is_today' => $cursor->isToday(),
                'day_number' => $cursor->day,
                'plans' => $plansByDate->get($dateString, collect()),
            ];
            $cursor->addDay();
        }

        $prevDate = $currentDate->copy()->subMonth();
        $nextDate = $currentDate->copy()->addMonth();

        return view('production-plans.calendar', [
            'currentDate' => $currentDate,
            'calendarDays' => $calendarDays,
            'selectedMonth' => $month,
            'selectedYear' => $year,
            'prevMonth' => $prevDate->month,
            'prevYear' => $prevDate->year,
            'nextMonth' => $nextDate->month,
            'nextYear' => $nextDate->year,
            'totalPlans' => $totalPlans,
            'totalPortions' => $totalPortions,
            'activeBatches' => $activeBatches,
            'completedBatches' => $completedBatches,
        ]);
    }

    /**
     * Menampilkan formulir pembuatan rencana produksi baru.
     */
    public function create(): View
    {
        $products = Product::query()
            ->active()
            ->withCount('recipeItems')
            ->orderBy('name')
            ->get();

        $suggestedPlanNumber = ProductionPlan::generatePlanNumber();
        $today = Carbon::today()->format('Y-m-d');

        return view('production-plans.create', [
            'products' => $products,
            'suggestedPlanNumber' => $suggestedPlanNumber,
            'today' => $today,
        ]);
    }

    /**
     * Menyimpan rencana produksi dan rincian menu hidangan ke database.
     */
    public function store(StoreProductionPlanRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $plan = DB::transaction(function () use ($request, $validated) {
            $planNumber = ProductionPlan::generatePlanNumber(
                Carbon::parse($validated['plan_date'])
            );

            $productionPlan = ProductionPlan::create([
                'plan_number' => $planNumber,
                'plan_date' => $validated['plan_date'],
                'production_date' => $validated['production_date'],
                'status' => ProductionPlanStatus::Scheduled,
                'notes' => $validated['notes'] ?? null,
                'created_by' => $request->user()->id,
            ]);

            foreach ($validated['items'] as $item) {
                $productionPlan->details()->create([
                    'product_id' => $item['product_id'],
                    'planned_quantity' => $item['planned_quantity'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            return $productionPlan;
        });

        return redirect()
            ->route('production-plans.show', $plan)
            ->with('success', "Rencana produksi {$plan->plan_number} berhasil dibuat dan dijadwalkan.");
    }

    /**
     * Menampilkan detail rencana produksi, komposisi menu, dan kalkulasi kebutuhan bahan baku otomatis.
     */
    public function show(ProductionPlan $productionPlan, ProductionIngredientCalculator $calculator): View
    {
        $productionPlan->load([
            'creator',
            'details.product.recipeItems.ingredient',
        ]);

        $calculation = $calculator->calculateForPlan($productionPlan);

        return view('production-plans.show', [
            'plan' => $productionPlan,
            'calculation' => $calculation,
        ]);
    }

    /**
     * Menampilkan lembar kerja / laporan kebutuhan bahan baku rencana produksi untuk dapur & logistik gudang.
     */
    public function ingredients(ProductionPlan $productionPlan, ProductionIngredientCalculator $calculator): View
    {
        $productionPlan->load([
            'creator',
            'details.product.recipeItems.ingredient',
        ]);

        $calculation = $calculator->calculateForPlan($productionPlan);

        return view('production-plans.ingredients', [
            'plan' => $productionPlan,
            'calculation' => $calculation,
        ]);
    }

    /**
     * Preview kalkulasi kebutuhan bahan baku secara interaktif untuk simulasi menu & porsi.
     */
    public function calculatePreview(Request $request, ProductionIngredientCalculator $calculator): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.planned_quantity' => ['required', 'integer', 'min:1'],
        ]);

        $calculation = $calculator->calculateForItems($validated['items']);

        return response()->json($calculation);
    }

    /**
     * Menampilkan formulir edit rencana produksi.
     */
    public function edit(ProductionPlan $productionPlan): View
    {
        $productionPlan->load('details.product');

        $products = Product::query()
            ->active()
            ->withCount('recipeItems')
            ->orderBy('name')
            ->get();

        return view('production-plans.edit', [
            'plan' => $productionPlan,
            'products' => $products,
            'statuses' => ProductionPlanStatus::cases(),
        ]);
    }

    /**
     * Memperbarui rencana produksi beserta rinciannya.
     */
    public function update(UpdateProductionPlanRequest $request, ProductionPlan $productionPlan): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $productionPlan) {
            $productionPlan->update([
                'plan_date' => $validated['plan_date'],
                'production_date' => $validated['production_date'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Hapus detail lama dan ganti dengan yang baru
            $productionPlan->details()->delete();

            foreach ($validated['items'] as $item) {
                $productionPlan->details()->create([
                    'product_id' => $item['product_id'],
                    'planned_quantity' => $item['planned_quantity'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('production-plans.show', $productionPlan)
            ->with('success', "Rencana produksi {$productionPlan->plan_number} berhasil diperbarui.");
    }

    /**
     * Menghapus rencana produksi.
     */
    public function destroy(ProductionPlan $productionPlan): RedirectResponse
    {
        // Cek jika sudah memiliki relasi ke pelaksanaan produksi fisik atau pengeluaran bahan
        $hasExecutions = false;

        if (Schema::hasTable('productions')) {
            $detailIds = $productionPlan->details()->pluck('id');
            if ($detailIds->isNotEmpty() && DB::table('productions')->whereIn('production_plan_detail_id', $detailIds)->exists()) {
                $hasExecutions = true;
            }
        }

        if (Schema::hasTable('material_requests') && DB::table('material_requests')->where('production_plan_id', $productionPlan->id)->exists()) {
            $hasExecutions = true;
        }

        if ($hasExecutions) {
            $productionPlan->update(['status' => ProductionPlanStatus::Cancelled]);

            return redirect()
                ->route('production-plans.index')
                ->with('warning', "Rencana produksi {$productionPlan->plan_number} sudah memiliki catatan operasional terkait sehingga statusnya dialihkan ke 'Dibatalkan' dan tetap tercatat di laporan.");
        }

        $planNumber = $productionPlan->plan_number;
        $productionPlan->details()->delete();
        $productionPlan->delete();

        return redirect()
            ->route('production-plans.index')
            ->with('success', "Rencana produksi {$planNumber} berhasil dihapus.");
    }
}
