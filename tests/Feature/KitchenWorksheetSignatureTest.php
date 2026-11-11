<?php

namespace Tests\Feature;

use App\Enums\ProductionPlanStatus;
use App\Enums\UserRole;
use App\Models\ProductionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KitchenWorksheetSignatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_kitchen_worksheet_contains_manual_signature_inputs(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Produksi,
            'name' => 'Budi Staf Dapur',
        ]);

        $plan = ProductionPlan::create([
            'plan_number' => 'PLN-TEST-SIG-001',
            'plan_date' => '2026-10-20',
            'production_date' => '2026-10-21',
            'status' => ProductionPlanStatus::Scheduled,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('production-plans.ingredients', $plan));

        $response->assertOk();
        // Kolom tanda tangan manual
        $response->assertSee('Disiapkan Oleh (Staff Gudang):');
        $response->assertSee('Diterima Oleh (Tim Dapur):');
        $response->assertSee('Disetujui (Kepala Produksi / Ahli Gizi):');

        // Input text untuk nama manual
        $response->assertSee('placeholder="Nama Staff Gudang"', false);
        $response->assertSee('placeholder="Nama Tim Dapur"', false);
        $response->assertSee('placeholder="Nama Kepala Produksi"', false);
    }
}
