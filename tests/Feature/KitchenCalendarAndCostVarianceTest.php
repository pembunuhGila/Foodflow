<?php

namespace Tests\Feature;

use App\Enums\ProductionPlanStatus;
use App\Enums\UserRole;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\ProductionMaterialUsage;
use App\Models\ProductionPlan;
use App\Models\ProductionPlanDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KitchenCalendarAndCostVarianceTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_roles_can_access_kitchen_production_calendar(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $produksi = User::factory()->create(['role' => UserRole::Produksi]);
        $gudang = User::factory()->create(['role' => UserRole::Gudang]);
        $owner = User::factory()->create(['role' => UserRole::Owner]);

        foreach ([$admin, $produksi, $gudang, $owner] as $user) {
            $response = $this->actingAs($user)->get(route('production-plans.calendar'));
            $response->assertOk()
                ->assertViewIs('production-plans.calendar')
                ->assertSee('Kalender Jadwal Masak Dapur');
        }
    }

    public function test_calendar_displays_production_plans_for_the_month(): void
    {
        $user = User::factory()->create(['role' => UserRole::Produksi]);

        $plan = ProductionPlan::create([
            'plan_number' => 'PLN-TEST-CAL-01',
            'plan_date' => now()->toDateString(),
            'production_date' => now()->toDateString(),
            'status' => ProductionPlanStatus::Scheduled,
            'notes' => 'Jadwal tes kalender dapur',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('production-plans.calendar', [
            'month' => now()->month,
            'year' => now()->year,
        ]));

        $response->assertOk()
            ->assertSee('PLN-TEST-CAL-01')
            ->assertSee('Dijadwalkan');
    }

    public function test_material_usage_page_displays_cost_variance_metrics(): void
    {
        $user = User::factory()->create(['role' => UserRole::Produksi]);

        $ingredient = Ingredient::create([
            'name' => 'Daging Sapi Has Luar',
            'category' => 'Protein Hewani',
            'unit' => 'kg',
            'minimum_stock' => 10,
            'current_stock' => 30,
            'calories' => 250,
            'protein' => 26,
            'fat' => 15,
            'carbohydrates' => 0,
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'Rendang Padang Spesial',
            'description' => 'Menu katering rendang',
            'unit' => 'porsi',
            'selling_price' => 35000,
            'is_active' => true,
        ]);

        $plan = ProductionPlan::create([
            'plan_number' => 'PLN-TEST-VAR-01',
            'plan_date' => now()->toDateString(),
            'production_date' => now()->toDateString(),
            'status' => ProductionPlanStatus::InProgress,
            'created_by' => $user->id,
        ]);

        ProductionPlanDetail::create([
            'production_plan_id' => $plan->id,
            'product_id' => $product->id,
            'planned_quantity' => 10,
        ]);

        ProductionMaterialUsage::create([
            'production_plan_id' => $plan->id,
            'ingredient_id' => $ingredient->id,
            'planned_quantity' => 2.0,
            'actual_quantity' => 2.5,
            'difference_quantity' => 0.5,
            'unit' => 'kg',
            'notes' => 'Penyusutan saat trimming lemak',
            'recorded_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('production-plans.material-usages.show', $plan));

        $response->assertOk()
            ->assertViewIs('production-plans.material-usages.show')
            ->assertSee('Estimasi Biaya Resep')
            ->assertSee('Realisasi Biaya Aktual')
            ->assertSee('Varians Efisiensi Biaya')
            ->assertSee('Daging Sapi Has Luar');
    }
}
