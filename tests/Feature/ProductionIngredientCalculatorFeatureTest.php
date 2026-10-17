<?php

namespace Tests\Feature;

use App\Enums\ProductionPlanStatus;
use App\Enums\UserRole;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\ProductionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionIngredientCalculatorFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $produksi;

    private User $gudang;

    private User $owner;

    private Ingredient $chicken;

    private Ingredient $rice;

    private Product $menuAyam;

    private Product $menuSup;

    private ProductionPlan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
        $this->produksi = User::factory()->create(['role' => UserRole::Produksi]);
        $this->gudang = User::factory()->create(['role' => UserRole::Gudang]);
        $this->owner = User::factory()->create(['role' => UserRole::Owner]);

        // Bahan baku
        $this->chicken = Ingredient::factory()->create([
            'name' => 'Dada Ayam Fillet Segar',
            'unit' => 'kg',
            'current_stock' => 12.00, // Hanya ada 12 kg
            'minimum_stock' => 5.00,
        ]);

        $this->rice = Ingredient::factory()->create([
            'name' => 'Beras Merah Organik',
            'unit' => 'kg',
            'current_stock' => 50.00, // Cukup 50 kg
            'minimum_stock' => 10.00,
        ]);

        // Menu 1: Nasi Ayam (100 porsi) -> butuh 15 kg ayam (150g/porsi) & 15 kg beras (150g/porsi)
        $this->menuAyam = Product::factory()->create([
            'name' => 'Nasi Beras Merah Ayam Panggang',
            'unit' => 'porsi',
            'is_active' => true,
        ]);
        $this->menuAyam->recipeItems()->create([
            'ingredient_id' => $this->chicken->id,
            'quantity_per_unit' => 150, // gram
            'unit' => 'gram',
        ]);
        $this->menuAyam->recipeItems()->create([
            'ingredient_id' => $this->rice->id,
            'quantity_per_unit' => 150, // gram
            'unit' => 'gram',
        ]);

        // Menu 2: Sup Kaldu Ayam (50 porsi) -> butuh 5 kg ayam (100g/porsi)
        $this->menuSup = Product::factory()->create([
            'name' => 'Sup Kaldu Ayam Sehat',
            'unit' => 'porsi',
            'is_active' => true,
        ]);
        $this->menuSup->recipeItems()->create([
            'ingredient_id' => $this->chicken->id,
            'quantity_per_unit' => 100, // gram
            'unit' => 'gram',
        ]);

        // Rencana produksi: 100 porsi menuAyam, 50 porsi menuSup
        // Total kebutuhan ayam: 15 kg + 5 kg = 20 kg. Stok: 12 kg -> Defisit 8 kg! (Kurang)
        // Total kebutuhan beras: 15 kg. Stok: 50 kg -> Proyeksi sisa 35 kg! (Aman)
        $this->plan = ProductionPlan::create([
            'plan_number' => 'PLN-20261017-0099',
            'plan_date' => '2026-10-17',
            'production_date' => '2026-10-18',
            'status' => ProductionPlanStatus::Scheduled,
            'created_by' => $this->produksi->id,
        ]);

        $this->plan->details()->create([
            'product_id' => $this->menuAyam->id,
            'planned_quantity' => 100,
        ]);

        $this->plan->details()->create([
            'product_id' => $this->menuSup->id,
            'planned_quantity' => 50,
        ]);
    }

    public function test_production_plan_show_displays_automatic_calculator_with_deficits(): void
    {
        $response = $this->actingAs($this->produksi)->get(route('production-plans.show', $this->plan));

        $response->assertOk();
        $response->assertSee('Kalkulasi Kebutuhan Bahan Baku dan Ketersediaan Stok Gudang');
        $response->assertSee('Dada Ayam Fillet Segar');
        $response->assertSee('Beras Merah Organik');

        // 10 Kolom Minimal
        $response->assertSee('Kebutuhan Total');
        $response->assertSee('Stok Aktual');
        $response->assertSee('Proyeksi Stok Akhir');
        $response->assertSee('Kekurangan Produksi');
        $response->assertSee('Minimum Stok');
        $response->assertSee('Status Kebutuhan');
        $response->assertSee('Status Stok Akhir');
        $response->assertSee('Cek Fisik');

        // Kebutuhan ayam = 20 kg, stok = 12 kg, defisit 8 kg
        $response->assertSee('20 kg');
        $response->assertSee('12 kg');
        $response->assertSee('-8 kg');
        $response->assertSee('8 kg');
        $response->assertSee('Kurang');

        // Kebutuhan beras = 15 kg, stok = 50 kg, sisa 35 kg
        $response->assertSee('15 kg');
        $response->assertSee('50 kg');
        $response->assertSee('+35 kg');
        $response->assertSee('Terpenuhi');
        $response->assertSee('Aman');

        // Status kesiapan produksi & kondisi persediaan
        $response->assertSee('Perlu Pengadaan');
        $response->assertSee('Defisit Stok');
    }

    public function test_plan_with_stock_below_minimum_shows_restock_warning_separated_from_readiness(): void
    {
        // Kondisi 2: Kebutuhan 18 kg, stok 20 kg, min 10 kg -> proyeksi 2 kg (< 10 kg).
        // Kesiapan produksi: Siap Produksi. Kondisi stok akhir: Restock Diperlukan.
        $beef = Ingredient::factory()->create([
            'name' => 'Daging Sapi Slice',
            'unit' => 'kg',
            'current_stock' => 20.00,
            'minimum_stock' => 10.00,
        ]);

        $menu = Product::factory()->create(['name' => 'Beef Teriyaki']);
        $menu->recipeItems()->create([
            'ingredient_id' => $beef->id,
            'quantity_per_unit' => 0.180,
            'unit' => 'kg',
        ]);

        $plan = ProductionPlan::create([
            'plan_number' => 'PLN-RESTOCK-001',
            'plan_date' => '2026-10-17',
            'production_date' => '2026-10-18',
            'status' => ProductionPlanStatus::Scheduled,
            'created_by' => $this->produksi->id,
        ]);

        $plan->details()->create([
            'product_id' => $menu->id,
            'planned_quantity' => 100, // 18 kg
        ]);

        $response = $this->actingAs($this->produksi)->get(route('production-plans.show', $plan));
        $response->assertOk();

        // Kesiapan Produksi = Siap Produksi (kebutuhan 18 kg terpenuhi oleh 20 kg)
        $response->assertSee('Siap Produksi');

        // Kondisi Persediaan Akhir = Restock Diperlukan (sisa 2 kg < min 10 kg)
        $response->assertSee('Restock Diperlukan');
        $response->assertSee('Di Bawah Minimum');
        $response->assertSee('+2 kg');
    }

    public function test_all_authenticated_roles_can_view_kitchen_worksheet(): void
    {
        foreach ([$this->admin, $this->produksi, $this->gudang, $this->owner] as $user) {
            $response = $this->actingAs($user)->get(route('production-plans.ingredients', $this->plan));

            $response->assertOk();
            $response->assertSee('Lembar Kerja Bahan Baku');
            $response->assertSee('PLN-20261017-0099');
            $response->assertSee('Dada Ayam Fillet Segar');
            $response->assertSee('Beras Merah Organik');
            $response->assertSee('Disiapkan Oleh (Staff Gudang):');
            $response->assertSee('Diterima Oleh (Tim Dapur):');
            $response->assertSee('Disetujui (Kepala Produksi / Ahli Gizi):');
        }
    }

    public function test_guest_is_redirected_from_ingredients_worksheet(): void
    {
        $response = $this->get(route('production-plans.ingredients', $this->plan));
        $response->assertRedirect(route('login'));
    }

    public function test_calculate_preview_json_endpoint_returns_accurate_results(): void
    {
        $payload = [
            'items' => [
                [
                    'product_id' => $this->menuAyam->id,
                    'planned_quantity' => 10, // 10 porsi -> butuh 1.5 kg ayam & 1.5 kg beras
                ],
            ],
        ];

        $response = $this->actingAs($this->produksi)->postJson(route('production-plans.calculate-preview'), $payload);

        $response->assertOk();
        $response->assertJsonStructure([
            'items' => [
                '*' => [
                    'ingredient_id',
                    'ingredient_name',
                    'required_quantity',
                    'current_stock',
                    'projected_ending_stock',
                    'production_shortage',
                    'availability_status',
                    'ending_stock_status',
                    'readiness_status',
                    'formatted_required',
                    'formatted_current_stock',
                    'menu_breakdown',
                ],
            ],
            'summary' => [
                'total_ingredients',
                'fulfilled_count',
                'shortage_count',
                'below_minimum_count',
                'safe_count',
                'production_readiness',
                'inventory_condition',
            ],
        ]);

        $data = $response->json();
        $this->assertEquals(2, $data['summary']['total_ingredients']);
        $this->assertEquals(0, $data['summary']['shortage_count']);
        $this->assertSame('ready', $data['summary']['production_readiness']['status']);
    }
}
