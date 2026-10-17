<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\CateringOrder;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CateringOrderListTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $owner;

    private User $produksi;

    private User $gudang;

    private Customer $customerA;

    private Customer $customerB;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
        $this->owner = User::factory()->create(['role' => UserRole::Owner]);
        $this->produksi = User::factory()->create(['role' => UserRole::Produksi]);
        $this->gudang = User::factory()->create(['role' => UserRole::Gudang]);

        $this->customerA = Customer::factory()->create([
            'name' => 'PT Maju Bersama Catering',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        $this->customerB = Customer::factory()->create([
            'name' => 'CV Sumber Makmur Enak',
            'phone' => '089876543210',
            'is_active' => true,
        ]);

        $this->product = Product::factory()->create([
            'name' => 'Paket Nasi Kotak Bumbu Rujak Organik',
            'unit' => 'porsi',
            'selling_price' => 35000,
            'is_active' => true,
        ]);
    }

    public function test_guests_are_redirected_to_login_from_catering_orders_list(): void
    {
        $response = $this->get(route('catering-orders.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_and_owner_can_access_catering_orders_list(): void
    {
        $adminResponse = $this->actingAs($this->admin)->get(route('catering-orders.index'));
        $adminResponse->assertOk();
        $adminResponse->assertSee('Daftar Pesanan Katering');

        $ownerResponse = $this->actingAs($this->owner)->get(route('catering-orders.index'));
        $ownerResponse->assertOk();
        $ownerResponse->assertSee('Daftar Pesanan Katering');
    }

    public function test_orders_list_displays_all_required_columns_and_information(): void
    {
        $order = CateringOrder::create([
            'order_number' => 'ORD-20261017-0001',
            'customer_id' => $this->customerA->id,
            'created_by' => $this->admin->id,
            'order_date' => '2026-10-17',
            'delivery_date' => '2026-10-25',
            'status' => 'pending',
            'total_amount' => 1750000.00,
        ]);

        $order->details()->create([
            'product_id' => $this->product->id,
            'quantity' => 50,
            'unit_price' => 35000.00,
            'subtotal' => 1750000.00,
        ]);

        $response = $this->actingAs($this->admin)->get(route('catering-orders.index'));

        $response->assertOk();
        // 1. Nomor pesanan
        $response->assertSee('ORD-20261017-0001');
        // 2. Nama pelanggan
        $response->assertSee('PT Maju Bersama Catering');
        // 3. Tanggal kirim
        $response->assertSee('25/10/2026');
        // 4. Total porsi
        $response->assertSee('50 Porsi');
        // 5. Total nominal
        $response->assertSee('Rp 1.750.000');
        // 6. Badge status pesanan
        $response->assertSee('Pending');
    }

    public function test_filter_by_status(): void
    {
        $orderPending = CateringOrder::create([
            'order_number' => 'ORD-20261017-0010',
            'customer_id' => $this->customerA->id,
            'created_by' => $this->admin->id,
            'order_date' => '2026-10-17',
            'delivery_date' => '2026-10-21',
            'status' => 'pending',
            'total_amount' => 500000.00,
        ]);

        $orderConfirmed = CateringOrder::create([
            'order_number' => 'ORD-20261017-0020',
            'customer_id' => $this->customerB->id,
            'created_by' => $this->admin->id,
            'order_date' => '2026-10-17',
            'delivery_date' => '2026-10-22',
            'status' => 'confirmed',
            'total_amount' => 600000.00,
        ]);

        $orderProcessing = CateringOrder::create([
            'order_number' => 'ORD-20261017-0030',
            'customer_id' => $this->customerA->id,
            'created_by' => $this->admin->id,
            'order_date' => '2026-10-17',
            'delivery_date' => '2026-10-23',
            'status' => 'processing',
            'total_amount' => 700000.00,
        ]);

        $orderDelivered = CateringOrder::create([
            'order_number' => 'ORD-20261017-0040',
            'customer_id' => $this->customerB->id,
            'created_by' => $this->admin->id,
            'order_date' => '2026-10-17',
            'delivery_date' => '2026-10-24',
            'status' => 'delivered',
            'total_amount' => 800000.00,
        ]);

        $orderCancelled = CateringOrder::create([
            'order_number' => 'ORD-20261017-0050',
            'customer_id' => $this->customerA->id,
            'created_by' => $this->admin->id,
            'order_date' => '2026-10-17',
            'delivery_date' => '2026-10-25',
            'status' => 'cancelled',
            'total_amount' => 900000.00,
        ]);

        // Filter: Pending
        $response = $this->actingAs($this->admin)->get(route('catering-orders.index', ['status' => 'pending']));
        $response->assertSee($orderPending->order_number);
        $response->assertDontSee($orderConfirmed->order_number);
        $response->assertDontSee($orderProcessing->order_number);
        $response->assertDontSee($orderDelivered->order_number);
        $response->assertDontSee($orderCancelled->order_number);

        // Filter: Confirmed / Dikonfirmasi
        $response = $this->actingAs($this->admin)->get(route('catering-orders.index', ['status' => 'confirmed']));
        $response->assertSee($orderConfirmed->order_number);
        $response->assertDontSee($orderPending->order_number);

        // Filter: Processing / Diproses
        $response = $this->actingAs($this->admin)->get(route('catering-orders.index', ['status' => 'processing']));
        $response->assertSee($orderProcessing->order_number);
        $response->assertDontSee($orderPending->order_number);

        // Filter: Delivered / Selesai
        $response = $this->actingAs($this->admin)->get(route('catering-orders.index', ['status' => 'delivered']));
        $response->assertSee($orderDelivered->order_number);
        $response->assertDontSee($orderPending->order_number);

        // Filter: Cancelled / Dibatalkan
        $response = $this->actingAs($this->admin)->get(route('catering-orders.index', ['status' => 'cancelled']));
        $response->assertSee($orderCancelled->order_number);
        $response->assertDontSee($orderPending->order_number);
    }

    public function test_filter_by_delivery_date_range(): void
    {
        $orderA = CateringOrder::create([
            'order_number' => 'ORD-20261017-1001',
            'customer_id' => $this->customerA->id,
            'created_by' => $this->admin->id,
            'order_date' => '2026-10-17',
            'delivery_date' => '2026-10-20',
            'status' => 'pending',
            'total_amount' => 200000.00,
        ]);

        $orderB = CateringOrder::create([
            'order_number' => 'ORD-20261017-1002',
            'customer_id' => $this->customerA->id,
            'created_by' => $this->admin->id,
            'order_date' => '2026-10-17',
            'delivery_date' => '2026-10-25',
            'status' => 'pending',
            'total_amount' => 200000.00,
        ]);

        $orderC = CateringOrder::create([
            'order_number' => 'ORD-20261017-1003',
            'customer_id' => $this->customerA->id,
            'created_by' => $this->admin->id,
            'order_date' => '2026-10-17',
            'delivery_date' => '2026-10-30',
            'status' => 'pending',
            'total_amount' => 200000.00,
        ]);

        $response = $this->actingAs($this->admin)->get(route('catering-orders.index', [
            'start_date' => '2026-10-22',
            'end_date' => '2026-10-26',
        ]));

        $response->assertOk();
        $response->assertDontSee($orderA->order_number);
        $response->assertSee($orderB->order_number);
        $response->assertDontSee($orderC->order_number);
    }

    public function test_filter_by_search_query_order_number_and_customer_name(): void
    {
        $orderA = CateringOrder::create([
            'order_number' => 'ORD-20261017-8888',
            'customer_id' => $this->customerA->id, // PT Maju Bersama Catering
            'created_by' => $this->admin->id,
            'order_date' => '2026-10-17',
            'delivery_date' => '2026-10-22',
            'status' => 'pending',
            'total_amount' => 250000.00,
        ]);

        $orderB = CateringOrder::create([
            'order_number' => 'ORD-20261017-9999',
            'customer_id' => $this->customerB->id, // CV Sumber Makmur Enak
            'created_by' => $this->admin->id,
            'order_date' => '2026-10-17',
            'delivery_date' => '2026-10-23',
            'status' => 'pending',
            'total_amount' => 350000.00,
        ]);

        // Cari berdasarkan No Order
        $searchOrderResponse = $this->actingAs($this->admin)->get(route('catering-orders.index', ['search' => '8888']));
        $searchOrderResponse->assertSee($orderA->order_number);
        $searchOrderResponse->assertDontSee($orderB->order_number);

        // Cari berdasarkan Nama Pelanggan
        $searchCustomerResponse = $this->actingAs($this->admin)->get(route('catering-orders.index', ['search' => 'Sumber Makmur']));
        $searchCustomerResponse->assertSee($orderB->order_number);
        $searchCustomerResponse->assertDontSee($orderA->order_number);
    }

    public function test_admin_can_perform_quick_status_update(): void
    {
        $order = CateringOrder::create([
            'order_number' => 'ORD-20261017-5555',
            'customer_id' => $this->customerA->id,
            'created_by' => $this->admin->id,
            'order_date' => '2026-10-17',
            'delivery_date' => '2026-10-24',
            'status' => 'pending',
            'total_amount' => 450000.00,
        ]);

        // Admin update status dari pending ke confirmed
        $response = $this->actingAs($this->admin)
            ->from(route('catering-orders.index'))
            ->patch(route('catering-orders.update-status', $order), [
                'status' => 'confirmed',
            ]);

        $response->assertRedirect(route('catering-orders.index'));
        $response->assertSessionHas('success');

        $this->assertEquals('confirmed', $order->fresh()->status);
        $this->assertEquals('Dikonfirmasi', $order->fresh()->status_label);

        // Admin update status selanjutnya dari confirmed ke processing
        $response2 = $this->actingAs($this->admin)
            ->patch(route('catering-orders.update-status', $order), [
                'status' => 'processing',
            ]);

        $response2->assertSessionHas('success');
        $this->assertEquals('processing', $order->fresh()->status);
        $this->assertEquals('Diproses', $order->fresh()->status_label);

        // Admin update status ke delivered (selesai)
        $response3 = $this->actingAs($this->admin)
            ->patch(route('catering-orders.update-status', $order), [
                'status' => 'delivered',
            ]);

        $response3->assertSessionHas('success');
        $this->assertEquals('delivered', $order->fresh()->status);
        $this->assertEquals('Selesai', $order->fresh()->status_label);
    }

    public function test_status_update_validates_allowed_status_values(): void
    {
        $order = CateringOrder::create([
            'order_number' => 'ORD-20261017-7777',
            'customer_id' => $this->customerA->id,
            'created_by' => $this->admin->id,
            'order_date' => '2026-10-17',
            'delivery_date' => '2026-10-24',
            'status' => 'pending',
            'total_amount' => 100000.00,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('catering-orders.update-status', $order), [
                'status' => 'status_tidak_valid',
            ]);

        $response->assertSessionHasErrors('status');
        $this->assertEquals('pending', $order->fresh()->status);
    }

    public function test_owner_cannot_update_order_status_due_to_read_only_authorization(): void
    {
        $order = CateringOrder::create([
            'order_number' => 'ORD-20261017-6666',
            'customer_id' => $this->customerA->id,
            'created_by' => $this->admin->id,
            'order_date' => '2026-10-17',
            'delivery_date' => '2026-10-24',
            'status' => 'pending',
            'total_amount' => 300000.00,
        ]);

        // Owner (read-only) mencoba melakukan aksi perubahan status cepat
        $response = $this->actingAs($this->owner)
            ->patch(route('catering-orders.update-status', $order), [
                'status' => 'confirmed',
            ]);

        $response->assertForbidden();
        $this->assertEquals('pending', $order->fresh()->status);
    }

    public function test_produksi_and_gudang_cannot_update_order_status(): void
    {
        $order = CateringOrder::create([
            'order_number' => 'ORD-20261017-4444',
            'customer_id' => $this->customerA->id,
            'created_by' => $this->admin->id,
            'order_date' => '2026-10-17',
            'delivery_date' => '2026-10-24',
            'status' => 'pending',
            'total_amount' => 300000.00,
        ]);

        foreach ([$this->produksi, $this->gudang] as $user) {
            $response = $this->actingAs($user)
                ->patch(route('catering-orders.update-status', $order), [
                    'status' => 'confirmed',
                ]);

            $response->assertForbidden();
            $this->assertEquals('pending', $order->fresh()->status);
        }
    }

    public function test_owner_does_not_see_status_change_forms_or_create_button_in_view(): void
    {
        $order = CateringOrder::create([
            'order_number' => 'ORD-20261017-3333',
            'customer_id' => $this->customerA->id,
            'created_by' => $this->admin->id,
            'order_date' => '2026-10-17',
            'delivery_date' => '2026-10-24',
            'status' => 'pending',
            'total_amount' => 300000.00,
        ]);

        $response = $this->actingAs($this->owner)->get(route('catering-orders.index'));

        $response->assertOk();
        // Owner melihat daftar dan badge status secara read-only
        $response->assertSee($order->order_number);
        $response->assertSee('Pending');
        // Owner TIDAK melihat tombol create pesanan atau dropdown perubahan status
        $response->assertDontSee('id="btn-create-catering-order"', false);
        $response->assertDontSee('id="status-select-'.$order->id.'"', false);
    }

    public function test_admin_sees_quick_status_change_controls_and_create_button_in_view(): void
    {
        $order = CateringOrder::create([
            'order_number' => 'ORD-20261017-2222',
            'customer_id' => $this->customerA->id,
            'created_by' => $this->admin->id,
            'order_date' => '2026-10-17',
            'delivery_date' => '2026-10-24',
            'status' => 'pending',
            'total_amount' => 300000.00,
        ]);

        $response = $this->actingAs($this->admin)->get(route('catering-orders.index'));

        $response->assertOk();
        $response->assertSee('id="btn-create-catering-order"', false);
        $response->assertSee('id="status-select-'.$order->id.'"', false);
        $response->assertSee('Konfirmasi');
    }

    public function test_pagination_renders_when_orders_exceed_per_page_limit(): void
    {
        // Buat 15 pesanan (limit per page adalah 10)
        for ($i = 1; $i <= 15; $i++) {
            CateringOrder::create([
                'order_number' => sprintf('ORD-20261017-%04d', $i),
                'customer_id' => $this->customerA->id,
                'created_by' => $this->admin->id,
                'order_date' => '2026-10-17',
                'delivery_date' => '2026-10-20',
                'status' => 'pending',
                'total_amount' => 100000.00,
            ]);
        }

        $page1Response = $this->actingAs($this->admin)->get(route('catering-orders.index'));
        $page1Response->assertOk();
        $page1Response->assertSee('ORD-20261017-0015'); // Paling baru (id tertinggi)

        $page2Response = $this->actingAs($this->admin)->get(route('catering-orders.index', ['page' => 2]));
        $page2Response->assertOk();
        $page2Response->assertSee('ORD-20261017-0001');
    }
}
