<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\IngredientStockTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class IngredientStockTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gudangUser = User::where('email', 'gudang@foodflow.test')->first()
            ?? User::first();

        $ingredients = Ingredient::where('is_active', true)->get();

        if ($ingredients->isEmpty() || ! $gudangUser) {
            return;
        }

        $now = Carbon::now();

        foreach ($ingredients as $i => $ingredient) {
            // 1. Transaksi Masuk (IN) awal bulan dari Supplier
            IngredientStockTransaction::firstOrCreate(
                [
                    'ingredient_id' => $ingredient->id,
                    'transaction_type' => 'in',
                    'transaction_date' => $now->copy()->startOfMonth()->addDays($i % 5)->format('Y-m-d'),
                ],
                [
                    'quantity' => round($ingredient->minimum_stock * 2.5, 2),
                    'reference_type' => 'MaterialReceipt',
                    'reference_id' => $i + 1,
                    'notes' => 'Penerimaan stok rutin awal bulan dari Supplier resmi FoodFlow',
                    'created_by' => $gudangUser->id,
                ]
            );

            // 2. Transaksi Keluar (OUT) pengeluaran bahan ke Dapur Produksi
            IngredientStockTransaction::firstOrCreate(
                [
                    'ingredient_id' => $ingredient->id,
                    'transaction_type' => 'out',
                    'transaction_date' => $now->copy()->startOfMonth()->addDays(6 + ($i % 5))->format('Y-m-d'),
                ],
                [
                    'quantity' => round($ingredient->minimum_stock * 1.2, 2),
                    'reference_type' => 'MaterialRequest',
                    'reference_id' => $i + 1,
                    'notes' => 'Pengeluaran bahan baku untuk batch masak katering harian',
                    'created_by' => $gudangUser->id,
                ]
            );

            // 3. Transaksi Koreksi Fisik (Adjustment) untuk sebagian bahan
            if ($i % 3 === 0) {
                IngredientStockTransaction::firstOrCreate(
                    [
                        'ingredient_id' => $ingredient->id,
                        'transaction_type' => 'adjustment',
                        'transaction_date' => $now->copy()->subDays(2)->format('Y-m-d'),
                    ],
                    [
                        'quantity' => $i % 2 === 0 ? 0.50 : -0.25,
                        'reference_type' => 'StockOpname',
                        'reference_id' => $i + 1,
                        'notes' => 'Penyesuaian selisih timbangan stock opname fisik gudang',
                        'created_by' => $gudangUser->id,
                    ]
                );
            }
        }
    }
}
