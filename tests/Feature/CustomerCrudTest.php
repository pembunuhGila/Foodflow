<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\CateringOrder;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $gudang;

    private User $produksi;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
        $this->gudang = User::factory()->create(['role' => UserRole::Gudang]);
        $this->produksi = User::factory()->create(['role' => UserRole::Produksi]);
        $this->owner = User::factory()->create(['role' => UserRole::Owner]);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('customers.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_and_owner_can_view_customers_list(): void
    {
        Customer::factory()->count(2)->create();

        foreach ([$this->admin, $this->owner] as $user) {
            $response = $this->actingAs($user)->get(route('customers.index'));
            $response->assertOk();
            $response->assertViewIs('customers.index');
        }
    }

    public function test_gudang_and_produksi_cannot_access_customers(): void
    {
        foreach ([$this->gudang, $this->produksi] as $user) {
            $response = $this->actingAs($user)->get(route('customers.index'));
            $response->assertForbidden();
        }
    }

    public function test_only_admin_can_access_create_page(): void
    {
        $this->actingAs($this->admin)->get(route('customers.create'))->assertOk();

        $this->actingAs($this->gudang)->get(route('customers.create'))->assertForbidden();
        $this->actingAs($this->produksi)->get(route('customers.create'))->assertForbidden();
        $this->actingAs($this->owner)->get(route('customers.create'))->assertForbidden();
    }

    public function test_admin_can_store_new_customer(): void
    {
        $payload = [
            'name' => 'SDIT Insan Teladan',
            'phone' => '082155667788',
            'email' => 'admin@insanteladan.sch.id',
            'address' => 'Jl. Pendidikan Utama No. 9, Jakarta Selatan',
            'notes' => 'Pesanan katering sehat 120 porsi setiap hari sekolah',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)->post(route('customers.store'), $payload);

        $response->assertRedirect(route('customers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('customers', [
            'name' => 'SDIT Insan Teladan',
            'email' => 'admin@insanteladan.sch.id',
            'is_active' => true,
        ]);
    }

    public function test_other_roles_cannot_store_customer(): void
    {
        $payload = [
            'name' => 'PT Semesta Sehat',
        ];

        $this->actingAs($this->gudang)->post(route('customers.store'), $payload)->assertForbidden();
        $this->actingAs($this->produksi)->post(route('customers.store'), $payload)->assertForbidden();
        $this->actingAs($this->owner)->post(route('customers.store'), $payload)->assertForbidden();
    }

    public function test_admin_can_update_customer(): void
    {
        $customer = Customer::factory()->create([
            'name' => 'Klinik Lama Sehat',
        ]);

        $updatePayload = [
            'name' => 'Klinik Pratama Rawat Inap Sehat',
            'phone' => '087788990011',
            'email' => 'gizi@pratamasehat.id',
            'address' => 'Jl. Kesehatan Baru No. 100',
            'notes' => 'Menu diet rendah garam',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)->put(route('customers.update', $customer), $updatePayload);

        $response->assertRedirect(route('customers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Klinik Pratama Rawat Inap Sehat',
        ]);
    }

    public function test_admin_can_toggle_customer_status(): void
    {
        $customer = Customer::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->admin)->patch(route('customers.toggle-status', $customer));
        $response->assertRedirect();
        $this->assertFalse($customer->fresh()->is_active);

        $response = $this->actingAs($this->admin)->patch(route('customers.toggle-status', $customer));
        $response->assertRedirect();
        $this->assertTrue($customer->fresh()->is_active);
    }

    public function test_admin_can_delete_customer_without_orders(): void
    {
        $customer = Customer::factory()->create(['name' => 'Customer Belum Pernah Order']);

        $response = $this->actingAs($this->admin)->delete(route('customers.destroy', $customer));

        $response->assertRedirect(route('customers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('customers', [
            'id' => $customer->id,
        ]);
    }

    public function test_customer_with_orders_is_soft_deactivated_instead_of_hard_deleted(): void
    {
        $customer = Customer::factory()->create([
            'name' => 'Pelanggan Tetap Catering',
            'is_active' => true,
        ]);

        CateringOrder::factory()->create([
            'customer_id' => $customer->id,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('customers.destroy', $customer));

        $response->assertRedirect(route('customers.index'));

        // Sesuai BR-02 & UC-26: data tidak terhapus fisik dari tabel customers, melainkan is_active = false
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'is_active' => false,
        ]);
    }

    public function test_customers_can_be_filtered_by_search_and_status(): void
    {
        $activeCustomer = Customer::factory()->create([
            'name' => 'SMA Nusantara Harapan',
            'is_active' => true,
        ]);

        $inactiveCustomer = Customer::factory()->create([
            'name' => 'Klub Komunitas Yoga',
            'is_active' => false,
        ]);

        // Filter search
        $response = $this->actingAs($this->admin)->get(route('customers.index', ['search' => 'Nusantara']));
        $response->assertSee('SMA Nusantara Harapan');
        $response->assertDontSee('Klub Komunitas Yoga');

        // Filter status inactive
        $response = $this->actingAs($this->admin)->get(route('customers.index', ['status' => 'inactive']));
        $response->assertSee('Klub Komunitas Yoga');
        $response->assertDontSee('SMA Nusantara Harapan');
    }
}
