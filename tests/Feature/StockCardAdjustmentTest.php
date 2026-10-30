<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Ingredient;
use App\Models\IngredientStockTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockCardAdjustmentTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $gudang;

    private User $produksi;

    private User $owner;

    private Ingredient $ingredient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
        $this->gudang = User::factory()->create(['role' => UserRole::Gudang]);
        $this->produksi = User::factory()->create(['role' => UserRole::Produksi]);
        $this->owner = User::factory()->create(['role' => UserRole::Owner]);

        $this->ingredient = Ingredient::factory()->create([
            'name' => 'Garam Beryodium Halus',
            'unit' => 'kg',
            'current_stock' => 50.0,
            'is_active' => true,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('stock-cards.index'))->assertRedirect(route('login'));
        $this->get(route('stock-adjustments.create'))->assertRedirect(route('login'));
    }

    public function test_admin_gudang_and_owner_can_view_stock_cards_index(): void
    {
        // Catat satu mutasi dummy
        IngredientStockTransaction::create([
            'ingredient_id' => $this->ingredient->id,
            'transaction_type' => 'in',
            'quantity' => 50.0,
            'transaction_date' => now()->format('Y-m-d'),
            'reference_type' => 'initial_stock',
            'notes' => 'Saldo awal stok',
            'created_by' => $this->admin->id,
        ]);

        foreach ([$this->admin, $this->gudang, $this->owner] as $user) {
            $this->actingAs($user)
                ->get(route('stock-cards.index'))
                ->assertOk()
                ->assertSee('Garam Beryodium Halus')
                ->assertSee('Buku Kartu Mutasi Stok');
        }
    }

    public function test_produksi_cannot_access_stock_cards_index(): void
    {
        $this->actingAs($this->produksi)
            ->get(route('stock-cards.index'))
            ->assertForbidden();
    }

    public function test_gudang_and_admin_can_access_adjustment_form(): void
    {
        $this->actingAs($this->gudang)
            ->get(route('stock-adjustments.create'))
            ->assertOk()
            ->assertSee('Penyesuaian Stok Fisik');

        $this->actingAs($this->admin)
            ->get(route('stock-adjustments.create'))
            ->assertOk();
    }

    public function test_owner_and_produksi_cannot_access_adjustment_form(): void
    {
        $this->actingAs($this->owner)
            ->get(route('stock-adjustments.create'))
            ->assertForbidden();

        $this->actingAs($this->produksi)
            ->get(route('stock-adjustments.create'))
            ->assertForbidden();
    }

    public function test_gudang_can_perform_stock_adjustment_when_stock_shrinks(): void
    {
        $payload = [
            'ingredient_id' => $this->ingredient->id,
            'physical_count' => 48.5, // Saldo sistem 50 -> fisik 48.5 (selisih -1.5 kg)
            'adjustment_date' => now()->format('Y-m-d'),
            'notes' => 'Susut akibat kemasan lembap saat opname fisik',
        ];

        $response = $this->actingAs($this->gudang)
            ->post(route('stock-adjustments.store'), $payload);

        $response->assertRedirect(route('stock-cards.index', ['ingredient_id' => $this->ingredient->id]));

        // Transaksi mutasi tipe 'adjustment' tercatat
        $this->assertDatabaseHas('ingredient_stock_transactions', [
            'ingredient_id' => $this->ingredient->id,
            'transaction_type' => 'adjustment',
            'quantity' => -1.5,
            'created_by' => $this->gudang->id,
        ]);

        // Saldo stok diupdate menjadi nilai fisik
        $this->assertEquals(48.5, (float) $this->ingredient->fresh()->current_stock);
    }

    public function test_notes_reason_is_mandatory_for_stock_adjustment(): void
    {
        $payload = [
            'ingredient_id' => $this->ingredient->id,
            'physical_count' => 45.0,
            'adjustment_date' => now()->format('Y-m-d'),
            'notes' => '', // Kosong -> harus gagal validasi (FR-26)
        ];

        $response = $this->actingAs($this->gudang)
            ->post(route('stock-adjustments.store'), $payload);

        $response->assertSessionHasErrors('notes');
        $this->assertEquals(50.0, (float) $this->ingredient->fresh()->current_stock);
    }
}
