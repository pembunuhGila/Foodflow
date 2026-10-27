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

class MaterialReceiptTest extends TestCase
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
            'name' => 'PT Agrikultur Nusantara',
            'is_active' => true,
        ]);

        $this->ingredient = Ingredient::factory()->create([
            'name' => 'Kentang Dieng Super',
            'unit' => 'kg',
            'current_stock' => 10,
            'is_active' => true,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('material-receipts.index'))->assertRedirect(route('login'));
        $this->get(route('material-receipts.create'))->assertRedirect(route('login'));
    }

    public function test_gudang_and_admin_can_access_create_receipt_page(): void
    {
        $this->actingAs($this->gudang)
            ->get(route('material-receipts.create'))
            ->assertOk()
            ->assertSee('Pemeriksaan Fisik');

        $this->actingAs($this->admin)
            ->get(route('material-receipts.create'))
            ->assertOk();
    }

    public function test_produksi_and_owner_cannot_access_create_receipt_page(): void
    {
        $this->actingAs($this->produksi)
            ->get(route('material-receipts.create'))
            ->assertForbidden();

        $this->actingAs($this->owner)
            ->get(route('material-receipts.create'))
            ->assertForbidden();
    }

    public function test_gudang_can_record_physical_receipt_with_conditions(): void
    {
        $po = PurchaseOrder::create([
            'vendor_id' => $this->vendor->id,
            'created_by' => $this->admin->id,
            'order_number' => 'PO-20261027-0001',
            'order_date' => now()->format('Y-m-d'),
            'expected_date' => now()->addDays(2)->format('Y-m-d'),
            'status' => PurchaseOrderStatus::Ordered,
        ]);

        PurchaseOrderDetail::create([
            'purchase_order_id' => $po->id,
            'ingredient_id' => $this->ingredient->id,
            'quantity' => 20,
            'unit' => 'kg',
            'price' => 12000,
            'subtotal' => 240000,
        ]);

        $payload = [
            'purchase_order_id' => $po->id,
            'receipt_date' => now()->format('Y-m-d'),
            'notes' => 'Diterima dari ekspedisi truk sayur',
            'items' => [
                [
                    'ingredient_id' => $this->ingredient->id,
                    'quantity' => 18,
                    'unit' => 'kg',
                    'condition' => 'baik',
                    'notes' => '18 kg mulus',
                ],
                [
                    'ingredient_id' => $this->ingredient->id,
                    'quantity' => 2,
                    'unit' => 'kg',
                    'condition' => 'rusak',
                    'notes' => '2 kg busuk di jalan',
                ],
            ],
        ];

        $response = $this->actingAs($this->gudang)
            ->post(route('material-receipts.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('material_receipts', [
            'purchase_order_id' => $po->id,
            'received_by' => $this->gudang->id,
        ]);

        $this->assertDatabaseHas('material_receipt_details', [
            'ingredient_id' => $this->ingredient->id,
            'quantity' => 18,
            'condition' => 'baik',
        ]);

        $this->assertDatabaseHas('material_receipt_details', [
            'ingredient_id' => $this->ingredient->id,
            'quantity' => 2,
            'condition' => 'rusak',
        ]);

        // PO status diperbarui menjadi received
        $this->assertEquals(PurchaseOrderStatus::Received, $po->fresh()->status);
    }
}
