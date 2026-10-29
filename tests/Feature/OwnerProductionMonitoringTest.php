<?php

namespace Tests\Feature;

use App\Enums\ProductionPlanStatus;
use App\Enums\UserRole;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Production;
use App\Models\ProductionMaterialUsage;
use App\Models\ProductionPlan;
use App\Models\ProductionPlanDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class OwnerProductionMonitoringTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $admin;

    private User $gudang;

    private User $produksi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create([
            'role' => UserRole::Owner,
            'status' => 'aktif',
        ]);

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin,
            'status' => 'aktif',
        ]);

        $this->gudang = User::factory()->create([
            'role' => UserRole::Gudang,
            'status' => 'aktif',
        ]);

        $this->produksi = User::factory()->create([
            'role' => UserRole::Produksi,
            'status' => 'aktif',
        ]);
    }

    public function test_guest_is_redirected_to_login_when_accessing_production_monitoring(): void
    {
        $response = $this->get(route('owner.production-monitoring.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_owner_can_access_production_monitoring_dashboard(): void
    {
        $response = $this->actingAs($this->owner)->get(route('owner.production-monitoring.index'));

        $response->assertOk();
        $response->assertSeeText('Monitoring Capaian Produksi Dapur (Live)');
        $response->assertSeeText('FR-37 & UC-10');
    }

    public function test_admin_can_access_production_monitoring_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('owner.production-monitoring.index'));

        $response->assertOk();
        $response->assertSeeText('Monitoring Capaian Produksi Dapur (Live)');
    }

    public function test_gudang_cannot_access_production_monitoring_dashboard(): void
    {
        $response = $this->actingAs($this->gudang)->get(route('owner.production-monitoring.index'));

        $response->assertForbidden();
    }

    public function test_produksi_cannot_access_production_monitoring_dashboard(): void
    {
        $response = $this->actingAs($this->produksi)->get(route('owner.production-monitoring.index'));

        $response->assertForbidden();
    }

    public function test_monitoring_displays_planned_and_actual_portions_and_efficiency(): void
    {
        $today = Carbon::today();

        $plan = ProductionPlan::create([
            'plan_number' => 'PP-'.$today->format('Ymd').'-0001',
            'plan_date' => $today,
            'production_date' => $today,
            'status' => ProductionPlanStatus::InProgress,
            'created_by' => $this->produksi->id,
        ]);

        $product = Product::factory()->create(['name' => 'Nasi Kotak Ayam Panggang']);

        $detail = ProductionPlanDetail::create([
            'production_plan_id' => $plan->id,
            'product_id' => $product->id,
            'planned_quantity' => 100,
        ]);

        Production::create([
            'production_number' => 'PRD-'.$today->format('Ymd').'-0001',
            'production_plan_detail_id' => $detail->id,
            'created_by' => $this->produksi->id,
            'production_date' => $today,
            'actual_quantity' => 85,
            'status' => 'partial',
        ]);

        $response = $this->actingAs($this->owner)->get(route('owner.production-monitoring.index', ['period' => 'today']));

        $response->assertOk();
        $response->assertSeeText('85');
        $response->assertSeeText('100 Porsi Target');
        $response->assertSeeText('85%');
        $response->assertSeeText('Nasi Kotak Ayam Panggang');
        $response->assertSeeText('PP-'.$today->format('Ymd').'-0001');
    }

    public function test_monitoring_filters_by_period(): void
    {
        $today = Carbon::today();
        $pastDate = $today->copy()->subDays(20);

        $planToday = ProductionPlan::create([
            'plan_number' => 'PP-'.$today->format('Ymd').'-0099',
            'plan_date' => $today,
            'production_date' => $today,
            'status' => ProductionPlanStatus::InProgress,
            'created_by' => $this->produksi->id,
        ]);

        $planPast = ProductionPlan::create([
            'plan_number' => 'PP-'.$pastDate->format('Ymd').'-0088',
            'plan_date' => $pastDate,
            'production_date' => $pastDate,
            'status' => ProductionPlanStatus::Completed,
            'created_by' => $this->produksi->id,
        ]);

        // When period is today, only planToday is visible
        $responseToday = $this->actingAs($this->owner)->get(route('owner.production-monitoring.index', ['period' => 'today']));
        $responseToday->assertOk();
        $responseToday->assertSeeText($planToday->plan_number);
        $responseToday->assertDontSeeText($planPast->plan_number);

        // When period is all, both are visible
        $responseAll = $this->actingAs($this->owner)->get(route('owner.production-monitoring.index', ['period' => 'all']));
        $responseAll->assertOk();
        $responseAll->assertSeeText($planToday->plan_number);
        $responseAll->assertSeeText($planPast->plan_number);
    }

    public function test_monitoring_filters_by_status(): void
    {
        $today = Carbon::today();

        $planScheduled = ProductionPlan::create([
            'plan_number' => 'PP-'.$today->format('Ymd').'-0101',
            'plan_date' => $today,
            'production_date' => $today,
            'status' => ProductionPlanStatus::Scheduled,
            'created_by' => $this->produksi->id,
        ]);

        $planCompleted = ProductionPlan::create([
            'plan_number' => 'PP-'.$today->format('Ymd').'-0102',
            'plan_date' => $today,
            'production_date' => $today,
            'status' => ProductionPlanStatus::Completed,
            'created_by' => $this->produksi->id,
        ]);

        $response = $this->actingAs($this->owner)->get(route('owner.production-monitoring.index', [
            'period' => 'today',
            'status' => 'completed',
        ]));

        $response->assertOk();
        $response->assertSeeText($planCompleted->plan_number);
        $response->assertDontSeeText($planScheduled->plan_number);
    }

    public function test_monitoring_displays_material_usage_efficiency_variance(): void
    {
        $today = Carbon::today();

        $plan = ProductionPlan::create([
            'plan_number' => 'PP-'.$today->format('Ymd').'-0201',
            'plan_date' => $today,
            'production_date' => $today,
            'status' => ProductionPlanStatus::Completed,
            'created_by' => $this->produksi->id,
        ]);

        $ingredient = Ingredient::factory()->create(['name' => 'Beras Pandan Wangi']);

        // Planned 10 kg, actual 9.5 kg => -0.5 kg (hemat)
        ProductionMaterialUsage::create([
            'production_plan_id' => $plan->id,
            'ingredient_id' => $ingredient->id,
            'planned_quantity' => 10.0,
            'actual_quantity' => 9.5,
            'difference_quantity' => -0.5,
            'unit' => 'kg',
            'recorded_by' => $this->produksi->id,
        ]);

        $response = $this->actingAs($this->owner)->get(route('owner.production-monitoring.index', ['period' => 'today']));

        $response->assertOk();
        $response->assertSeeText('0.50');
        $response->assertSeeText('Unit Lebih Hemat');
        $response->assertSeeText('Hemat 0.50 Unit');
    }
}
