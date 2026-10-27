<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\CateringOrder;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CateringOrderCreateTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $produksi;

    private User $gudang;

    private User $owner;

    private Customer $customer;

    private Product $productA;

    private Product $productB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
        $this->produksi = User::factory()->create(['role' => UserRole::Produksi]);
        $this->gudang = User::factory()->create(['role' => UserRole::Gudang]);
        $this->owner = User::factory()->create(['role' => UserRole::Owner]);

        $this->customer = Customer::factory()->create([
            'name' => 'PT Maju Makmur Bersama',
            'phone' => '081234567890',
            'address' => 'Jl. Merdeka No. 45, Jakarta Pusat',
            'is_active' => true,
        ]);

        $this->productA = Product::factory()->create([
            'name' => 'Nasi Kotak Ayam Bakar Madu Organik',
            'unit' => 'porsi',
            'selling_price' => 35000,
            'is_active' => true,
        ]);

        $this->productB = Product::factory()->create([
            'name' => 'Salad Buah Saus Yogurt Rendah Gula',
            'unit' => 'porsi',
            'selling_price' => 20000,
            'is_active' => true,
        ]);
    }

    public function test_guests_are_redirected_to_login_from_create_form(): void
    {
        $response = $this->get(route('catering-orders.create'));
        $response->assertRedirect(route('login'));
    }

    public function test_guests_cannot_store_catering_order(): void
    {
        $response = $this->post(route('catering-orders.store'), [
            'customer_id' => $this->customer->id,
            'order_date' => '2026-10-17',
            'delivery_date' => '2026-10-18',
            'items' => [
                ['product_id' => $this->productA->id, 'quantity' => 10],
            ],
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('catering_orders', 0);
    }

    public function test_admin_can_access_catering_order_create_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('catering-orders.create'));

        $response->assertOk();
        $response->assertSee('Buat Pesanan Katering Baru');
        $response->assertSee($this->customer->name);
        $response->assertSee($this->productA->name);
        $response->assertSee($this->productB->name);
    }

    public function test_non_admin_roles_are_forbidden_from_accessing_create_form(): void
    {
        foreach ([$this->produksi, $this->gudang, $this->owner] as $user) {
            $response = $this->actingAs($user)->get(route('catering-orders.create'));
            $response->assertForbidden();
        }
    }

    public function test_admin_can_create_catering_order_with_dynamic_items(): void
    {
        $payload = [
            'customer_id' => $this->customer->id,
            'order_date' => '2026-10-17',
            'delivery_date' => '2026-10-20',
            'notes' => 'Acara seminar kantor, minta pisahkan box packaging bento.',
            'items' => [
                [
                    'product_id' => $this->productA->id,
                    'quantity' => 50,
                    'unit_price' => 35000,
                    'notes' => 'Sambal dipisah',
                ],
                [
                    'product_id' => $this->productB->id,
                    'quantity' => 50,
                    'unit_price' => 20000,
                    'notes' => 'Dinginkan sebelum dikirim',
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('catering-orders.store'), $payload);

        $order = CateringOrder::latest('id')->first();
        $this->assertNotNull($order);

        $response->assertRedirect(route('catering-orders.show', $order));
        $response->assertSessionHas('success');

        // Check header order
        $this->assertDatabaseHas('catering_orders', [
            'id' => $order->id,
            'customer_id' => $this->customer->id,
            'created_by' => $this->admin->id,
            'order_date' => '2026-10-17 00:00:00',
            'delivery_date' => '2026-10-20 00:00:00',
            'status' => 'pending',
            'total_amount' => 2750000.00, // (50 * 35.000) + (50 * 20.000) = 1.750.000 + 1.000.000 = 2.750.000
            'notes' => 'Acara seminar kantor, minta pisahkan box packaging bento.',
        ]);

        $this->assertMatchesRegularExpression('/^ORD-\d{8}-\d{4}$/', $order->order_number);

        // Check order details
        $this->assertDatabaseHas('catering_order_details', [
            'catering_order_id' => $order->id,
            'product_id' => $this->productA->id,
            'quantity' => 50,
            'unit_price' => 35000.00,
            'subtotal' => 1750000.00,
            'notes' => 'Sambal dipisah',
        ]);

        $this->assertDatabaseHas('catering_order_details', [
            'catering_order_id' => $order->id,
            'product_id' => $this->productB->id,
            'quantity' => 50,
            'unit_price' => 20000.00,
            'subtotal' => 1000000.00,
            'notes' => 'Dinginkan sebelum dikirim',
        ]);

        $this->assertCount(2, $order->details);
    }

    public function test_non_admin_roles_are_forbidden_from_storing_catering_order(): void
    {
        $payload = [
            'customer_id' => $this->customer->id,
            'order_date' => '2026-10-17',
            'delivery_date' => '2026-10-18',
            'items' => [
                ['product_id' => $this->productA->id, 'quantity' => 10],
            ],
        ];

        foreach ([$this->produksi, $this->gudang, $this->owner] as $user) {
            $response = $this->actingAs($user)->post(route('catering-orders.store'), $payload);
            $response->assertForbidden();
        }

        $this->assertDatabaseCount('catering_orders', 0);
    }

    public function test_validation_requires_customer_and_dates(): void
    {
        $response = $this->actingAs($this->admin)->post(route('catering-orders.store'), [
            'customer_id' => '',
            'order_date' => '',
            'delivery_date' => '',
            'items' => [],
        ]);

        $response->assertSessionHasErrors(['customer_id', 'order_date', 'delivery_date', 'items']);
    }

    public function test_validation_delivery_date_cannot_be_before_order_date(): void
    {
        $response = $this->actingAs($this->admin)->post(route('catering-orders.store'), [
            'customer_id' => $this->customer->id,
            'order_date' => '2026-10-20',
            'delivery_date' => '2026-10-18', // Earlier than order date
            'items' => [
                ['product_id' => $this->productA->id, 'quantity' => 5],
            ],
        ]);

        $response->assertSessionHasErrors(['delivery_date']);
    }

    public function test_validation_requires_at_least_one_item_with_positive_quantity(): void
    {
        $response = $this->actingAs($this->admin)->post(route('catering-orders.store'), [
            'customer_id' => $this->customer->id,
            'order_date' => '2026-10-17',
            'delivery_date' => '2026-10-18',
            'items' => [
                ['product_id' => $this->productA->id, 'quantity' => 0],
            ],
        ]);

        $response->assertSessionHasErrors(['items.0.quantity']);
    }

    public function test_validation_rejects_duplicate_products_in_same_order(): void
    {
        $response = $this->actingAs($this->admin)->post(route('catering-orders.store'), [
            'customer_id' => $this->customer->id,
            'order_date' => '2026-10-17',
            'delivery_date' => '2026-10-18',
            'items' => [
                ['product_id' => $this->productA->id, 'quantity' => 10],
                ['product_id' => $this->productA->id, 'quantity' => 15], // Duplicate product
            ],
        ]);

        $response->assertSessionHasErrors(['items.0.product_id', 'items.1.product_id']);
    }

    public function test_fallback_to_product_selling_price_when_unit_price_is_not_provided(): void
    {
        $payload = [
            'customer_id' => $this->customer->id,
            'order_date' => '2026-10-17',
            'delivery_date' => '2026-10-18',
            'items' => [
                [
                    'product_id' => $this->productA->id,
                    'quantity' => 10, // 10 * 35000 = 350000
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('catering-orders.store'), $payload);

        $order = CateringOrder::latest('id')->first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('catering-orders.show', $order));

        $this->assertDatabaseHas('catering_order_details', [
            'catering_order_id' => $order->id,
            'product_id' => $this->productA->id,
            'quantity' => 10,
            'unit_price' => 35000.00,
            'subtotal' => 350000.00,
        ]);

        $this->assertEquals(350000.00, (float) $order->total_amount);
    }

    public function test_all_authenticated_roles_can_view_catering_orders_index_and_show(): void
    {
        $order = CateringOrder::factory()->create([
            'customer_id' => $this->customer->id,
            'created_by' => $this->admin->id,
            'total_amount' => 150000,
        ]);

        $order->details()->create([
            'product_id' => $this->productA->id,
            'quantity' => 5,
            'unit_price' => 30000,
            'subtotal' => 150000,
        ]);

        foreach ([$this->admin, $this->produksi, $this->gudang, $this->owner] as $user) {
            $indexResponse = $this->actingAs($user)->get(route('catering-orders.index'));
            $indexResponse->assertOk();
            $indexResponse->assertSee($order->order_number);
            $indexResponse->assertSee($this->customer->name);

            $showResponse = $this->actingAs($user)->get(route('catering-orders.show', $order));
            $showResponse->assertOk();
            $showResponse->assertSee($order->order_number);
            $showResponse->assertSee('Nasi Kotak Ayam Bakar Madu Organik');
        }
    }
}
