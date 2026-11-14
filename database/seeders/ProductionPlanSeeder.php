<?php

namespace Database\Seeders;

use App\Enums\ProductionPlanStatus;
use App\Models\CateringOrder;
use App\Models\Ingredient;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestDetail;
use App\Models\Product;
use App\Models\Production;
use App\Models\ProductionMaterialUsage;
use App\Models\ProductionPlan;
use App\Models\ProductionPlanDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProductionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $produksiUser = User::where('email', 'produksi@foodflow.test')->first() ?? User::first();
        $gudangUser = User::where('email', 'gudang@foodflow.test')->first() ?? User::first();

        $products = Product::where('is_active', true)->with('ingredients')->get();
        $orders = CateringOrder::orderBy('id')->get();
        $allIngredients = Ingredient::where('is_active', true)->get();

        if ($products->isEmpty() || ! $produksiUser) {
            return;
        }

        $now = Carbon::now();

        // 16 Batch Rencana Produksi terdistribusi di bulan berjalan
        $plansData = [
            [
                'plan_number' => 'PP-'.date('Ym').'-0001',
                'order_index' => 0,
                'days_ago' => 12,
                'status' => ProductionPlanStatus::Completed,
                'notes' => 'Batch katering MBG SDIT Insan Mandiri 150 porsi.',
                'items' => [['qty' => 150, 'actual' => 150]],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0002',
                'order_index' => 1,
                'days_ago' => 11,
                'status' => ProductionPlanStatus::Completed,
                'notes' => 'Batch katering makan siang kantor PT Citra Pratama 80 box.',
                'items' => [['qty' => 80, 'actual' => 82]],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0003',
                'order_index' => 2,
                'days_ago' => 10,
                'status' => ProductionPlanStatus::Completed,
                'notes' => 'Batch menu rawat inap diet rendah garam Klinik Medika.',
                'items' => [['qty' => 50, 'actual' => 50]],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0004',
                'order_index' => 3,
                'days_ago' => 9,
                'status' => ProductionPlanStatus::Completed,
                'notes' => 'Batch makan siang bento SMPN 1 Malang 200 porsi.',
                'items' => [['qty' => 200, 'actual' => 198]],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0005',
                'order_index' => 4,
                'days_ago' => 8,
                'status' => ProductionPlanStatus::Completed,
                'notes' => 'Batch menu lansia bubur lunak Yayasan Kasih Ibu.',
                'items' => [['qty' => 45, 'actual' => 45]],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0006',
                'order_index' => 5,
                'days_ago' => 5,
                'status' => ProductionPlanStatus::Completed,
                'notes' => 'Batch snack box rapat BKD Kota.',
                'items' => [['qty' => 60, 'actual' => 60]],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0007',
                'order_index' => 6,
                'days_ago' => 2,
                'status' => ProductionPlanStatus::Completed,
                'notes' => 'Batch menu sehat SMA Brawijaya Smart School.',
                'items' => [['qty' => 120, 'actual' => 120]],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0008',
                'order_index' => 7,
                'days_ago' => 0,
                'status' => ProductionPlanStatus::InProgress,
                'notes' => 'Batch produksi makan siang hari ini sedang dimasak di dapur sentral.',
                'items' => [['qty' => 110, 'actual' => 70]],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0009',
                'order_index' => 8,
                'days_ago' => 0,
                'status' => ProductionPlanStatus::InProgress,
                'notes' => 'Batch katering makan siang Dinas Pendidikan Kota Malang.',
                'items' => [['qty' => 65, 'actual' => 30]],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0010',
                'order_index' => 9,
                'days_ago' => -1,
                'status' => ProductionPlanStatus::Scheduled,
                'notes' => 'Batch persiapan masak katering besok pagi Panti Asuhan Kasih Sayang.',
                'items' => [['qty' => 100, 'actual' => 0]],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0011',
                'order_index' => 10,
                'days_ago' => -2,
                'status' => ProductionPlanStatus::Scheduled,
                'notes' => 'Batch katering akhir pekan PT Citra Pratama.',
                'items' => [['qty' => 55, 'actual' => 0]],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0012',
                'order_index' => 11,
                'days_ago' => -4,
                'status' => ProductionPlanStatus::Scheduled,
                'notes' => 'Batch katering prasmanan pekan depan.',
                'items' => [['qty' => 80, 'actual' => 0]],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0013',
                'order_index' => 0,
                'days_ago' => -6,
                'status' => ProductionPlanStatus::Scheduled,
                'notes' => 'Batch makan siang bergizi berkala SDIT Insan Mandiri.',
                'items' => [['qty' => 150, 'actual' => 0]],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0014',
                'order_index' => 1,
                'days_ago' => -8,
                'status' => ProductionPlanStatus::Scheduled,
                'notes' => 'Batch katering karyawan shift malam PT Citra Pratama.',
                'items' => [['qty' => 60, 'actual' => 0]],
            ],
        ];

        foreach ($plansData as $index => $data) {
            $order = $orders->get($data['order_index'] % max(1, $orders->count()));
            $prodDate = $now->copy()->subDays($data['days_ago'])->format('Y-m-d');

            $plan = ProductionPlan::updateOrCreate(
                ['plan_number' => $data['plan_number']],
                [
                    'catering_order_id' => $order?->id,
                    'plan_date' => $prodDate,
                    'production_date' => $prodDate,
                    'status' => $data['status'],
                    'notes' => $data['notes'],
                    'created_by' => $produksiUser->id,
                ]
            );

            // Pasangkan produk
            $product = $products->get($index % $products->count());

            foreach ($data['items'] as $item) {
                $detail = ProductionPlanDetail::updateOrCreate(
                    [
                        'production_plan_id' => $plan->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'planned_quantity' => $item['qty'],
                        'notes' => 'SOP porsi masak standar FoodFlow',
                    ]
                );

                if ($item['actual'] > 0) {
                    Production::updateOrCreate(
                        ['production_plan_detail_id' => $detail->id],
                        [
                            'production_number' => 'PRD-'.date('Ym').'-'.str_pad($detail->id, 4, '0', STR_PAD_LEFT),
                            'production_date' => $prodDate,
                            'actual_quantity' => $item['actual'],
                            'status' => 'completed',
                            'notes' => 'Tercatat matang sempurna oleh tim dapur',
                            'created_by' => $produksiUser->id,
                        ]
                    );
                }
            }

            // Buat permintaan bahan baku dapur (Material Request)
            $mr = MaterialRequest::updateOrCreate(
                ['production_plan_id' => $plan->id],
                [
                    'requested_by' => $produksiUser->id,
                    'verified_by' => in_array($data['status'], [ProductionPlanStatus::Completed, ProductionPlanStatus::InProgress]) ? $gudangUser->id : null,
                    'request_date' => $prodDate,
                    'status' => in_array($data['status'], [ProductionPlanStatus::Completed, ProductionPlanStatus::InProgress]) ? 'approved' : 'pending',
                    'notes' => 'Kebutuhan bahan baku produksi '.$data['plan_number'],
                ]
            );

            // Buat pencatatan pemakaian bahan riil (ProductionMaterialUsage) untuk plan yang sudah selesai atau sedang dimasak
            if (in_array($data['status'], [ProductionPlanStatus::Completed, ProductionPlanStatus::InProgress])) {
                $qtyFactor = $data['items'][0]['qty'] ?? 50;

                // Bahan resep utama
                $recipeIngredients = $product->ingredients;
                if ($recipeIngredients->isEmpty()) {
                    $recipeIngredients = $allIngredients->take(3);
                }

                foreach ($recipeIngredients as $ingIndex => $ing) {
                    $plannedPerPortion = (float) ($ing->pivot->quantity_per_unit ?? 0.15);
                    $plannedTotal = round($plannedPerPortion * $qtyFactor, 2);

                    // Variasi realistis: beberapa hemat (-), beberapa sedikit boros (+), beberapa sesuai (0)
                    $varianceMultiplier = match ($ingIndex % 3) {
                        0 => -0.05, // hemat 5%
                        1 => 0.04,  // boros 4%
                        default => 0.0, // pas sesuai resep
                    };

                    $actualTotal = round($plannedTotal * (1 + $varianceMultiplier), 2);
                    $diff = round($actualTotal - $plannedTotal, 2);

                    $notes = match ($ingIndex % 3) {
                        0 => 'Efisiensi takaran dan pemotongan rapi tim dapur',
                        1 => 'Penyusutan saat perebusan dan pembuangan lemak',
                        default => 'Penggunaan sesuai takaran formula standar',
                    };

                    ProductionMaterialUsage::updateOrCreate(
                        [
                            'production_plan_id' => $plan->id,
                            'ingredient_id' => $ing->id,
                        ],
                        [
                            'planned_quantity' => $plannedTotal,
                            'actual_quantity' => $actualTotal,
                            'difference_quantity' => $diff,
                            'unit' => $ing->unit ?? 'kg',
                            'notes' => $notes,
                            'recorded_by' => $produksiUser->id,
                        ]
                    );

                    MaterialRequestDetail::updateOrCreate(
                        [
                            'material_request_id' => $mr->id,
                            'ingredient_id' => $ing->id,
                        ],
                        [
                            'requested_quantity' => $plannedTotal,
                            'issued_quantity' => $actualTotal,
                            'unit' => $ing->unit ?? 'kg',
                            'notes' => 'Bahan dikeluarkan staf gudang',
                        ]
                    );
                }
            }
        }
    }
}
