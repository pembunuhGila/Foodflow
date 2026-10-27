<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Ingredient;
use App\Models\IngredientStockTransaction;
use App\Models\User;
use App\Services\MaterialReceiptService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaterialReceiptStockInTest extends TestCase
{
    use RefreshDatabase;

    private User $gudang;

    private Ingredient $ingredient1;

    private Ingredient $ingredient2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gudang = User::factory()->create(['role' => UserRole::Gudang]);

        $this->ingredient1 = Ingredient::factory()->create([
            'name' => 'Beras Pandan Wangi',
            'unit' => 'kg',
            'current_stock' => 50.0,
            'is_active' => true,
        ]);

        $this->ingredient2 = Ingredient::factory()->create([
            'name' => 'Telur Ayam Negeri',
            'unit' => 'butir',
            'current_stock' => 100.0,
            'is_active' => true,
        ]);
    }

    public function test_only_good_condition_ingredients_trigger_stock_in_and_update_current_stock(): void
    {
        $service = app(MaterialReceiptService::class);

        $payload = [
            'receipt_date' => now()->format('Y-m-d'),
            'notes' => 'Penerimaan stok mingguan',
            'items' => [
                [
                    'ingredient_id' => $this->ingredient1->id,
                    'quantity' => 25.0,
                    'unit' => 'kg',
                    'condition' => 'baik', // Layak -> Masuk stok
                    'notes' => 'Kondisi karung rapi',
                ],
                [
                    'ingredient_id' => $this->ingredient2->id,
                    'quantity' => 10.0,
                    'unit' => 'butir',
                    'condition' => 'rusak', // Rusak/Pecah -> TIDAK boleh masuk stok (BR-04)
                    'notes' => '10 butir pecah saat pengiriman',
                ],
            ],
        ];

        $receipt = $service->recordReceipt($payload, $this->gudang);

        // 1. Baris transaksi mutasi stok masuk HANYA ada untuk ingredient1 (Beras)
        $this->assertDatabaseHas('ingredient_stock_transactions', [
            'ingredient_id' => $this->ingredient1->id,
            'transaction_type' => 'in',
            'quantity' => 25.0,
            'reference_type' => 'material_receipt',
            'reference_id' => $receipt->id,
            'created_by' => $this->gudang->id,
        ]);

        // 2. Baris transaksi mutasi stok TIDAK ADA untuk ingredient2 (Telur pecah)
        $this->assertDatabaseMissing('ingredient_stock_transactions', [
            'ingredient_id' => $this->ingredient2->id,
            'reference_id' => $receipt->id,
        ]);

        // 3. Saldo current_stock beras bertambah dari 50 menjadi 75
        $this->assertEquals(75.0, (float) $this->ingredient1->fresh()->current_stock);

        // 4. Saldo current_stock telur tetap 100 (tidak bertambah)
        $this->assertEquals(100.0, (float) $this->ingredient2->fresh()->current_stock);
    }

    public function test_mismatched_and_expired_conditions_never_increase_stock(): void
    {
        $service = app(MaterialReceiptService::class);

        $payload = [
            'receipt_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'ingredient_id' => $this->ingredient1->id,
                    'quantity' => 5.0,
                    'unit' => 'kg',
                    'condition' => 'kedaluwarsa',
                ],
                [
                    'ingredient_id' => $this->ingredient1->id,
                    'quantity' => 10.0,
                    'unit' => 'kg',
                    'condition' => 'tidak_sesuai',
                ],
            ],
        ];

        $receipt = $service->recordReceipt($payload, $this->gudang);

        // Tidak ada transaksi masuk sama sekali
        $this->assertEquals(0, IngredientStockTransaction::where('reference_id', $receipt->id)->count());

        // Saldo tetap 50
        $this->assertEquals(50.0, (float) $this->ingredient1->fresh()->current_stock);
    }
}
