<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\CateringOrder;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CateringOrderDetailTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $owner;

    private User $produksi;

    private User $gudang;

    private Customer $customer;

    private Product $productA;

    private Product $productB;

    private CateringOrder $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
        $this->owner = User::factory()->create(['role' => UserRole::Owner]);
        $this->produksi = User::factory()->create(['role' => UserRole::Produksi]);
        $this->gudang = User::factory()->create(['role' => UserRole::Gudang]);

        $this->customer = Customer::factory()->create([
            'name' => 'PT Makmur Sentosa Bersama',
            'phone' => '081298765432',
            'email' => 'kontak@makmursentosa.com',
            'address' => 'Gedung Wisma Niaga Lt. 8, Jl. Thamrin No. 12, Jakarta Pusat',
            'is_active' => true,
        ]);

        $this->productA = Product::factory()->create([
            'name' => 'Paket Nasi Liwet Ayam Bakar Komplit',
            'unit' => 'porsi',
            'selling_price' => 45000,
            'is_active' => true,
        ]);

        $this->productB = Product::factory()->create([
            'name' => 'Puding Pandan Organik Gula Aren',
            'unit' => 'porsi',
            'selling_price' => 15000,
            'is_active' => true,
        ]);

        $this->order = CateringOrder::create([
            'order_number' => 'ORD-20261017-0099',
            'customer_id' => $this->customer->id,
            'created_by' => $this->admin->id,
            'order_date' => '2026-10-17',
            'delivery_date' => '2026-10-25',
            'status' => 'pending',
            'total_amount' => 3000000.00,
            'notes' => 'Tolong sediakan sendok garpu kayu ramah lingkungan dan sambal dipisah wadahnya.',
        ]);

        $this->order->details()->create([
            'product_id' => $this->productA->id,
            'quantity' => 50,
            'unit_price' => 45000.00,
            'subtotal' => 2250000.00,
            'notes' => 'Ayam bakar sambal terpisah',
        ]);

        $this->order->details()->create([
            'product_id' => $this->productB->id,
            'quantity' => 50,
            'unit_price' => 15000.00,
            'subtotal' => 750000.00,
            'notes' => 'Dingin sebelum dikirim',
        ]);
    }

    public function test_guests_are_redirected_to_login_from_catering_order_detail(): void
    {
        $response = $this->get(route('catering-orders.show', $this->order));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_catering_order_detail(): void
    {
        $response = $this->actingAs($this->admin)->get(route('catering-orders.show', $this->order));

        $response->assertOk();
        $response->assertSee('Rincian Pesanan Katering');
        $response->assertSee($this->order->order_number);
    }

    public function test_owner_can_view_catering_order_detail(): void
    {
        $response = $this->actingAs($this->owner)->get(route('catering-orders.show', $this->order));

        $response->assertOk();
        $response->assertSee('Rincian Pesanan Katering');
        $response->assertSee($this->order->order_number);
    }

    public function test_produksi_and_gudang_can_view_catering_order_detail(): void
    {
        $produksiResponse = $this->actingAs($this->produksi)->get(route('catering-orders.show', $this->order));
        $produksiResponse->assertOk();
        $produksiResponse->assertSee($this->order->order_number);

        $gudangResponse = $this->actingAs($this->gudang)->get(route('catering-orders.show', $this->order));
        $gudangResponse->assertOk();
        $gudangResponse->assertSee($this->order->order_number);
    }

    public function test_order_detail_displays_full_customer_information(): void
    {
        $response = $this->actingAs($this->admin)->get(route('catering-orders.show', $this->order));

        $response->assertOk();
        // Nama Pelanggan / Institusi & PIC
        $response->assertSee('PT Makmur Sentosa Bersama');
        $response->assertSee('PIC / Kontak');
        // Nomor Telepon / WhatsApp
        $response->assertSee('081298765432');
        // Email
        $response->assertSee('kontak@makmursentosa.com');
        // Alamat Pengiriman
        $response->assertSee('Gedung Wisma Niaga Lt. 8, Jl. Thamrin No. 12, Jakarta Pusat');
    }

    public function test_order_detail_displays_invoice_number_delivery_date_and_special_notes(): void
    {
        $response = $this->actingAs($this->admin)->get(route('catering-orders.show', $this->order));

        $response->assertOk();
        // Nomor Invoice Pesanan
        $response->assertSee('ORD-20261017-0099');
        $response->assertSee('Nomor Invoice');

        // Tanggal Pengiriman
        $response->assertSee('25 October 2026');

        // Catatan Khusus
        $response->assertSee('Tolong sediakan sendok garpu kayu ramah lingkungan dan sambal dipisah wadahnya.');
    }

    public function test_order_detail_displays_menu_items_table_with_prices_quantities_and_subtotals(): void
    {
        $response = $this->actingAs($this->admin)->get(route('catering-orders.show', $this->order));

        $response->assertOk();

        // Menu A
        $response->assertSee('Paket Nasi Liwet Ayam Bakar Komplit');
        $response->assertSee('Ayam bakar sambal terpisah');
        $response->assertSee('Rp 45.000');
        $response->assertSee('Rp 2.250.000');

        // Menu B
        $response->assertSee('Puding Pandan Organik Gula Aren');
        $response->assertSee('Dingin sebelum dikirim');
        $response->assertSee('Rp 15.000');
        $response->assertSee('Rp 750.000');

        // Total Porsi & Grand Total
        $response->assertSee('100 Porsi');
        $response->assertSee('Rp 3.000.000');
    }

    public function test_order_detail_has_action_buttons_for_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('catering-orders.show', $this->order));

        $response->assertOk();

        // Tombol Cetak / Lihat Ringkasan Invoice
        $response->assertSee('Lihat Ringkasan Invoice');
        $response->assertSee('Cetak Invoice');

        // Tombol Edit Pesanan
        $response->assertSee('Edit Pesanan');
        $response->assertSee(route('catering-orders.edit', $this->order));

        // Tombol Batalkan Pesanan
        $response->assertSee('Batalkan Pesanan');
    }

    public function test_admin_can_cancel_catering_order_via_action(): void
    {
        $this->assertEquals('pending', $this->order->status);

        $response = $this->actingAs($this->admin)->patch(
            route('catering-orders.update-status', $this->order),
            ['status' => 'cancelled']
        );

        $response->assertRedirect();
        $this->assertEquals('cancelled', $this->order->fresh()->status);

        // Setelah dibatalkan, halaman detail menampilkan badge dibatalkan
        $detailResponse = $this->actingAs($this->admin)->get(route('catering-orders.show', $this->order));
        $detailResponse->assertSee('Dibatalkan');
        $detailResponse->assertSee('Pesanan Telah Dibatalkan');
    }

    public function test_non_admin_cannot_see_edit_or_cancel_action_buttons(): void
    {
        $response = $this->actingAs($this->owner)->get(route('catering-orders.show', $this->order));

        $response->assertOk();
        $response->assertDontSee('Edit Pesanan');
        $response->assertDontSee('Batalkan Pesanan');
        // Namun tetap bisa melihat tombol cetak/ringkasan invoice
        $response->assertSee('Lihat Ringkasan Invoice');
        $response->assertSee('Cetak Invoice');
    }

    public function test_show_returns_404_when_order_does_not_exist(): void
    {
        $response = $this->actingAs($this->admin)->get('/catering-orders/999999');

        $response->assertNotFound();
    }

    public function test_admin_can_access_edit_page_and_update_catering_order(): void
    {
        $editResponse = $this->actingAs($this->admin)->get(route('catering-orders.edit', $this->order));

        $editResponse->assertOk();
        $editResponse->assertSee('Edit Pesanan Katering');
        $editResponse->assertSee($this->order->order_number);

        $updateResponse = $this->actingAs($this->admin)->put(
            route('catering-orders.update', $this->order),
            [
                'customer_id' => $this->customer->id,
                'delivery_date' => '2026-10-28',
                'status' => 'confirmed',
                'notes' => 'Catatan pesanan diperbarui oleh admin.',
            ]
        );

        $updateResponse->assertRedirect(route('catering-orders.show', $this->order));

        $this->assertDatabaseHas('catering_orders', [
            'id' => $this->order->id,
            'delivery_date' => '2026-10-28 00:00:00',
            'status' => 'confirmed',
            'notes' => 'Catatan pesanan diperbarui oleh admin.',
        ]);
    }
}
