<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Ingredient;
use App\Models\IngredientStockTransaction;
use App\Models\Product;
use App\Models\ProductionPlan;
use App\Models\ProductionPlanDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerReportPrintTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $owner;

    private User $gudang;

    private User $produksi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
        $this->owner = User::factory()->create(['role' => UserRole::Owner]);
        $this->gudang = User::factory()->create(['role' => UserRole::Gudang]);
        $this->produksi = User::factory()->create(['role' => UserRole::Produksi]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('owner.nutrition.index'))->assertRedirect(route('login'));
        $this->get(route('owner.reports.index'))->assertRedirect(route('login'));
        $this->get(route('owner.reports.production'))->assertRedirect(route('login'));
        $this->get(route('owner.reports.inventory'))->assertRedirect(route('login'));
    }

    public function test_owner_and_admin_can_access_nutrition_and_reports(): void
    {
        foreach ([$this->owner, $this->admin] as $user) {
            $this->actingAs($user)
                ->get(route('owner.nutrition.index'))
                ->assertOk()
                ->assertSee('Evaluasi Kepatuhan Gizi');

            $this->actingAs($user)
                ->get(route('owner.reports.index'))
                ->assertOk()
                ->assertSee('Pusat Cetak Laporan');

            $this->actingAs($user)
                ->get(route('owner.reports.production'))
                ->assertOk()
                ->assertSeeText('LAPORAN REKAPITULASI CAPAIAN PRODUKSI MASAK');

            $this->actingAs($user)
                ->get(route('owner.reports.inventory'))
                ->assertOk()
                ->assertSeeText('LAPORAN MUTASI & PERSEDIAAN BAHAN BAKU GUDANG');
        }
    }

    public function test_gudang_and_produksi_are_forbidden_from_owner_reports(): void
    {
        foreach ([$this->gudang, $this->produksi] as $user) {
            $this->actingAs($user)
                ->get(route('owner.nutrition.index'))
                ->assertForbidden();

            $this->actingAs($user)
                ->get(route('owner.reports.index'))
                ->assertForbidden();

            $this->actingAs($user)
                ->get(route('owner.reports.production'))
                ->assertForbidden();

            $this->actingAs($user)
                ->get(route('owner.reports.inventory'))
                ->assertForbidden();
        }
    }

    public function test_nutrition_page_shows_product_nutritional_profile(): void
    {
        $product = Product::factory()->create([
            'name' => 'Paket Nasi Kotak Ayam Panggang Madu',
            'calories' => 620.5,
            'protein' => 28.0,
            'fat' => 18.5,
            'carbohydrates' => 75.0,
            'dietary_fiber' => 6.2,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->owner)
            ->get(route('owner.nutrition.index'));

        $response->assertOk()
            ->assertSee($product->name)
            ->assertSee('620,5')
            ->assertSee('28,0');
    }

    public function test_production_report_aggregates_totals_and_achievement(): void
    {
        $product = Product::factory()->create(['name' => 'Menu MBG Bergizi']);

        $plan = ProductionPlan::create([
            'created_by' => $this->admin->id,
            'plan_number' => 'PLN-20261027-0099',
            'plan_date' => now()->format('Y-m-d'),
            'production_date' => now()->format('Y-m-d'),
            'status' => 'completed',
        ]);

        ProductionPlanDetail::create([
            'production_plan_id' => $plan->id,
            'product_id' => $product->id,
            'planned_quantity' => 100,
        ]);

        $response = $this->actingAs($this->owner)
            ->get(route('owner.reports.production', [
                'from_date' => now()->startOfMonth()->format('Y-m-d'),
                'to_date' => now()->endOfMonth()->format('Y-m-d'),
            ]));

        $response->assertOk()
            ->assertSee($plan->plan_number)
            ->assertSee('100');
    }

    public function test_inventory_report_aggregates_stock_transactions(): void
    {
        $ingredient = Ingredient::factory()->create([
            'name' => 'Minyak Kelapa Murni',
            'unit' => 'liter',
            'current_stock' => 80,
            'is_active' => true,
        ]);

        IngredientStockTransaction::create([
            'ingredient_id' => $ingredient->id,
            'transaction_type' => 'in',
            'quantity' => 100,
            'transaction_date' => now()->format('Y-m-d'),
            'reference_type' => 'material_receipt',
            'notes' => 'Penerimaan supplier',
            'created_by' => $this->admin->id,
        ]);

        IngredientStockTransaction::create([
            'ingredient_id' => $ingredient->id,
            'transaction_type' => 'out',
            'quantity' => 20,
            'transaction_date' => now()->format('Y-m-d'),
            'reference_type' => 'material_request',
            'notes' => 'Dikeluarkan ke dapur',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->owner)
            ->get(route('owner.reports.inventory', [
                'from_date' => now()->startOfMonth()->format('Y-m-d'),
                'to_date' => now()->endOfMonth()->format('Y-m-d'),
            ]));

        $response->assertOk()
            ->assertSee($ingredient->name)
            ->assertSee('+100,00')
            ->assertSee('-20,00')
            ->assertSee('80,00');
    }
}
