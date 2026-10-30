<?php

namespace Tests\Feature;

use App\Enums\ProductionPlanStatus;
use App\Enums\UserRole;
use App\Models\Product;
use App\Models\ProductionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionPlanTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $produksi;

    private User $gudang;

    private User $owner;

    private Product $product1;

    private Product $product2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
        $this->produksi = User::factory()->create(['role' => UserRole::Produksi]);
        $this->gudang = User::factory()->create(['role' => UserRole::Gudang]);
        $this->owner = User::factory()->create(['role' => UserRole::Owner]);

        $this->product1 = Product::factory()->create([
            'name' => 'Nasi Beras Merah Ayam Panggang',
            'unit' => 'porsi',
            'selling_price' => 25000,
            'is_active' => true,
        ]);

        $this->product2 = Product::factory()->create([
            'name' => 'Tumis Sayur Pelangi Tempe Organik',
            'unit' => 'porsi',
            'selling_price' => 18000,
            'is_active' => true,
        ]);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('production-plans.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('production-plans.create'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_and_produksi_can_view_production_plans_index(): void
    {
        $plan = ProductionPlan::create([
            'plan_number' => 'PLN-20261015-0001',
            'plan_date' => '2026-10-15',
            'production_date' => '2026-10-16',
            'status' => ProductionPlanStatus::Scheduled,
            'created_by' => $this->produksi->id,
        ]);

        foreach ([$this->admin, $this->produksi] as $user) {
            $response = $this->actingAs($user)->get(route('production-plans.index'));
            $response->assertOk();
            $response->assertSee('PLN-20261015-0001');
        }

        foreach ([$this->gudang, $this->owner] as $user) {
            $response = $this->actingAs($user)->get(route('production-plans.index'));
            $response->assertForbidden();
        }
    }

    public function test_all_authenticated_roles_can_view_production_plan_show(): void
    {
        $plan = ProductionPlan::create([
            'plan_number' => 'PLN-20261015-0002',
            'plan_date' => '2026-10-15',
            'production_date' => '2026-10-16',
            'status' => ProductionPlanStatus::Scheduled,
            'created_by' => $this->produksi->id,
        ]);

        $plan->details()->create([
            'product_id' => $this->product1->id,
            'planned_quantity' => 120,
        ]);

        foreach ([$this->admin, $this->produksi, $this->gudang, $this->owner] as $user) {
            $response = $this->actingAs($user)->get(route('production-plans.show', $plan));
            $response->assertOk();
            $response->assertSee('PLN-20261015-0002');
            $response->assertSee('Nasi Beras Merah Ayam Panggang');
            $response->assertSee('120');
        }
    }

    public function test_admin_and_produksi_can_access_create_form(): void
    {
        $response = $this->actingAs($this->produksi)->get(route('production-plans.create'));
        $response->assertOk();
        $response->assertSee('Susun Rencana Produksi & Jadwal Masak');

        $response = $this->actingAs($this->admin)->get(route('production-plans.create'));
        $response->assertOk();
    }

    public function test_gudang_and_owner_are_forbidden_from_create_form(): void
    {
        $response = $this->actingAs($this->gudang)->get(route('production-plans.create'));
        $response->assertForbidden();

        $response = $this->actingAs($this->owner)->get(route('production-plans.create'));
        $response->assertForbidden();
    }

    public function test_produksi_can_create_production_plan_with_multiple_items(): void
    {
        $payload = [
            'plan_date' => '2026-10-15',
            'production_date' => '2026-10-16',
            'notes' => 'Produksi katering sehat shift pagi RS Mitra Sehat',
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'planned_quantity' => 100,
                    'notes' => 'Tanpa cabai',
                ],
                [
                    'product_id' => $this->product2->id,
                    'planned_quantity' => 80,
                    'notes' => 'Rendah garam',
                ],
            ],
        ];

        $response = $this->actingAs($this->produksi)->post(route('production-plans.store'), $payload);

        $plan = ProductionPlan::latest('id')->first();
        $this->assertNotNull($plan);
        $response->assertRedirect(route('production-plans.show', $plan));

        $this->assertDatabaseHas('production_plans', [
            'id' => $plan->id,
            'status' => 'scheduled',
            'created_by' => $this->produksi->id,
            'notes' => 'Produksi katering sehat shift pagi RS Mitra Sehat',
        ]);

        $this->assertEquals('2026-10-15', $plan->fresh()->plan_date->format('Y-m-d'));
        $this->assertEquals('2026-10-16', $plan->fresh()->production_date->format('Y-m-d'));

        $this->assertDatabaseHas('production_plan_details', [
            'production_plan_id' => $plan->id,
            'product_id' => $this->product1->id,
            'planned_quantity' => 100,
            'notes' => 'Tanpa cabai',
        ]);

        $this->assertDatabaseHas('production_plan_details', [
            'production_plan_id' => $plan->id,
            'product_id' => $this->product2->id,
            'planned_quantity' => 80,
            'notes' => 'Rendah garam',
        ]);

        $this->assertEquals(180, $plan->totalPlannedQuantity());
        $this->assertStringStartsWith('PLN-20261015-', $plan->plan_number);
    }

    public function test_validation_fails_when_required_fields_are_missing(): void
    {
        $response = $this->actingAs($this->produksi)->post(route('production-plans.store'), []);

        $response->assertSessionHasErrors(['plan_date', 'production_date', 'items']);
    }

    public function test_validation_fails_when_production_date_is_before_plan_date(): void
    {
        $payload = [
            'plan_date' => '2026-10-20',
            'production_date' => '2026-10-18', // Sebelum plan_date
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'planned_quantity' => 50,
                ],
            ],
        ];

        $response = $this->actingAs($this->produksi)->post(route('production-plans.store'), $payload);

        $response->assertSessionHasErrors(['production_date']);
    }

    public function test_validation_fails_when_quantity_is_zero_or_negative(): void
    {
        $payload = [
            'plan_date' => '2026-10-15',
            'production_date' => '2026-10-15',
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'planned_quantity' => 0,
                ],
            ],
        ];

        $response = $this->actingAs($this->produksi)->post(route('production-plans.store'), $payload);

        $response->assertSessionHasErrors(['items.0.planned_quantity']);
    }

    public function test_validation_fails_when_duplicate_products_are_selected(): void
    {
        $payload = [
            'plan_date' => '2026-10-15',
            'production_date' => '2026-10-15',
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'planned_quantity' => 25,
                ],
                [
                    'product_id' => $this->product1->id, // Duplikat
                    'planned_quantity' => 30,
                ],
            ],
        ];

        $response = $this->actingAs($this->produksi)->post(route('production-plans.store'), $payload);

        $response->assertSessionHasErrors(['items.0.product_id', 'items.1.product_id']);
    }

    public function test_admin_and_produksi_can_update_production_plan(): void
    {
        $plan = ProductionPlan::create([
            'plan_number' => 'PLN-20261015-0010',
            'plan_date' => '2026-10-15',
            'production_date' => '2026-10-16',
            'status' => ProductionPlanStatus::Draft,
            'created_by' => $this->admin->id,
        ]);

        $plan->details()->create([
            'product_id' => $this->product1->id,
            'planned_quantity' => 50,
        ]);

        $updatePayload = [
            'plan_date' => '2026-10-15',
            'production_date' => '2026-10-17',
            'status' => ProductionPlanStatus::InProgress->value,
            'notes' => 'Diperbarui untuk shift siang',
            'items' => [
                [
                    'product_id' => $this->product2->id,
                    'planned_quantity' => 75,
                    'notes' => 'Kemasan bento premium',
                ],
            ],
        ];

        $response = $this->actingAs($this->produksi)->put(route('production-plans.update', $plan), $updatePayload);

        $response->assertRedirect(route('production-plans.show', $plan));

        $this->assertDatabaseHas('production_plans', [
            'id' => $plan->id,
            'status' => 'in_progress',
            'notes' => 'Diperbarui untuk shift siang',
        ]);

        $this->assertEquals('2026-10-17', $plan->fresh()->production_date->format('Y-m-d'));

        $this->assertDatabaseMissing('production_plan_details', [
            'product_id' => $this->product1->id,
        ]);

        $this->assertDatabaseHas('production_plan_details', [
            'production_plan_id' => $plan->id,
            'product_id' => $this->product2->id,
            'planned_quantity' => 75,
            'notes' => 'Kemasan bento premium',
        ]);
    }

    public function test_gudang_and_owner_cannot_update_or_delete_plan(): void
    {
        $plan = ProductionPlan::create([
            'plan_number' => 'PLN-20261015-0020',
            'plan_date' => '2026-10-15',
            'production_date' => '2026-10-16',
            'status' => ProductionPlanStatus::Scheduled,
            'created_by' => $this->produksi->id,
        ]);

        $response = $this->actingAs($this->gudang)->put(route('production-plans.update', $plan), []);
        $response->assertForbidden();

        $response = $this->actingAs($this->owner)->delete(route('production-plans.destroy', $plan));
        $response->assertForbidden();
    }

    public function test_admin_and_produksi_can_delete_production_plan(): void
    {
        $plan = ProductionPlan::create([
            'plan_number' => 'PLN-20261015-0030',
            'plan_date' => '2026-10-15',
            'production_date' => '2026-10-16',
            'status' => ProductionPlanStatus::Draft,
            'created_by' => $this->produksi->id,
        ]);

        $plan->details()->create([
            'product_id' => $this->product1->id,
            'planned_quantity' => 40,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('production-plans.destroy', $plan));

        $response->assertRedirect(route('production-plans.index'));

        $this->assertDatabaseMissing('production_plans', ['id' => $plan->id]);
        $this->assertDatabaseMissing('production_plan_details', ['production_plan_id' => $plan->id]);
    }
}
