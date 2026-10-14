<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SupplierCrudTest extends TestCase
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
        $response = $this->get(route('suppliers.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_gudang_and_owner_can_view_suppliers_list(): void
    {
        Supplier::factory()->count(2)->create();

        foreach ([$this->admin, $this->gudang, $this->owner] as $user) {
            $response = $this->actingAs($user)->get(route('suppliers.index'));
            $response->assertOk();
            $response->assertViewIs('suppliers.index');
        }
    }

    public function test_produksi_cannot_access_suppliers(): void
    {
        $response = $this->actingAs($this->produksi)->get(route('suppliers.index'));
        $response->assertForbidden();
    }

    public function test_only_admin_can_access_create_page(): void
    {
        $this->actingAs($this->admin)->get(route('suppliers.create'))->assertOk();

        $this->actingAs($this->gudang)->get(route('suppliers.create'))->assertForbidden();
        $this->actingAs($this->produksi)->get(route('suppliers.create'))->assertForbidden();
        $this->actingAs($this->owner)->get(route('suppliers.create'))->assertForbidden();
    }

    public function test_admin_can_store_new_supplier(): void
    {
        $payload = [
            'name' => 'PT Sumber Pangan Mandiri',
            'phone' => '081234567899',
            'email' => 'sumber.pangan@supplier.id',
            'address' => 'Jl. Pergudangan No. 5, Jakarta Barat',
            'notes' => 'Pemasok telur dan bumbu',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)->post(route('suppliers.store'), $payload);

        $response->assertRedirect(route('suppliers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('vendors', [
            'name' => 'PT Sumber Pangan Mandiri',
            'email' => 'sumber.pangan@supplier.id',
            'is_active' => true,
        ]);
    }

    public function test_other_roles_cannot_store_supplier(): void
    {
        $payload = [
            'name' => 'Toko Mitra Tani',
        ];

        $this->actingAs($this->gudang)->post(route('suppliers.store'), $payload)->assertForbidden();
        $this->actingAs($this->produksi)->post(route('suppliers.store'), $payload)->assertForbidden();
        $this->actingAs($this->owner)->post(route('suppliers.store'), $payload)->assertForbidden();
    }

    public function test_admin_can_update_supplier(): void
    {
        $supplier = Supplier::factory()->create([
            'name' => 'CV Lama Jaya',
        ]);

        $updatePayload = [
            'name' => 'CV Baru Jaya Makmur',
            'phone' => '089988776655',
            'email' => 'baru.jaya@supplier.id',
            'address' => 'Jl. Sukses Selalu No. 9',
            'notes' => 'Mitra baru',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)->put(route('suppliers.update', $supplier), $updatePayload);

        $response->assertRedirect(route('suppliers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('vendors', [
            'id' => $supplier->id,
            'name' => 'CV Baru Jaya Makmur',
        ]);
    }

    public function test_admin_can_toggle_supplier_status(): void
    {
        $supplier = Supplier::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->admin)->patch(route('suppliers.toggle-status', $supplier));
        $response->assertRedirect();
        $this->assertFalse($supplier->fresh()->is_active);

        $response = $this->actingAs($this->admin)->patch(route('suppliers.toggle-status', $supplier));
        $response->assertRedirect();
        $this->assertTrue($supplier->fresh()->is_active);
    }

    public function test_admin_can_delete_supplier_without_orders(): void
    {
        $supplier = Supplier::factory()->create(['name' => 'Supplier Tanpa Transaksi']);

        $response = $this->actingAs($this->admin)->delete(route('suppliers.destroy', $supplier));

        $response->assertRedirect(route('suppliers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('vendors', [
            'id' => $supplier->id,
        ]);
    }

    public function test_supplier_with_orders_is_soft_deactivated_instead_of_hard_deleted(): void
    {
        $supplier = Supplier::factory()->create([
            'name' => 'Supplier Bertransaksi',
            'is_active' => true,
        ]);

        if (! Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function ($table) {
                $table->id();
                $table->foreignId('vendor_id');
                $table->timestamps();
            });
        }

        DB::table('purchase_orders')->insert([
            'vendor_id' => $supplier->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('suppliers.destroy', $supplier));

        $response->assertRedirect(route('suppliers.index'));

        // Data tidak terhapus fisik dari tabel vendors, melainkan dinonaktifkan (is_active = false)
        $this->assertDatabaseHas('vendors', [
            'id' => $supplier->id,
            'is_active' => false,
        ]);
    }

    public function test_suppliers_can_be_filtered_by_search_and_status(): void
    {
        $activeSupplier = Supplier::factory()->create([
            'name' => 'PT Beras Nusantara',
            'is_active' => true,
        ]);

        $inactiveSupplier = Supplier::factory()->create([
            'name' => 'CV Sayur Makmur',
            'is_active' => false,
        ]);

        // Filter search
        $response = $this->actingAs($this->admin)->get(route('suppliers.index', ['search' => 'Beras']));
        $response->assertSee('PT Beras Nusantara');
        $response->assertDontSee('CV Sayur Makmur');

        // Filter status inactive
        $response = $this->actingAs($this->admin)->get(route('suppliers.index', ['status' => 'inactive']));
        $response->assertSee('CV Sayur Makmur');
        $response->assertDontSee('PT Beras Nusantara');
    }
}
