<?php

namespace Tests\Unit;

use App\Enums\ProductionPlanStatus;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\ProductionPlan;
use App\Models\User;
use App\Services\ProductionIngredientCalculator;
use App\Services\UnitConverter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionIngredientCalculatorTest extends TestCase
{
    use RefreshDatabase;

    private ProductionIngredientCalculator $calculator;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new ProductionIngredientCalculator(new UnitConverter);
        $this->user = User::factory()->create();
    }

    /**
     * Kondisi 1: Kebutuhan 18 kg, stok aktual 0 kg, minimum stok 10 kg.
     * Kekurangan: 18 kg.
     * Status ketersediaan: Kurang.
     * Status kesiapan produksi: Perlu Pengadaan.
     * Status stok akhir: Defisit.
     */
    public function test_condition_1_required_18_stock_0_minimum_10(): void
    {
        $ingredient = Ingredient::factory()->create([
            'name' => 'Daging Sapi Segar',
            'unit' => 'kg',
            'current_stock' => 0.00, // Valid 0 kg
            'minimum_stock' => 10.00,
        ]);

        $product = Product::factory()->create(['name' => 'Gulai Daging']);
        $product->recipeItems()->create([
            'ingredient_id' => $ingredient->id,
            'quantity_per_unit' => 0.180, // 0.18 kg per porsi
            'unit' => 'kg',
        ]);

        $plan = ProductionPlan::create([
            'plan_number' => 'PLN-TEST-0001',
            'plan_date' => '2026-10-17',
            'production_date' => '2026-10-18',
            'status' => ProductionPlanStatus::Scheduled,
            'created_by' => $this->user->id,
        ]);

        $plan->details()->create([
            'product_id' => $product->id,
            'planned_quantity' => 100, // 100 porsi x 0.18 kg = 18 kg
        ]);

        $result = $this->calculator->calculateForPlan($plan);
        $item = $result['items'][0];

        $this->assertEquals(18.0, $item['required_quantity']);
        $this->assertEquals(0.0, $item['current_stock']);
        $this->assertEquals(10.0, $item['minimum_stock']);
        $this->assertEquals(18.0, $item['production_shortage']);
        $this->assertEquals(-18.0, $item['projected_ending_stock']);
        $this->assertSame('shortage', $item['availability_status']);
        $this->assertSame('Kurang', $item['availability_label']);
        $this->assertSame('procurement_needed', $item['readiness_status']);
        $this->assertSame('Perlu Pengadaan', $item['readiness_label']);
        $this->assertSame('deficit', $item['ending_stock_status']);
        $this->assertSame('Defisit', $item['ending_stock_label']);
        $this->assertFalse($item['is_sufficient']);

        // Summary
        $this->assertSame('procurement_needed', $result['summary']['production_readiness']['status']);
        $this->assertSame('Perlu Pengadaan', $result['summary']['production_readiness']['label']);
        $this->assertSame('deficit', $result['summary']['inventory_condition']['status']);
    }

    /**
     * Kondisi 2: Kebutuhan 18 kg, stok aktual 20 kg, minimum stok 10 kg.
     * Kekurangan: 0 kg.
     * Proyeksi stok akhir: 2 kg.
     * Status ketersediaan: Terpenuhi.
     * Status stok akhir: Di Bawah Minimum.
     * Status kesiapan: Kebutuhan produksi terpenuhi, tetapi restock diperlukan.
     */
    public function test_condition_2_required_18_stock_20_minimum_10(): void
    {
        $ingredient = Ingredient::factory()->create([
            'name' => 'Dada Ayam',
            'unit' => 'kg',
            'current_stock' => 20.00,
            'minimum_stock' => 10.00,
        ]);

        $product = Product::factory()->create(['name' => 'Ayam Panggang']);
        $product->recipeItems()->create([
            'ingredient_id' => $ingredient->id,
            'quantity_per_unit' => 0.180,
            'unit' => 'kg',
        ]);

        $plan = ProductionPlan::create([
            'plan_number' => 'PLN-TEST-0002',
            'plan_date' => '2026-10-17',
            'production_date' => '2026-10-18',
            'status' => ProductionPlanStatus::Scheduled,
            'created_by' => $this->user->id,
        ]);

        $plan->details()->create([
            'product_id' => $product->id,
            'planned_quantity' => 100, // 18 kg
        ]);

        $result = $this->calculator->calculateForPlan($plan);
        $item = $result['items'][0];

        $this->assertEquals(18.0, $item['required_quantity']);
        $this->assertEquals(20.0, $item['current_stock']);
        $this->assertEquals(0.0, $item['production_shortage']);
        $this->assertEquals(2.0, $item['projected_ending_stock']);
        $this->assertSame('fulfilled', $item['availability_status']);
        $this->assertSame('Terpenuhi', $item['availability_label']);
        $this->assertSame('below_minimum', $item['ending_stock_status']);
        $this->assertSame('Di Bawah Minimum', $item['ending_stock_label']);
        $this->assertSame('restock_needed', $item['readiness_status']);
        $this->assertSame('Kebutuhan produksi terpenuhi, tetapi restock diperlukan', $item['readiness_label']);
        $this->assertTrue($item['is_sufficient']);

        // Summary: Produksi siap, tapi persediaan perlu restock
        $this->assertSame('ready', $result['summary']['production_readiness']['status']);
        $this->assertSame('Siap Produksi', $result['summary']['production_readiness']['label']);
        $this->assertSame('restock_needed', $result['summary']['inventory_condition']['status']);
        $this->assertSame('Restock Diperlukan', $result['summary']['inventory_condition']['label']);
    }

    /**
     * Kondisi 3: Kebutuhan 18 kg, stok aktual 30 kg, minimum stok 10 kg.
     * Proyeksi stok akhir: 12 kg.
     * Kekurangan: 0 kg.
     * Status ketersediaan: Terpenuhi.
     * Status stok akhir: Aman.
     * Status kesiapan: Siap Produksi.
     */
    public function test_condition_3_required_18_stock_30_minimum_10(): void
    {
        $ingredient = Ingredient::factory()->create([
            'name' => 'Beras Organik',
            'unit' => 'kg',
            'current_stock' => 30.00,
            'minimum_stock' => 10.00,
        ]);

        $product = Product::factory()->create(['name' => 'Nasi Kotak Sehat']);
        $product->recipeItems()->create([
            'ingredient_id' => $ingredient->id,
            'quantity_per_unit' => 0.180,
            'unit' => 'kg',
        ]);

        $plan = ProductionPlan::create([
            'plan_number' => 'PLN-TEST-0003',
            'plan_date' => '2026-10-17',
            'production_date' => '2026-10-18',
            'status' => ProductionPlanStatus::Scheduled,
            'created_by' => $this->user->id,
        ]);

        $plan->details()->create([
            'product_id' => $product->id,
            'planned_quantity' => 100, // 18 kg
        ]);

        $result = $this->calculator->calculateForPlan($plan);
        $item = $result['items'][0];

        $this->assertEquals(18.0, $item['required_quantity']);
        $this->assertEquals(30.0, $item['current_stock']);
        $this->assertEquals(0.0, $item['production_shortage']);
        $this->assertEquals(12.0, $item['projected_ending_stock']);
        $this->assertSame('fulfilled', $item['availability_status']);
        $this->assertSame('Terpenuhi', $item['availability_label']);
        $this->assertSame('safe', $item['ending_stock_status']);
        $this->assertSame('Aman', $item['ending_stock_label']);
        $this->assertSame('ready', $item['readiness_status']);
        $this->assertSame('Siap Produksi', $item['readiness_label']);
        $this->assertTrue($item['is_sufficient']);

        // Summary
        $this->assertSame('ready', $result['summary']['production_readiness']['status']);
        $this->assertSame('safe', $result['summary']['inventory_condition']['status']);
        $this->assertSame('Persediaan Aman', $result['summary']['inventory_condition']['label']);
    }

    /**
     * Kondisi 4: Kebutuhan 18 kg, stok aktual 28 kg, minimum stok 10 kg.
     * Proyeksi stok akhir: 10 kg.
     * Kekurangan: 0 kg.
     * Status stok akhir: Tepat di Batas Minimum.
     */
    public function test_condition_4_required_18_stock_28_minimum_10(): void
    {
        $ingredient = Ingredient::factory()->create([
            'name' => 'Minyak Zaitun',
            'unit' => 'liter',
            'current_stock' => 28.00,
            'minimum_stock' => 10.00,
        ]);

        $product = Product::factory()->create(['name' => 'Salad Mediterania']);
        $product->recipeItems()->create([
            'ingredient_id' => $ingredient->id,
            'quantity_per_unit' => 0.180,
            'unit' => 'liter',
        ]);

        $plan = ProductionPlan::create([
            'plan_number' => 'PLN-TEST-0004',
            'plan_date' => '2026-10-17',
            'production_date' => '2026-10-18',
            'status' => ProductionPlanStatus::Scheduled,
            'created_by' => $this->user->id,
        ]);

        $plan->details()->create([
            'product_id' => $product->id,
            'planned_quantity' => 100, // 18 liter
        ]);

        $result = $this->calculator->calculateForPlan($plan);
        $item = $result['items'][0];

        $this->assertEquals(18.0, $item['required_quantity']);
        $this->assertEquals(28.0, $item['current_stock']);
        $this->assertEquals(0.0, $item['production_shortage']);
        $this->assertEquals(10.0, $item['projected_ending_stock']);
        $this->assertSame('fulfilled', $item['availability_status']);
        $this->assertSame('Terpenuhi', $item['availability_label']);
        $this->assertSame('at_minimum', $item['ending_stock_status']);
        $this->assertSame('Tepat di Batas Minimum', $item['ending_stock_label']);
        $this->assertSame('critical_minimum', $item['readiness_status']);
        $this->assertTrue($item['is_sufficient']);

        // Summary
        $this->assertSame('ready', $result['summary']['production_readiness']['status']);
        $this->assertSame('restock_needed', $result['summary']['inventory_condition']['status']);
    }

    /**
     * Memastikan stok yang null / belum tercatat tidak diasumsikan 0 kg.
     */
    public function test_unrecorded_null_stock_is_not_treated_as_zero(): void
    {
        $ingredient = Ingredient::factory()->create([
            'name' => 'Bumbu Rempah Khusus',
            'unit' => 'kg',
            'current_stock' => null, // Data stok tidak ada
            'minimum_stock' => 5.00,
        ]);

        $product = Product::factory()->create(['name' => 'Kari Spesial']);
        $product->recipeItems()->create([
            'ingredient_id' => $ingredient->id,
            'quantity_per_unit' => 0.050,
            'unit' => 'kg',
        ]);

        $plan = ProductionPlan::create([
            'plan_number' => 'PLN-TEST-0005',
            'plan_date' => '2026-10-17',
            'production_date' => '2026-10-18',
            'status' => ProductionPlanStatus::Scheduled,
            'created_by' => $this->user->id,
        ]);

        $plan->details()->create([
            'product_id' => $product->id,
            'planned_quantity' => 20, // 1 kg
        ]);

        $result = $this->calculator->calculateForPlan($plan);
        $item = $result['items'][0];

        $this->assertEquals(1.0, $item['required_quantity']);
        $this->assertNull($item['current_stock']);
        $this->assertNull($item['projected_ending_stock']);
        $this->assertNull($item['production_shortage']);
        $this->assertFalse($item['is_stock_available']);
        $this->assertSame('unverified', $item['availability_status']);
        $this->assertSame('Data Tidak Tersedia', $item['availability_label']);
        $this->assertSame('Data Tidak Tersedia', $item['formatted_current_stock']);
    }

    /**
     * Beberapa menu yang menggunakan bahan yang sama dijumlahkan sebelum dibandingkan stok.
     */
    public function test_it_aggregates_the_same_ingredient_from_multiple_menus(): void
    {
        $chicken = Ingredient::factory()->create([
            'name' => 'Dada Ayam Segar',
            'unit' => 'kg',
            'current_stock' => 20.00,
            'minimum_stock' => 5.00,
        ]);

        $menu1 = Product::factory()->create(['name' => 'Ayam Bakar']);
        $menu1->recipeItems()->create([
            'ingredient_id' => $chicken->id,
            'quantity_per_unit' => 150, // 150 gram per porsi
            'unit' => 'gram',
        ]);

        $menu2 = Product::factory()->create(['name' => 'Sup Ayam']);
        $menu2->recipeItems()->create([
            'ingredient_id' => $chicken->id,
            'quantity_per_unit' => 100, // 100 gram per porsi
            'unit' => 'gram',
        ]);

        $plan = ProductionPlan::create([
            'plan_number' => 'PLN-TEST-0006',
            'plan_date' => '2026-10-17',
            'production_date' => '2026-10-18',
            'status' => ProductionPlanStatus::Scheduled,
            'created_by' => $this->user->id,
        ]);

        $plan->details()->create(['product_id' => $menu1->id, 'planned_quantity' => 100]); // 15 kg
        $plan->details()->create(['product_id' => $menu2->id, 'planned_quantity' => 50]);  // 5 kg

        $result = $this->calculator->calculateForPlan($plan);
        $item = $result['items'][0];

        // 15 kg + 5 kg = 20 kg
        $this->assertEquals(20.0, $item['required_quantity']);
        $this->assertEquals(20.0, $item['current_stock']);
        $this->assertEquals(0.0, $item['production_shortage']);
        $this->assertEquals(0.0, $item['projected_ending_stock']);
        $this->assertSame('fulfilled', $item['availability_status']);
        $this->assertCount(2, $item['menu_breakdown']);
    }

    /**
     * Konversi satuan gram ke kilogram.
     */
    public function test_gram_to_kilogram_conversion_in_recipe_is_calculated_correctly(): void
    {
        $ingredient = Ingredient::factory()->create([
            'name' => 'Wortel Brastagi',
            'unit' => 'kg',
            'current_stock' => 10.00,
        ]);

        $product = Product::factory()->create(['name' => 'Capcay Sehat']);
        $product->recipeItems()->create([
            'ingredient_id' => $ingredient->id,
            'quantity_per_unit' => 250, // 250 gram = 0.25 kg
            'unit' => 'gram',
        ]);

        $items = [
            [
                'product_id' => $product->id,
                'planned_quantity' => 40, // 40 porsi x 250 g = 10,000 g = 10 kg
            ],
        ];

        $result = $this->calculator->calculateForItems($items);
        $item = $result['items'][0];

        $this->assertEquals(10.0, $item['required_quantity']);
        $this->assertEquals(10.0, $item['current_stock']);
        $this->assertEquals(0.0, $item['production_shortage']);
        $this->assertEquals(0.0, $item['projected_ending_stock']);
    }
}
