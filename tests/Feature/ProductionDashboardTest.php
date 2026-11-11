<?php

namespace Tests\Feature;

use App\Enums\ProductionPlanStatus;
use App\Enums\UserRole;
use App\Models\ProductionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_production_dashboard_displays_latest_five_active_plans_ordered_by_date_desc(): void
    {
        $user = User::factory()->create(['role' => UserRole::Produksi]);

        // Buat 7 rencana produksi dengan tanggal berturut-turut
        $oldestPlan = ProductionPlan::create([
            'plan_number' => 'PLN-OLD-001',
            'plan_date' => '2026-10-01',
            'production_date' => '2026-10-02',
            'status' => ProductionPlanStatus::Scheduled,
            'created_by' => $user->id,
        ]);

        $secondOldest = ProductionPlan::create([
            'plan_number' => 'PLN-OLD-002',
            'plan_date' => '2026-10-02',
            'production_date' => '2026-10-03',
            'status' => ProductionPlanStatus::Scheduled,
            'created_by' => $user->id,
        ]);

        // 5 rencana terbaru
        $recentPlans = [];
        for ($i = 1; $i <= 5; $i++) {
            $recentPlans[] = ProductionPlan::create([
                'plan_number' => sprintf('PLN-NEW-%03d', $i),
                'plan_date' => '2026-10-10',
                'production_date' => '2026-10-'.(10 + $i),
                'status' => ProductionPlanStatus::Scheduled,
                'created_by' => $user->id,
            ]);
        }

        $response = $this->actingAs($user)->get(route('produksi.dashboard'));

        $response->assertOk();
        // 5 rencana terbaru harus tampil
        foreach ($recentPlans as $plan) {
            $response->assertSee($plan->plan_number);
        }

        // Rencana lama (yang tidak masuk 5 terbaru) tidak boleh tampil di tabel limit
        $response->assertDontSee($oldestPlan->plan_number);
        $response->assertDontSee($secondOldest->plan_number);
    }
}
