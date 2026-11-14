<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gudang = User::where('email', 'gudang@foodflow.test')->first() ?? User::first();
        $suppliers = Supplier::where('is_active', true)->get();
        $ingredients = Ingredient::where('is_active', true)->get();

        if ($suppliers->isEmpty() || $ingredients->isEmpty() || ! $gudang) {
            return;
        }

        $now = Carbon::now();

        // 8 Purchase Order untuk pengadaan bahan baku ke mitra supplier
        $poData = [
            [
                'order_number' => 'PO-'.date('Ym').'-0001',
                'supplier_index' => 0, // CV Beras Tani Makmur
                'days_ago' => 14,
                'status' => 'received',
                'notes' => 'Pengadaan beras merah organik dan beras putih awal bulan.',
                'items' => [
                    ['ing_name' => 'Beras Merah Organik', 'qty' => 100, 'unit' => 'kg', 'price' => 18000],
                    ['ing_name' => 'Beras Putih Pandan Wangi', 'qty' => 150, 'unit' => 'kg', 'price' => 15000],
                ],
            ],
            [
                'order_number' => 'PO-'.date('Ym').'-0002',
                'supplier_index' => 1, // PT Unggas Sejahtera Nusantara
                'days_ago' => 12,
                'status' => 'received',
                'notes' => 'Pasokan fillet dada ayam broiler segar tanpa kulit.',
                'items' => [
                    ['ing_name' => 'Dada Ayam Fillet Segar', 'qty' => 80, 'unit' => 'kg', 'price' => 42000],
                    ['ing_name' => 'Telur Ayam Ras Segar', 'qty' => 50, 'unit' => 'kg', 'price' => 28000],
                ],
            ],
            [
                'order_number' => 'PO-'.date('Ym').'-0003',
                'supplier_index' => 2, // CV Sayur Segar Hidroponik
                'days_ago' => 10,
                'status' => 'received',
                'notes' => 'Pengadaan sayuran segar harian untuk menu katering.',
                'items' => [
                    ['ing_name' => 'Sayur Bayam Segar', 'qty' => 60, 'unit' => 'ikat', 'price' => 4000],
                    ['ing_name' => 'Wortel Berastagi', 'qty' => 50, 'unit' => 'kg', 'price' => 14000],
                    ['ing_name' => 'Brokoli Hijau Segar', 'qty' => 40, 'unit' => 'kg', 'price' => 22000],
                ],
            ],
            [
                'order_number' => 'PO-'.date('Ym').'-0004',
                'supplier_index' => 3, // PT Pangan Sehat Lestari
                'days_ago' => 8,
                'status' => 'received',
                'notes' => 'Minyak zaitun extra virgin dan tahu tempe organik.',
                'items' => [
                    ['ing_name' => 'Minyak Zaitun Extra Virgin', 'qty' => 20, 'unit' => 'liter', 'price' => 120000],
                    ['ing_name' => 'Tempe Kedelai Murni', 'qty' => 50, 'unit' => 'papan', 'price' => 8000],
                    ['ing_name' => 'Tahu Putih Kedelai Organik', 'qty' => 100, 'unit' => 'potong', 'price' => 2500],
                ],
            ],
            [
                'order_number' => 'PO-'.date('Ym').'-0005',
                'supplier_index' => 4, // UD Rempah Nusantara
                'days_ago' => 6,
                'status' => 'received',
                'notes' => 'Bumbu dapur lengkap, bawang putih, bawang merah, jahe.',
                'items' => [
                    ['ing_name' => 'Bawang Putih Kupas', 'qty' => 30, 'unit' => 'kg', 'price' => 36000],
                    ['ing_name' => 'Bawang Merah Brebes', 'qty' => 30, 'unit' => 'kg', 'price' => 32000],
                ],
            ],
            [
                'order_number' => 'PO-'.date('Ym').'-0006',
                'supplier_index' => 1, // PT Unggas Sejahtera
                'days_ago' => 3,
                'status' => 'ordered',
                'notes' => 'Tambahan daging ayam dan telur persiapan pesanan akhir pekan.',
                'items' => [
                    ['ing_name' => 'Dada Ayam Fillet Segar', 'qty' => 50, 'unit' => 'kg', 'price' => 42500],
                ],
            ],
            [
                'order_number' => 'PO-'.date('Ym').'-0007',
                'supplier_index' => 2, // Sayur Hidroponik
                'days_ago' => 1,
                'status' => 'ordered',
                'notes' => 'Pemesanan sayur segar besok pagi.',
                'items' => [
                    ['ing_name' => 'Wortel Berastagi', 'qty' => 30, 'unit' => 'kg', 'price' => 14000],
                    ['ing_name' => 'Brokoli Hijau Segar', 'qty' => 25, 'unit' => 'kg', 'price' => 22000],
                ],
            ],
            [
                'order_number' => 'PO-'.date('Ym').'-0008',
                'supplier_index' => 0, // Beras Tani
                'days_ago' => 0,
                'status' => 'draft',
                'notes' => 'Draft pengadaan restock beras akhir bulan.',
                'items' => [
                    ['ing_name' => 'Beras Putih Pandan Wangi', 'qty' => 100, 'unit' => 'kg', 'price' => 15000],
                ],
            ],
        ];

        foreach ($poData as $data) {
            $supplier = $suppliers->get($data['supplier_index'] % $suppliers->count());
            $orderDate = $now->copy()->subDays($data['days_ago'])->format('Y-m-d');
            $expectedDate = $now->copy()->subDays($data['days_ago'] - 2)->format('Y-m-d');

            $po = PurchaseOrder::updateOrCreate(
                ['order_number' => $data['order_number']],
                [
                    'vendor_id' => $supplier->id,
                    'created_by' => $gudang->id,
                    'order_date' => $orderDate,
                    'expected_date' => $expectedDate,
                    'status' => $data['status'],
                    'total_amount' => 0,
                    'notes' => $data['notes'],
                ]
            );

            $totalAmount = 0;
            foreach ($data['items'] as $item) {
                $ing = Ingredient::where('name', $item['ing_name'])->first()
                    ?? $ingredients->first();

                $subtotal = $item['price'] * $item['qty'];
                $totalAmount += $subtotal;

                PurchaseOrderDetail::updateOrCreate(
                    [
                        'purchase_order_id' => $po->id,
                        'ingredient_id' => $ing->id,
                    ],
                    [
                        'quantity' => $item['qty'],
                        'unit' => $item['unit'],
                        'price' => $item['price'],
                        'subtotal' => $subtotal,
                        'notes' => 'Harga acuan kontrak pasokan katering',
                    ]
                );
            }

            $po->update(['total_amount' => $totalAmount]);
        }
    }
}
