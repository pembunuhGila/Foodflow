<?php

namespace Database\Seeders;

use App\Models\CateringOrder;
use App\Models\CateringOrderDetail;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CateringOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@foodflow.test')->first() ?? User::first();
        $customers = Customer::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();

        if ($customers->isEmpty() || $products->isEmpty() || ! $admin) {
            return;
        }

        $now = Carbon::now();

        $ordersData = [
            [
                'order_number' => 'ORD-'.date('Ym').'-0001',
                'customer_index' => 0,
                'days_ago' => 12,
                'delivery_days_ago' => 12,
                'status' => 'delivered',
                'notes' => 'Makan siang bergizi siswa SDIT Insan Mandiri 150 porsi.',
                'items' => [
                    ['product_index' => 0, 'qty' => 150],
                ],
            ],
            [
                'order_number' => 'ORD-'.date('Ym').'-0002',
                'customer_index' => 1,
                'days_ago' => 11,
                'delivery_days_ago' => 11,
                'status' => 'delivered',
                'notes' => 'Katering harian karyawan kantor PT Citra Pratama 80 box.',
                'items' => [
                    ['product_index' => 6, 'qty' => 80],
                ],
            ],
            [
                'order_number' => 'ORD-'.date('Ym').'-0003',
                'customer_index' => 2,
                'days_ago' => 10,
                'delivery_days_ago' => 10,
                'status' => 'delivered',
                'notes' => 'Menu rawat inap rendah garam Klinik Medika Sehat.',
                'items' => [
                    ['product_index' => 1, 'qty' => 50],
                ],
            ],
            [
                'order_number' => 'ORD-'.date('Ym').'-0004',
                'customer_index' => 3,
                'days_ago' => 9,
                'delivery_days_ago' => 9,
                'status' => 'delivered',
                'notes' => 'Snack box & makan siang rapat dinas BKD Kota.',
                'items' => [
                    ['product_index' => 0, 'qty' => 100],
                    ['product_index' => 4, 'qty' => 100],
                ],
            ],
            [
                'order_number' => 'ORD-'.date('Ym').'-0005',
                'customer_index' => 4,
                'days_ago' => 8,
                'delivery_days_ago' => 8,
                'status' => 'delivered',
                'notes' => 'Menu lunak lansia Yayasan Kasih Ibu.',
                'items' => [
                    ['product_index' => 2, 'qty' => 45],
                ],
            ],
            [
                'order_number' => 'ORD-'.date('Ym').'-0006',
                'customer_index' => 1,
                'days_ago' => 5,
                'delivery_days_ago' => 5,
                'status' => 'delivered',
                'notes' => 'Katering rapat bulanan PT Citra Pratama.',
                'items' => [
                    ['product_index' => 6, 'qty' => 60],
                ],
            ],
            [
                'order_number' => 'ORD-'.date('Ym').'-0007',
                'customer_index' => 0,
                'days_ago' => 2,
                'delivery_days_ago' => 2,
                'status' => 'delivered',
                'notes' => 'Paket bento gizi seimbang anak sekolah.',
                'items' => [
                    ['product_index' => 0, 'qty' => 90],
                ],
            ],
            [
                'order_number' => 'ORD-'.date('Ym').'-0008',
                'customer_index' => 2,
                'days_ago' => 1,
                'delivery_days_ago' => 0,
                'status' => 'processing',
                'notes' => 'Pesanan katering siang ini sedang dimasak di dapur.',
                'items' => [
                    ['product_index' => 1, 'qty' => 70],
                    ['product_index' => 3, 'qty' => 70],
                ],
            ],
            [
                'order_number' => 'ORD-'.date('Ym').'-0009',
                'customer_index' => 3,
                'days_ago' => 1,
                'delivery_days_ago' => 0,
                'status' => 'processing',
                'notes' => 'Katering makan siang Dinas BKD Kota Malang.',
                'items' => [
                    ['product_index' => 6, 'qty' => 65],
                ],
            ],
            [
                'order_number' => 'ORD-'.date('Ym').'-0010',
                'customer_index' => 0,
                'days_ago' => 0,
                'delivery_days_ago' => -1,
                'status' => 'confirmed',
                'notes' => 'Pesanan katering besok pagi Panti Asuhan & Sekolah.',
                'items' => [
                    ['product_index' => 0, 'qty' => 100],
                ],
            ],
            [
                'order_number' => 'ORD-'.date('Ym').'-0011',
                'customer_index' => 1,
                'days_ago' => 0,
                'delivery_days_ago' => -2,
                'status' => 'confirmed',
                'notes' => 'Pesanan weekend meeting PT Citra Pratama.',
                'items' => [
                    ['product_index' => 6, 'qty' => 55],
                ],
            ],
            [
                'order_number' => 'ORD-'.date('Ym').'-0012',
                'customer_index' => 4,
                'days_ago' => 0,
                'delivery_days_ago' => -4,
                'status' => 'pending',
                'notes' => 'Permintaan penawaran prasmanan pekan depan.',
                'items' => [
                    ['product_index' => 2, 'qty' => 80],
                ],
            ],
        ];

        foreach ($ordersData as $data) {
            $customer = $customers->get($data['customer_index'] % $customers->count());

            $orderDate = $now->copy()->subDays($data['days_ago'])->format('Y-m-d');
            $deliveryDate = $now->copy()->subDays($data['delivery_days_ago'])->format('Y-m-d');

            $order = CateringOrder::updateOrCreate(
                ['order_number' => $data['order_number']],
                [
                    'customer_id' => $customer->id,
                    'created_by' => $admin->id,
                    'order_date' => $orderDate,
                    'delivery_date' => $deliveryDate,
                    'status' => $data['status'],
                    'total_amount' => 0,
                    'notes' => $data['notes'],
                ]
            );

            $totalAmount = 0;
            foreach ($data['items'] as $item) {
                $product = $products->get($item['product_index'] % $products->count());
                $unitPrice = (float) $product->selling_price;
                $subtotal = $unitPrice * $item['qty'];
                $totalAmount += $subtotal;

                CateringOrderDetail::updateOrCreate(
                    [
                        'catering_order_id' => $order->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'quantity' => $item['qty'],
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                        'notes' => 'Porsi standar higienis FoodFlow',
                    ]
                );
            }

            $order->update(['total_amount' => $totalAmount]);
        }
    }
}
