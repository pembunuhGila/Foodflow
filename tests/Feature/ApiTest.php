<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\CateringOrder;
use App\Models\CateringOrderDetail;
use App\Models\Customer;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@foodflow.test',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
            'status' => UserStatus::Aktif,
        ]);
    }

    public function test_login_success_and_returns_sanctum_token(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'admin@foodflow.test',
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'token',
                'token_type',
                'expires_at',
                'user' => ['id', 'name', 'email', 'role'],
            ]);
    }

    public function test_login_with_invalid_credentials_returns_401(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'admin@foodflow.test',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Kredensial tidak valid.');
    }

    public function test_unauthenticated_request_returns_401(): void
    {
        $response = $this->getJson('/api/v1/ingredients');

        $response->assertStatus(401);
    }

    public function test_authenticated_can_list_ingredients_with_pagination(): void
    {
        Ingredient::factory()->count(5)->create(['is_active' => true]);

        Sanctum::actingAs($this->admin, ['ingredients:read']);

        $response = $this->getJson('/api/v1/ingredients?per_page=3');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'category', 'unit', 'current_stock', 'minimum_stock'],
                ],
                'links',
                'meta',
            ])
            ->assertJsonPath('meta.per_page', 3);
    }

    public function test_authenticated_can_list_products(): void
    {
        Product::factory()->count(3)->create(['is_active' => true]);

        Sanctum::actingAs($this->admin, ['products:read']);

        $response = $this->getJson('/api/v1/products');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'price', 'nutrition'],
                ],
            ]);
    }

    public function test_catering_order_requires_idempotency_key(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['selling_price' => 25000]);

        Sanctum::actingAs($this->admin, ['orders:create']);

        $response = $this->postJson('/api/v1/catering-orders', [
            'customer_id' => $customer->id,
            'delivery_date' => now()->addDays(2)->format('Y-m-d'),
            'items' => [
                ['product_id' => $product->id, 'quantity' => 10],
            ],
        ]);

        $response->assertStatus(400)
            ->assertJsonPath('message', 'Header Idempotency-Key wajib diisi.');
    }

    public function test_catering_order_is_idempotent_when_key_is_replayed(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['selling_price' => 20000]);

        Sanctum::actingAs($this->admin, ['orders:create']);

        $key = (string) Str::uuid();
        $payload = [
            'customer_id' => $customer->id,
            'delivery_date' => now()->addDays(2)->format('Y-m-d'),
            'items' => [
                ['product_id' => $product->id, 'quantity' => 5],
            ],
        ];

        // Request 1: Buat order baru
        $first = $this->withHeaders(['Idempotency-Key' => $key])
            ->postJson('/api/v1/catering-orders', $payload);

        $first->assertStatus(201)
            ->assertJsonPath('data.total_amount', 100000);

        $orderId = $first->json('data.id');

        // Request 2: Replay dengan key yang sama
        $second = $this->withHeaders(['Idempotency-Key' => $key])
            ->postJson('/api/v1/catering-orders', $payload);

        $second->assertStatus(201)
            ->assertHeader('Idempotent-Replayed', 'true')
            ->assertJsonPath('data.id', $orderId);

        // Pastikan hanya 1 pesanan katering yang tercatat di database
        $this->assertDatabaseCount('catering_orders', 1);
    }

    public function test_authenticated_can_list_catering_orders(): void
    {
        $customer = Customer::factory()->create();
        CateringOrder::factory()->count(3)->create(['customer_id' => $customer->id]);

        Sanctum::actingAs($this->admin, ['orders:read']);

        $response = $this->getJson('/api/v1/catering-orders');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'order_number', 'delivery_date', 'total_amount', 'status', 'status_label'],
                ],
                'links',
                'meta',
            ]);
    }

    public function test_authenticated_can_view_catering_order_detail(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['selling_price' => 25000]);
        $order = CateringOrder::factory()->create(['customer_id' => $customer->id]);
        CateringOrderDetail::create([
            'catering_order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'unit_price' => 25000,
            'subtotal' => 250000,
        ]);

        Sanctum::actingAs($this->admin, ['orders:read']);

        $response = $this->getJson("/api/v1/catering-orders/{$order->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $order->id)
            ->assertJsonPath('data.customer.id', $customer->id)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'order_number',
                    'total_amount',
                    'customer' => ['id', 'name', 'phone'],
                    'items' => [
                        '*' => ['id', 'product_id', 'product_name', 'quantity', 'unit_price', 'subtotal'],
                    ],
                ],
            ]);
    }

    public function test_logout_revokes_token(): void
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/logout');

        $response->assertOk()
            ->assertJsonPath('message', 'Logout berhasil.');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
