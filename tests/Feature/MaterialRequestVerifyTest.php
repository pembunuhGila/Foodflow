<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Ingredient;
use App\Models\IngredientStockTransaction;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestDetail;
use App\Models\Product;
use App\Models\ProductionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaterialRequestVerifyTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $gudang;

    private User $produksi;

    private User $owner;

    private Ingredient $ingredient1;

    private Ingredient $ingredient2;

    private ProductionPlan $productionPlan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
        $this->gudang = User::factory()->create(['role' => UserRole::Gudang]);
        $this->produksi = User::factory()->create(['role' => UserRole::Produksi]);
        $this->owner = User::factory()->create(['role' => UserRole::Owner]);

        $this->ingredient1 = Ingredient::factory()->create([
            'name' => 'Beras Organik',
            'unit' => 'kg',
            'current_stock' => 50.00,
            'minimum_stock' => 10.00,
            'is_active' => true,
        ]);

        $this->ingredient2 = Ingredient::factory()->create([
            'name' => 'Daging Ayam Fillet',
            'unit' => 'kg',
            'current_stock' => 30.00,
            'minimum_stock' => 5.00,
            'is_active' => true,
        ]);

        $this->productionPlan = ProductionPlan::create([
            'plan_number' => 'PLN-20261018-0001',
            'plan_date' => '2026-10-18',
            'production_date' => '2026-10-19',
            'status' => 'scheduled',
            'created_by' => $this->produksi->id,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $materialRequest = MaterialRequest::factory()->create([
            'production_plan_id' => $this->productionPlan->id,
            'requested_by' => $this->produksi->id,
            'status' => 'pending',
        ]);

        $this->get(route('material-requests.index'))->assertRedirect(route('login'));
        $this->get(route('material-requests.show', $materialRequest))->assertRedirect(route('login'));
        $this->post(route('material-requests.verify', $materialRequest), [])->assertRedirect(route('login'));
    }

    public function test_gudang_and_admin_can_view_index_and_show(): void
    {
        $materialRequest = MaterialRequest::factory()->create([
            'production_plan_id' => $this->productionPlan->id,
            'requested_by' => $this->produksi->id,
            'status' => 'pending',
        ]);

        MaterialRequestDetail::factory()->create([
            'material_request_id' => $materialRequest->id,
            'ingredient_id' => $this->ingredient1->id,
            'requested_quantity' => 10,
            'unit' => 'kg',
        ]);

        // Gudang
        $this->actingAs($this->gudang)
            ->get(route('material-requests.index'))
            ->assertOk()
            ->assertSee($this->productionPlan->plan_number);

        $this->actingAs($this->gudang)
            ->get(route('material-requests.show', $materialRequest))
            ->assertOk()
            ->assertSee('Beras Organik');

        // Admin
        $this->actingAs($this->admin)
            ->get(route('material-requests.index'))
            ->assertOk()
            ->assertSee($this->productionPlan->plan_number);

        $this->actingAs($this->admin)
            ->get(route('material-requests.show', $materialRequest))
            ->assertOk()
            ->assertSee('Beras Organik');
    }

    public function test_all_authenticated_roles_can_view_index_and_show(): void
    {
        $materialRequest = MaterialRequest::factory()->create([
            'production_plan_id' => $this->productionPlan->id,
            'requested_by' => $this->produksi->id,
            'status' => 'pending',
        ]);

        foreach ([$this->admin, $this->gudang, $this->produksi, $this->owner] as $user) {
            $this->actingAs($user)
                ->get(route('material-requests.index'))
                ->assertOk();

            $this->actingAs($user)
                ->get(route('material-requests.show', $materialRequest))
                ->assertOk();
        }
    }

    public function test_produksi_can_submit_material_request_from_production_plan(): void
    {
        $product = Product::factory()->create(['name' => 'Ayam Penyet Sambal Korek']);
        $product->recipeItems()->create([
            'ingredient_id' => $this->ingredient1->id,
            'quantity_per_unit' => 0.200,
            'unit' => 'kg',
        ]);

        $this->productionPlan->details()->create([
            'product_id' => $product->id,
            'planned_quantity' => 50,
        ]);

        $response = $this->actingAs($this->produksi)
            ->post(route('production-plans.material-requests.store', $this->productionPlan));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Permintaan bahan tersimpan dengan status 'pending'
        $this->assertDatabaseHas('material_requests', [
            'production_plan_id' => $this->productionPlan->id,
            'requested_by' => $this->produksi->id,
            'status' => 'pending',
        ]);

        $mr = MaterialRequest::where('production_plan_id', $this->productionPlan->id)->first();
        $this->assertNotNull($mr);
        $this->assertDatabaseHas('material_request_details', [
            'material_request_id' => $mr->id,
            'ingredient_id' => $this->ingredient1->id,
            'requested_quantity' => 10.000,
        ]);

        // Verifikasi bahwa permintaan ini sekarang muncul di bagian gudang
        $this->actingAs($this->gudang)
            ->get(route('material-requests.index'))
            ->assertOk()
            ->assertSee($this->productionPlan->plan_number);
    }

    public function test_gudang_can_verify_pending_material_request_and_deduct_stock(): void
    {
        $materialRequest = MaterialRequest::factory()->create([
            'production_plan_id' => $this->productionPlan->id,
            'requested_by' => $this->produksi->id,
            'status' => 'pending',
        ]);

        $detail1 = MaterialRequestDetail::factory()->create([
            'material_request_id' => $materialRequest->id,
            'ingredient_id' => $this->ingredient1->id,
            'requested_quantity' => 15.000,
            'unit' => 'kg',
        ]);

        $detail2 = MaterialRequestDetail::factory()->create([
            'material_request_id' => $materialRequest->id,
            'ingredient_id' => $this->ingredient2->id,
            'requested_quantity' => 10.000,
            'unit' => 'kg',
        ]);

        $payload = [
            'items' => [
                [
                    'detail_id' => $detail1->id,
                    'issued_quantity' => 15.000,
                ],
                [
                    'detail_id' => $detail2->id,
                    'issued_quantity' => 8.500, // Diserahkan sebagian
                ],
            ],
            'notes' => 'Bahan diserahkan lengkap dan dalam kondisi baik.',
        ];

        $response = $this->actingAs($this->gudang)
            ->post(route('material-requests.verify', $materialRequest), $payload);

        $response->assertRedirect(route('material-requests.show', $materialRequest));
        $response->assertSessionHas('success');

        // 1. Verifikasi status permintaan berubah menjadi 'approved' dan verified_by tercatat
        $materialRequest->refresh();
        $this->assertEquals('approved', $materialRequest->status);
        $this->assertEquals($this->gudang->id, $materialRequest->verified_by);

        // 2. Verifikasi issued_quantity tersimpan pada detail
        $detail1->refresh();
        $detail2->refresh();
        $this->assertEquals('15.000', (string) $detail1->issued_quantity);
        $this->assertEquals('8.500', (string) $detail2->issued_quantity);

        // 3. Verifikasi pengurangan stok bahan baku
        $this->ingredient1->refresh();
        $this->ingredient2->refresh();
        // 50.00 - 15.00 = 35.00
        $this->assertEquals('35.00', (string) $this->ingredient1->current_stock);
        // 30.00 - 8.50 = 21.50
        $this->assertEquals('21.50', (string) $this->ingredient2->current_stock);

        // 4. Verifikasi transaksi stok keluar (Stok OUT - BR-05) tercatat
        $this->assertDatabaseHas('ingredient_stock_transactions', [
            'ingredient_id' => $this->ingredient1->id,
            'transaction_type' => 'out',
            'quantity' => 15.000,
            'reference_type' => 'material_request',
            'reference_id' => $materialRequest->id,
            'created_by' => $this->gudang->id,
        ]);

        $this->assertDatabaseHas('ingredient_stock_transactions', [
            'ingredient_id' => $this->ingredient2->id,
            'transaction_type' => 'out',
            'quantity' => 8.500,
            'reference_type' => 'material_request',
            'reference_id' => $materialRequest->id,
            'created_by' => $this->gudang->id,
        ]);

        $tx = IngredientStockTransaction::where('reference_id', $materialRequest->id)->first();
        $this->assertStringContainsString('PLN-20261018-0001', $tx->notes);
    }

    public function test_admin_can_verify_pending_material_request(): void
    {
        $materialRequest = MaterialRequest::factory()->create([
            'production_plan_id' => $this->productionPlan->id,
            'requested_by' => $this->produksi->id,
            'status' => 'pending',
        ]);

        $detail = MaterialRequestDetail::factory()->create([
            'material_request_id' => $materialRequest->id,
            'ingredient_id' => $this->ingredient1->id,
            'requested_quantity' => 5.000,
            'unit' => 'kg',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('material-requests.verify', $materialRequest), [
                'items' => [
                    [
                        'detail_id' => $detail->id,
                        'issued_quantity' => 5.000,
                    ],
                ],
            ]);

        $response->assertRedirect(route('material-requests.show', $materialRequest));

        $materialRequest->refresh();
        $this->assertEquals('approved', $materialRequest->status);
        $this->assertEquals($this->admin->id, $materialRequest->verified_by);

        $this->ingredient1->refresh();
        // 50.00 - 5.00 = 45.00
        $this->assertEquals('45.00', (string) $this->ingredient1->current_stock);
    }

    public function test_unauthorized_user_cannot_verify_material_request(): void
    {
        $materialRequest = MaterialRequest::factory()->create([
            'production_plan_id' => $this->productionPlan->id,
            'requested_by' => $this->produksi->id,
            'status' => 'pending',
        ]);

        $detail = MaterialRequestDetail::factory()->create([
            'material_request_id' => $materialRequest->id,
            'ingredient_id' => $this->ingredient1->id,
            'requested_quantity' => 5.000,
            'unit' => 'kg',
        ]);

        $this->actingAs($this->produksi)
            ->post(route('material-requests.verify', $materialRequest), [
                'items' => [
                    [
                        'detail_id' => $detail->id,
                        'issued_quantity' => 5.000,
                    ],
                ],
            ])
            ->assertForbidden();

        $this->actingAs($this->owner)
            ->post(route('material-requests.verify', $materialRequest), [
                'items' => [
                    [
                        'detail_id' => $detail->id,
                        'issued_quantity' => 5.000,
                    ],
                ],
            ])
            ->assertForbidden();

        // Stok tidak berubah
        $this->ingredient1->refresh();
        $this->assertEquals('50.00', (string) $this->ingredient1->current_stock);
        $this->assertEquals(0, IngredientStockTransaction::count());
    }

    public function test_verification_rejects_negative_issued_quantity(): void
    {
        $materialRequest = MaterialRequest::factory()->create([
            'production_plan_id' => $this->productionPlan->id,
            'requested_by' => $this->produksi->id,
            'status' => 'pending',
        ]);

        $detail = MaterialRequestDetail::factory()->create([
            'material_request_id' => $materialRequest->id,
            'ingredient_id' => $this->ingredient1->id,
            'requested_quantity' => 10.000,
            'unit' => 'kg',
        ]);

        $response = $this->actingAs($this->gudang)
            ->post(route('material-requests.verify', $materialRequest), [
                'items' => [
                    [
                        'detail_id' => $detail->id,
                        'issued_quantity' => -5,
                    ],
                ],
            ]);

        $response->assertSessionHasErrors();

        // Status dan stok tetap
        $materialRequest->refresh();
        $this->assertEquals('pending', $materialRequest->status);
        $this->ingredient1->refresh();
        $this->assertEquals('50.00', (string) $this->ingredient1->current_stock);
        $this->assertEquals(0, IngredientStockTransaction::count());
    }

    public function test_verification_rejects_issued_quantity_exceeding_requested(): void
    {
        $materialRequest = MaterialRequest::factory()->create([
            'production_plan_id' => $this->productionPlan->id,
            'requested_by' => $this->produksi->id,
            'status' => 'pending',
        ]);

        $detail = MaterialRequestDetail::factory()->create([
            'material_request_id' => $materialRequest->id,
            'ingredient_id' => $this->ingredient1->id,
            'requested_quantity' => 10.000,
            'unit' => 'kg',
        ]);

        $response = $this->actingAs($this->gudang)
            ->post(route('material-requests.verify', $materialRequest), [
                'items' => [
                    [
                        'detail_id' => $detail->id,
                        'issued_quantity' => 12.000, // Diminta 10, diserahkan 12 -> ditolak
                    ],
                ],
            ]);

        $response->assertSessionHasErrors();

        // Stok tidak berubah
        $materialRequest->refresh();
        $this->assertEquals('pending', $materialRequest->status);
        $this->ingredient1->refresh();
        $this->assertEquals('50.00', (string) $this->ingredient1->current_stock);
        $this->assertEquals(0, IngredientStockTransaction::count());
    }

    public function test_verification_rejects_issued_quantity_exceeding_available_stock(): void
    {
        // Set stok bahan hanya 5 kg
        $this->ingredient1->update(['current_stock' => 5.00]);

        $materialRequest = MaterialRequest::factory()->create([
            'production_plan_id' => $this->productionPlan->id,
            'requested_by' => $this->produksi->id,
            'status' => 'pending',
        ]);

        $detail = MaterialRequestDetail::factory()->create([
            'material_request_id' => $materialRequest->id,
            'ingredient_id' => $this->ingredient1->id,
            'requested_quantity' => 10.000, // Diminta 10, stok hanya 5, coba serahkan 8 -> ditolak
            'unit' => 'kg',
        ]);

        $response = $this->actingAs($this->gudang)
            ->post(route('material-requests.verify', $materialRequest), [
                'items' => [
                    [
                        'detail_id' => $detail->id,
                        'issued_quantity' => 8.000,
                    ],
                ],
            ]);

        $response->assertSessionHasErrors();

        // Stok tidak berubah
        $materialRequest->refresh();
        $this->assertEquals('pending', $materialRequest->status);
        $this->ingredient1->refresh();
        $this->assertEquals('5.00', (string) $this->ingredient1->current_stock);
        $this->assertEquals(0, IngredientStockTransaction::count());
    }

    public function test_already_processed_request_cannot_be_verified_again(): void
    {
        $materialRequest = MaterialRequest::factory()->create([
            'production_plan_id' => $this->productionPlan->id,
            'requested_by' => $this->produksi->id,
            'verified_by' => $this->gudang->id,
            'status' => 'approved',
        ]);

        $detail = MaterialRequestDetail::factory()->create([
            'material_request_id' => $materialRequest->id,
            'ingredient_id' => $this->ingredient1->id,
            'requested_quantity' => 10.000,
            'issued_quantity' => 10.000,
            'unit' => 'kg',
        ]);

        $response = $this->actingAs($this->gudang)
            ->post(route('material-requests.verify', $materialRequest), [
                'items' => [
                    [
                        'detail_id' => $detail->id,
                        'issued_quantity' => 10.000,
                    ],
                ],
            ]);

        $response->assertSessionHasErrors('status');

        // Stok tetap 50.00 dan transaksi stok keluar tidak bertambah ganda
        $this->ingredient1->refresh();
        $this->assertEquals('50.00', (string) $this->ingredient1->current_stock);
        $this->assertEquals(0, IngredientStockTransaction::count());
    }

    public function test_transaction_rolls_back_atomically_if_one_item_fails(): void
    {
        // Bahan 1 stok cukup (50 kg), Bahan 2 stok habis (0 kg)
        $this->ingredient2->update(['current_stock' => 0.00]);

        $materialRequest = MaterialRequest::factory()->create([
            'production_plan_id' => $this->productionPlan->id,
            'requested_by' => $this->produksi->id,
            'status' => 'pending',
        ]);

        $detail1 = MaterialRequestDetail::factory()->create([
            'material_request_id' => $materialRequest->id,
            'ingredient_id' => $this->ingredient1->id,
            'requested_quantity' => 10.000,
            'unit' => 'kg',
        ]);

        $detail2 = MaterialRequestDetail::factory()->create([
            'material_request_id' => $materialRequest->id,
            'ingredient_id' => $this->ingredient2->id,
            'requested_quantity' => 5.000,
            'unit' => 'kg',
        ]);

        $response = $this->actingAs($this->gudang)
            ->post(route('material-requests.verify', $materialRequest), [
                'items' => [
                    [
                        'detail_id' => $detail1->id,
                        'issued_quantity' => 10.000, // Valid
                    ],
                    [
                        'detail_id' => $detail2->id,
                        'issued_quantity' => 5.000, // Invalid karena stok habis
                    ],
                ],
            ]);

        $response->assertSessionHasErrors();

        // Atomisitas: Bahan 1 tidak terpotong, status tetap pending, tidak ada transaksi tercatat
        $this->ingredient1->refresh();
        $this->ingredient2->refresh();
        $materialRequest->refresh();

        $this->assertEquals('50.00', (string) $this->ingredient1->current_stock);
        $this->assertEquals('0.00', (string) $this->ingredient2->current_stock);
        $this->assertEquals('pending', $materialRequest->status);
        $this->assertEquals(0, IngredientStockTransaction::count());
    }
}
