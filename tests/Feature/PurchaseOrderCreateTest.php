<?php

namespace Tests\Feature;

use App\Enums\PurchaseOrderStatus;
use App\Enums\UserRole;
use App\Models\Ingredient;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderCreateTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $gudang;

    private User $produksi;

    private User $owner;

    private Supplier $vendor;

    private Ingredient $ingredient1;

    private Ingredient $ingredient2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
        $this->gudang = User::factory()->create(['role' => UserRole::Gudang]);
        $this->produksi = User::factory()->create(['role' => UserRole::Produksi]);
        $this->owner = User::factory()->create(['role' => UserRole::Owner]);

        $this->vendor = Supplier::factory()->create([
            'name' => 'PT Sumber Bahan Segar',
            'is_active' => true,
        ]);

        $this->ingredient1 = Ingredient::factory()->create([
            'name' => 'Beras Merah Organik',
            'unit' => 'kg',
            'is_active' => true,
        ]);

        $this->ingredient2 = Ingredient::factory()->create([
            'name' => 'Dada Ayam Fillet',
            'unit' => 'kg',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function test_guest_is_redirected_to_login_when_accessing_purchase_order_pages(): void
    {
        $this->get(route('purchase-orders.index'))->assertRedirect(route('login'));
        $this->get(route('purchase-orders.create'))->assertRedirect(route('login'));
    }

    /** @test */
    public function test_admin_can_access_purchase_order_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('purchase-orders.index'));
        $response->assertOk();
        $response->assertSee('Daftar Purchase Order');
    }

    /** @test */
    public function test_admin_can_access_create_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('purchase-orders.create'));
        $response->assertOk();
        $response->assertSee('Buat Purchase Order Bahan Baku');
        $response->assertSee('PT Sumber Bahan Segar');
        $response->assertSee('Beras Merah Organik');
    }

    /** @test */
    public function test_non_admin_roles_are_forbidden_from_create_form(): void
    {
        foreach ([$this->gudang, $this->produksi, $this->owner] as $user) {
            $response = $this->actingAs($user)->get(route('purchase-orders.create'));
            $response->assertForbidden();
        }
    }

    /** @test */
    public function test_non_admin_roles_are_forbidden_from_index(): void
    {
        foreach ([$this->gudang, $this->produksi, $this->owner] as $user) {
            $response = $this->actingAs($user)->get(route('purchase-orders.index'));
            $response->assertForbidden();
        }
    }

    /** @test */
    public function test_admin_can_create_purchase_order_with_multiple_items(): void
    {
        $payload = [
            'vendor_id' => $this->vendor->id,
            'order_date' => '2026-10-17',
            'expected_date' => '2026-10-24',
            'notes' => 'Tolong kirim sebelum jam 08.00 pagi',
            'items' => [
                [
                    'ingredient_id' => $this->ingredient1->id,
                    'quantity' => 50,
                    'unit' => 'kg',
                    'price' => 15000,
                ],
                [
                    'ingredient_id' => $this->ingredient2->id,
                    'quantity' => 30,
                    'unit' => 'kg',
                    'price' => 35000,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('purchase-orders.store'), $payload);

        $order = PurchaseOrder::latest('id')->first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('purchase-orders.show', $order));

        $this->assertDatabaseHas('purchase_orders', [
            'id' => $order->id,
            'vendor_id' => $this->vendor->id,
            'created_by' => $this->admin->id,
            'order_date' => '2026-10-17',
            'expected_date' => '2026-10-24',
            'status' => 'draft',
            'notes' => 'Tolong kirim sebelum jam 08.00 pagi',
        ]);

        $this->assertDatabaseHas('purchase_order_details', [
            'purchase_order_id' => $order->id,
            'ingredient_id' => $this->ingredient1->id,
            'quantity' => 50,
            'unit' => 'kg',
            'price' => 15000,
            'subtotal' => 750000,
        ]);

        $this->assertDatabaseHas('purchase_order_details', [
            'purchase_order_id' => $order->id,
            'ingredient_id' => $this->ingredient2->id,
            'quantity' => 30,
            'unit' => 'kg',
            'price' => 35000,
            'subtotal' => 1050000,
        ]);

        $this->assertEquals(2, $order->details()->count());
    }

    /** @test */
    public function test_order_number_is_generated_with_correct_format(): void
    {
        $payload = [
            'vendor_id' => $this->vendor->id,
            'order_date' => '2026-10-17',
            'expected_date' => '2026-10-20',
            'items' => [
                [
                    'ingredient_id' => $this->ingredient1->id,
                    'quantity' => 10,
                    'unit' => 'kg',
                    'price' => 15000,
                ],
            ],
        ];

        $this->actingAs($this->admin)->post(route('purchase-orders.store'), $payload);

        $order = PurchaseOrder::latest('id')->first();
        $this->assertNotNull($order);
        $this->assertMatchesRegularExpression('/^PO-\d{8}-\d{4}$/', $order->order_number);
        $this->assertStringStartsWith('PO-20261017-', $order->order_number);
    }

    /** @test */
    public function test_validation_fails_when_required_fields_are_missing(): void
    {
        $response = $this->actingAs($this->admin)->post(route('purchase-orders.store'), []);

        $response->assertSessionHasErrors(['vendor_id', 'order_date', 'expected_date', 'items']);
    }

    /** @test */
    public function test_validation_fails_when_expected_date_is_before_order_date(): void
    {
        $payload = [
            'vendor_id' => $this->vendor->id,
            'order_date' => '2026-10-20',
            'expected_date' => '2026-10-18', // Sebelum order_date
            'items' => [
                [
                    'ingredient_id' => $this->ingredient1->id,
                    'quantity' => 10,
                    'unit' => 'kg',
                    'price' => 15000,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('purchase-orders.store'), $payload);

        $response->assertSessionHasErrors(['expected_date']);
    }

    /** @test */
    public function test_validation_fails_when_quantity_is_zero_or_negative(): void
    {
        $payload = [
            'vendor_id' => $this->vendor->id,
            'order_date' => '2026-10-17',
            'expected_date' => '2026-10-20',
            'items' => [
                [
                    'ingredient_id' => $this->ingredient1->id,
                    'quantity' => 0,
                    'unit' => 'kg',
                    'price' => 15000,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('purchase-orders.store'), $payload);

        $response->assertSessionHasErrors(['items.0.quantity']);
    }

    /** @test */
    public function test_validation_fails_when_price_is_zero_or_negative(): void
    {
        $payload = [
            'vendor_id' => $this->vendor->id,
            'order_date' => '2026-10-17',
            'expected_date' => '2026-10-20',
            'items' => [
                [
                    'ingredient_id' => $this->ingredient1->id,
                    'quantity' => 10,
                    'unit' => 'kg',
                    'price' => 0,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('purchase-orders.store'), $payload);

        $response->assertSessionHasErrors(['items.0.price']);
    }

    /** @test */
    public function test_validation_fails_when_duplicate_ingredients_are_selected(): void
    {
        $payload = [
            'vendor_id' => $this->vendor->id,
            'order_date' => '2026-10-17',
            'expected_date' => '2026-10-20',
            'items' => [
                [
                    'ingredient_id' => $this->ingredient1->id,
                    'quantity' => 10,
                    'unit' => 'kg',
                    'price' => 15000,
                ],
                [
                    'ingredient_id' => $this->ingredient1->id, // Duplikat
                    'quantity' => 5,
                    'unit' => 'kg',
                    'price' => 15000,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('purchase-orders.store'), $payload);

        $response->assertSessionHasErrors(['items.0.ingredient_id', 'items.1.ingredient_id']);
    }

    /** @test */
    public function test_admin_can_view_purchase_order_detail(): void
    {
        $order = PurchaseOrder::create([
            'vendor_id' => $this->vendor->id,
            'created_by' => $this->admin->id,
            'order_number' => 'PO-20261017-0001',
            'order_date' => '2026-10-17',
            'expected_date' => '2026-10-24',
            'status' => PurchaseOrderStatus::Draft,
        ]);

        $order->details()->create([
            'ingredient_id' => $this->ingredient1->id,
            'quantity' => 20,
            'unit' => 'kg',
            'price' => 15000,
            'subtotal' => 300000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('purchase-orders.show', $order));
        $response->assertOk();
        $response->assertSee('PO-20261017-0001');
        $response->assertSee('PT Sumber Bahan Segar');
        $response->assertSee('Beras Merah Organik');
    }

    /** @test */
    public function test_non_admin_cannot_store_purchase_order(): void
    {
        $payload = [
            'vendor_id' => $this->vendor->id,
            'order_date' => '2026-10-17',
            'expected_date' => '2026-10-20',
            'items' => [
                [
                    'ingredient_id' => $this->ingredient1->id,
                    'quantity' => 10,
                    'unit' => 'kg',
                    'price' => 15000,
                ],
            ],
        ];

        foreach ([$this->gudang, $this->produksi, $this->owner] as $user) {
            $response = $this->actingAs($user)->post(route('purchase-orders.store'), $payload);
            $response->assertForbidden();
        }

        $this->assertDatabaseCount('purchase_orders', 0);
    }

    /** @test */
    public function test_subtotal_is_calculated_correctly_per_item(): void
    {
        $payload = [
            'vendor_id' => $this->vendor->id,
            'order_date' => '2026-10-17',
            'expected_date' => '2026-10-24',
            'items' => [
                [
                    'ingredient_id' => $this->ingredient1->id,
                    'quantity' => 25.5,
                    'unit' => 'kg',
                    'price' => 12000,
                ],
            ],
        ];

        $this->actingAs($this->admin)->post(route('purchase-orders.store'), $payload);

        $order = PurchaseOrder::latest('id')->first();
        $this->assertNotNull($order);

        $detail = $order->details()->first();
        $this->assertNotNull($detail);
        $this->assertEquals(306000.0, (float) $detail->subtotal); // 25.5 * 12000 = 306000
    }
}
