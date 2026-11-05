<?php

namespace Database\Seeders;

use App\Enums\ProductionPlanStatus;
use App\Models\Product;
use App\Models\Production;
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
        $produksiUser = User::where('email', 'produksi@foodflow.test')->first()
            ?? User::first();

        $products = Product::where('is_active', true)->get();

        if ($products->isEmpty() || ! $produksiUser) {
            return;
        }

        $now = Carbon::now();

        // 16 Batch Rencana Produksi untuk menguji pagination (10 item/halaman -> 2 halaman)
        $plansData = [
            [
                'plan_number' => 'PP-'.date('Ym').'-0001',
                'plan_date' => $now->copy()->subDays(12)->format('Y-m-d'),
                'production_date' => $now->copy()->subDays(12)->format('Y-m-d'),
                'status' => ProductionPlanStatus::Completed,
                'notes' => 'Batch katering MBG SDIT Insan Mandiri 150 porsi.',
                'items' => [
                    ['qty' => 150, 'actual' => 150],
                ],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0002',
                'plan_date' => $now->copy()->subDays(11)->format('Y-m-d'),
                'production_date' => $now->copy()->subDays(11)->format('Y-m-d'),
                'status' => ProductionPlanStatus::Completed,
                'notes' => 'Batch katering makan siang kantor PT Citra Pratama 80 box.',
                'items' => [
                    ['qty' => 80, 'actual' => 82],
                ],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0003',
                'plan_date' => $now->copy()->subDays(10)->format('Y-m-d'),
                'production_date' => $now->copy()->subDays(10)->format('Y-m-d'),
                'status' => ProductionPlanStatus::Completed,
                'notes' => 'Batch menu rawat inap diet rendah garam Klinik Medika.',
                'items' => [
                    ['qty' => 50, 'actual' => 50],
                ],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0004',
                'plan_date' => $now->copy()->subDays(9)->format('Y-m-d'),
                'production_date' => $now->copy()->subDays(9)->format('Y-m-d'),
                'status' => ProductionPlanStatus::Completed,
                'notes' => 'Batch makan siang bento SMPN 1 Malang 200 porsi.',
                'items' => [
                    ['qty' => 200, 'actual' => 198],
                ],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0005',
                'plan_date' => $now->copy()->subDays(8)->format('Y-m-d'),
                'production_date' => $now->copy()->subDays(8)->format('Y-m-d'),
                'status' => ProductionPlanStatus::Completed,
                'notes' => 'Batch menu lansia bubur lunak Yayasan Kasih Ibu.',
                'items' => [
                    ['qty' => 45, 'actual' => 45],
                ],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0006',
                'plan_date' => $now->copy()->subDays(7)->format('Y-m-d'),
                'production_date' => $now->copy()->subDays(7)->format('Y-m-d'),
                'status' => ProductionPlanStatus::Completed,
                'notes' => 'Batch snack box rapat BKD Kota Depok.',
                'items' => [
                    ['qty' => 60, 'actual' => 60],
                ],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0007',
                'plan_date' => $now->copy()->subDays(6)->format('Y-m-d'),
                'production_date' => $now->copy()->subDays(6)->format('Y-m-d'),
                'status' => ProductionPlanStatus::Completed,
                'notes' => 'Batch menu sehat SMA Brawijaya Smart School.',
                'items' => [
                    ['qty' => 120, 'actual' => 120],
                ],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0008',
                'plan_date' => $now->copy()->subDays(5)->format('Y-m-d'),
                'production_date' => $now->copy()->subDays(5)->format('Y-m-d'),
                'status' => ProductionPlanStatus::Completed,
                'notes' => 'Batch PMT Puskesmas Dinoyo balita gizi seimbang.',
                'items' => [
                    ['qty' => 75, 'actual' => 75],
                ],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0009',
                'plan_date' => $now->copy()->subDays(4)->format('Y-m-d'),
                'production_date' => $now->copy()->subDays(4)->format('Y-m-d'),
                'status' => ProductionPlanStatus::Completed,
                'notes' => 'Batch katering karyawan PT TS Digital 60 porsi.',
                'items' => [
                    ['qty' => 60, 'actual' => 60],
                ],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0010',
                'plan_date' => $now->copy()->subDays(3)->format('Y-m-d'),
                'production_date' => $now->copy()->subDays(3)->format('Y-m-d'),
                'status' => ProductionPlanStatus::Completed,
                'notes' => 'Batch menu pemulihan gizi RSIA Permata.',
                'items' => [
                    ['qty' => 40, 'actual' => 40],
                ],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0011',
                'plan_date' => $now->copy()->subDays(2)->format('Y-m-d'),
                'production_date' => $now->copy()->subDays(2)->format('Y-m-d'),
                'status' => ProductionPlanStatus::Completed,
                'notes' => 'Batch katering meeting Bank Indonesia Malang.',
                'items' => [
                    ['qty' => 35, 'actual' => 35],
                ],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0012',
                'plan_date' => $now->copy()->subDays(1)->format('Y-m-d'),
                'production_date' => $now->copy()->subDays(1)->format('Y-m-d'),
                'status' => ProductionPlanStatus::Completed,
                'notes' => 'Batch menu anak SD Bintang Kejora bebas alergen.',
                'items' => [
                    ['qty' => 90, 'actual' => 90],
                ],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0013',
                'plan_date' => $now->format('Y-m-d'),
                'production_date' => $now->format('Y-m-d'),
                'status' => ProductionPlanStatus::InProgress,
                'notes' => 'Batch produksi siang hari ini sedang proses masak dapur.',
                'items' => [
                    ['qty' => 110, 'actual' => 70],
                ],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0014',
                'plan_date' => $now->format('Y-m-d'),
                'production_date' => $now->format('Y-m-d'),
                'status' => ProductionPlanStatus::InProgress,
                'notes' => 'Batch katering makan siang Dinas Pendidikan Kota Malang.',
                'items' => [
                    ['qty' => 65, 'actual' => 30],
                ],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0015',
                'plan_date' => $now->copy()->addDay()->format('Y-m-d'),
                'production_date' => $now->copy()->addDay()->format('Y-m-d'),
                'status' => ProductionPlanStatus::Scheduled,
                'notes' => 'Batch persiapan masak katering besok pagi Panti Asuhan Kasih Sayang.',
                'items' => [
                    ['qty' => 100, 'actual' => 0],
                ],
            ],
            [
                'plan_number' => 'PP-'.date('Ym').'-0016',
                'plan_date' => $now->copy()->addDays(2)->format('Y-m-d'),
                'production_date' => $now->copy()->addDays(2)->format('Y-m-d'),
                'status' => ProductionPlanStatus::Scheduled,
                'notes' => 'Batch katering atlet Gym Vitality Malang akhir pekan.',
                'items' => [
                    ['qty' => 55, 'actual' => 0],
                ],
            ],
        ];

        foreach ($plansData as $index => $data) {
            $plan = ProductionPlan::updateOrCreate(
                ['plan_number' => $data['plan_number']],
                [
                    'plan_date' => $data['plan_date'],
                    'production_date' => $data['production_date'],
                    'status' => $data['status'],
                    'notes' => $data['notes'],
                    'created_by' => $produksiUser->id,
                ]
            );

            // Pasangkan produk secara bergantian
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
                            'production_date' => $data['production_date'],
                            'actual_quantity' => $item['actual'],
                            'status' => 'completed',
                            'notes' => 'Tercatat matang sempurna oleh tim dapur',
                            'created_by' => $produksiUser->id,
                        ]
                    );
                }
            }
        }
    }
}
