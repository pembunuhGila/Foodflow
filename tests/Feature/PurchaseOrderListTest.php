<?php

namespace Tests\Feature;

use App\Enums\PurchaseOrderStatus;
use App\Enums\UserRole;
use App\Models\Ingredient;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderListTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $gudang;

    private User $produksi;

    private User $owner;

    private Supplier $vendor;

    private Ingredient $ingredient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
        $this->gudang = User::factory()->create(['role' => UserRole::Gudang]);
        $this->produksi = User::factory()->create(['role' => UserRole::Produksi]);
        $this->owner = User::factory()->create(['role' => UserRole::Owner]);

        $this->vendor = Supplier::factory()->create([
            'name' => 'CV Sumber Berkah Sayur',
            'is_active' => true,
        ]);

        $this->ingredient = Ingredient::factory()->create([
            'name' => 'Wortel Brastagi',
            'unit' => 'kg',
            'is_active' => true,
        ]);
    }

    private function createPurchaseOrder(array $attributes = []): PurchaseOrder
    {
        $po = PurchaseOrder::create(array_merge([
            'vendor_id' => $this->vendor->id,
            'created_by' => $this->admin->id,
            'order_number' => PurchaseOrder::generateOrderNumber(),
            'order_date' => now()->format('Y-m-d'),
            'expected_date' => now()->addDays(2)->format('Y-m-d'),
            'status' => PurchaseOrderStatus::Draft,
            'notes' => 'Catatan pesanan uji coba',
        ], $attributes));

        PurchaseOrderDetail::create([
            'purchase_order_id' => $po->id,
            'ingredient_id' => $this->ingredient->id,
            'quantity' => 10,
            'unit' => 'kg',
            'price' => 15000,
            'subtotal' => 150000,
        ]);

        return $po;
    }

    public function test_guest_is_redirected_to_login_when_accessing_po_index(): void
    {
        $response = $this->get(route('purchase-orders.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_gudang_and_owner_can_access_po_index(): void
    {
        $po = $this->createPurchaseOrder();

        $this->actingAs($this->admin)
            ->get(route('purchase-orders.index'))
            ->assertOk()
            ->assertSee($po->order_number);

        $this->actingAs($this->gudang)
            ->get(route('purchase-orders.index'))
            ->assertOk()
            ->assertSee($po->order_number);

        $this->actingAs($this->owner)
            ->get(route('purchase-orders.index'))
            ->assertOk()
            ->assertSee($po->order_number);
    }

    public function test_produksi_cannot_access_po_index(): void
    {
        $this->actingAs($this->produksi)
            ->get(route('purchase-orders.index'))
            ->assertForbidden();
    }

    public function test_admin_gudang_and_owner_can_view_po_detail(): void
    {
        $po = $this->createPurchaseOrder();

        $this->actingAs($this->admin)
            ->get(route('purchase-orders.show', $po))
            ->assertOk()
            ->assertSee($po->order_number)
            ->assertSee('Wortel Brastagi')
            ->assertSee('Cetak Surat PO');

        $this->actingAs($this->gudang)
            ->get(route('purchase-orders.show', $po))
            ->assertOk()
            ->assertSee($po->order_number);

        $this->actingAs($this->owner)
            ->get(route('purchase-orders.show', $po))
            ->assertOk()
            ->assertSee($po->order_number);
    }

    public function test_po_index_can_be_filtered_by_status(): void
    {
        $poDraft = $this->createPurchaseOrder(['order_number' => 'PO-20261027-0001', 'status' => PurchaseOrderStatus::Draft]);
        $poOrdered = $this->createPurchaseOrder(['order_number' => 'PO-20261027-0002', 'status' => PurchaseOrderStatus::Ordered]);

        $response = $this->actingAs($this->admin)
            ->get(route('purchase-orders.index', ['status' => 'ordered']));

        $response->assertOk()
            ->assertSee($poOrdered->order_number)
            ->assertDontSee($poDraft->order_number);
    }

    public function test_po_index_can_be_searched_by_order_number_or_vendor(): void
    {
        $vendorLain = Supplier::factory()->create(['name' => 'Toko Daging Segar Jaya']);
        $po1 = $this->createPurchaseOrder(['order_number' => 'PO-20261027-0099', 'vendor_id' => $vendorLain->id]);
        $po2 = $this->createPurchaseOrder(['order_number' => 'PO-20261027-0011']);

        // Search by vendor name
        $response = $this->actingAs($this->admin)
            ->get(route('purchase-orders.index', ['search' => 'Daging Segar']));

        $response->assertOk()
            ->assertSee($po1->order_number)
            ->assertDontSee($po2->order_number);
    }

    public function test_admin_can_update_po_status(): void
    {
        $po = $this->createPurchaseOrder(['status' => PurchaseOrderStatus::Draft]);

        $response = $this->actingAs($this->admin)
            ->patch(route('purchase-orders.update-status', $po), [
                'status' => 'ordered',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('purchase_orders', [
            'id' => $po->id,
            'status' => 'ordered',
        ]);
    }

    public function test_non_admin_cannot_update_po_status(): void
    {
        $po = $this->createPurchaseOrder(['status' => PurchaseOrderStatus::Draft]);

        $this->actingAs($this->gudang)
            ->patch(route('purchase-orders.update-status', $po), [
                'status' => 'ordered',
            ])
            ->assertForbidden();

        $this->actingAs($this->produksi)
            ->patch(route('purchase-orders.update-status', $po), [
                'status' => 'ordered',
            ])
            ->assertForbidden();
    }
}
