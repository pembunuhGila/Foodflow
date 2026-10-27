<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\ProductIngredient;
use App\Models\ProductionPlan;
use App\Models\ProductionPlanDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionMaterialUsageTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $produksi;

    private User $gudang;

    private User $owner;

    private ProductionPlan $plan;

    private Ingredient $ingredient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
        $this->produksi = User::factory()->create(['role' => UserRole::Produksi]);
        $this->gudang = User::factory()->create(['role' => UserRole::Gudang]);
        $this->owner = User::factory()->create(['role' => UserRole::Owner]);

        $this->ingredient = Ingredient::factory()->create([
            'name' => 'Bawang Putih Kating',
            'unit' => 'kg',
            'current_stock' => 15,
            'is_active' => true,
        ]);

        $product = Product::factory()->create([
            'name' => 'Sup Ayam Jamur Sehat',
            'is_active' => true,
        ]);

        ProductIngredient::create([
            'product_id' => $product->id,
            'ingredient_id' => $this->ingredient->id,
            'quantity' => 0.05,
            'unit' => 'kg',
        ]);

        $this->plan = ProductionPlan::create([
            'created_by' => $this->admin->id,
            'plan_number' => 'PLN-20261027-0001',
            'plan_date' => now()->format('Y-m-d'),
            'production_date' => now()->format('Y-m-d'),
            'status' => 'in_progress',
        ]);

        ProductionPlanDetail::create([
            'production_plan_id' => $this->plan->id,
            'product_id' => $product->id,
            'planned_quantity' => 100,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('production-plans.material-usages.create', $this->plan))
            ->assertRedirect(route('login'));
    }

    public function test_produksi_and_admin_can_access_create_form(): void
    {
        $this->actingAs($this->produksi)
            ->get(route('production-plans.material-usages.create', $this->plan))
            ->assertOk()
            ->assertSee('Bawang Putih Kating')
            ->assertSee('Pemakaian Riil');

        $this->actingAs($this->admin)
            ->get(route('production-plans.material-usages.create', $this->plan))
            ->assertOk();
    }

    public function test_gudang_and_owner_cannot_access_create_form(): void
    {
        $this->actingAs($this->gudang)
            ->get(route('production-plans.material-usages.create', $this->plan))
            ->assertForbidden();

        $this->actingAs($this->owner)
            ->get(route('production-plans.material-usages.create', $this->plan))
            ->assertForbidden();
    }

    public function test_all_roles_can_view_material_usages_evaluation_page(): void
    {
        foreach ([$this->admin, $this->produksi, $this->gudang, $this->owner] as $user) {
            $this->actingAs($user)
                ->get(route('production-plans.material-usages.show', $this->plan))
                ->assertOk()
                ->assertSee('Evaluasi Penggunaan Bahan Baku Riil');
        }
    }

    public function test_produksi_can_save_actual_material_usages_and_calculate_difference(): void
    {
        $payload = [
            'items' => [
                [
                    'ingredient_id' => $this->ingredient->id,
                    'planned_quantity' => 5.0, // 100 porsi x 0.05 kg = 5 kg
                    'actual_quantity' => 5.25, // Dapur memakai 5.25 kg (+0.25 kg lebih)
                    'unit' => 'kg',
                    'notes' => 'Tumpah 250gr saat proses blender bumbu',
                ],
            ],
        ];

        $response = $this->actingAs($this->produksi)
            ->post(route('production-plans.material-usages.store', $this->plan), $payload);

        $response->assertRedirect(route('production-plans.material-usages.show', $this->plan));

        $this->assertDatabaseHas('production_material_usages', [
            'production_plan_id' => $this->plan->id,
            'ingredient_id' => $this->ingredient->id,
            'planned_quantity' => 5.0,
            'actual_quantity' => 5.25,
            'difference_quantity' => 0.25,
            'notes' => 'Tumpah 250gr saat proses blender bumbu',
            'recorded_by' => $this->produksi->id,
        ]);
    }
}
