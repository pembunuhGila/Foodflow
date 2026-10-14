<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Ingredient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IngredientCrudTest extends TestCase
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
        $response = $this->get(route('ingredients.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_all_authenticated_roles_can_view_ingredients_index(): void
    {
        Ingredient::factory()->count(3)->create();

        foreach ([$this->admin, $this->gudang, $this->produksi, $this->owner] as $user) {
            $response = $this->actingAs($user)->get(route('ingredients.index'));
            $response->assertOk();
            $response->assertViewIs('ingredients.index');
        }
    }

    public function test_all_authenticated_roles_can_view_ingredient_detail(): void
    {
        $ingredient = Ingredient::factory()->create([
            'name' => 'Beras Merah Premium',
        ]);

        foreach ([$this->admin, $this->gudang, $this->produksi, $this->owner] as $user) {
            $response = $this->actingAs($user)->get(route('ingredients.show', $ingredient));
            $response->assertOk();
            $response->assertSee('Beras Merah Premium');
        }
    }

    public function test_only_admin_and_gudang_can_access_create_page(): void
    {
        $this->actingAs($this->admin)->get(route('ingredients.create'))->assertOk();
        $this->actingAs($this->gudang)->get(route('ingredients.create'))->assertOk();

        $this->actingAs($this->produksi)->get(route('ingredients.create'))->assertForbidden();
        $this->actingAs($this->owner)->get(route('ingredients.create'))->assertForbidden();
    }

    public function test_admin_and_gudang_can_store_new_ingredient(): void
    {
        $payload = [
            'name' => 'Minyak Kelapa Murni',
            'category' => 'Bumbu & Minyak',
            'unit' => 'liter',
            'minimum_stock' => 15.00,
            'notes' => 'Cold pressed coconut oil',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->gudang)->post(route('ingredients.store'), $payload);

        $response->assertRedirect(route('ingredients.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('ingredients', [
            'name' => 'Minyak Kelapa Murni',
            'unit' => 'liter',
            'minimum_stock' => 15.00,
        ]);
    }

    public function test_produksi_and_owner_cannot_store_ingredient(): void
    {
        $payload = [
            'name' => 'Bawang Bombay',
            'unit' => 'kg',
            'minimum_stock' => 5,
        ];

        $this->actingAs($this->produksi)->post(route('ingredients.store'), $payload)->assertForbidden();
        $this->actingAs($this->owner)->post(route('ingredients.store'), $payload)->assertForbidden();
    }

    public function test_admin_and_gudang_can_update_ingredient(): void
    {
        $ingredient = Ingredient::factory()->create([
            'name' => 'Daging Ayam',
            'unit' => 'kg',
            'minimum_stock' => 20,
        ]);

        $updatePayload = [
            'name' => 'Dada Ayam Fillet Tanpa Kulit',
            'category' => 'Protein Hewani',
            'unit' => 'kg',
            'minimum_stock' => 25.00,
            'notes' => 'Daging dada ayam segar',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)->put(route('ingredients.update', $ingredient), $updatePayload);

        $response->assertRedirect(route('ingredients.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('ingredients', [
            'id' => $ingredient->id,
            'name' => 'Dada Ayam Fillet Tanpa Kulit',
            'minimum_stock' => 25.00,
        ]);
    }

    public function test_admin_and_gudang_can_toggle_ingredient_status(): void
    {
        $ingredient = Ingredient::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->gudang)->patch(route('ingredients.toggle-status', $ingredient));
        $response->assertRedirect();
        $this->assertFalse($ingredient->fresh()->is_active);

        $response = $this->actingAs($this->gudang)->patch(route('ingredients.toggle-status', $ingredient));
        $response->assertRedirect();
        $this->assertTrue($ingredient->fresh()->is_active);
    }

    public function test_admin_and_gudang_can_delete_ingredient(): void
    {
        $ingredient = Ingredient::factory()->create(['name' => 'Bahan Untuk Dihapus']);

        $response = $this->actingAs($this->admin)->delete(route('ingredients.destroy', $ingredient));

        $response->assertRedirect(route('ingredients.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('ingredients', [
            'id' => $ingredient->id,
        ]);
    }

    public function test_ingredients_can_be_filtered_by_search_category_and_status(): void
    {
        $activeVeggie = Ingredient::factory()->create([
            'name' => 'Bayam Hijau Organik',
            'category' => 'Sayuran',
            'is_active' => true,
        ]);

        $inactiveMeat = Ingredient::factory()->create([
            'name' => 'Daging Sapi Slice',
            'category' => 'Protein Hewani',
            'is_active' => false,
        ]);

        // Filter search
        $response = $this->actingAs($this->admin)->get(route('ingredients.index', ['search' => 'Bayam']));
        $response->assertSee('Bayam Hijau Organik');
        $response->assertDontSee('Daging Sapi Slice');

        // Filter category
        $response = $this->actingAs($this->admin)->get(route('ingredients.index', ['category' => 'Protein Hewani']));
        $response->assertSee('Daging Sapi Slice');
        $response->assertDontSee('Bayam Hijau Organik');

        // Filter status inactive
        $response = $this->actingAs($this->admin)->get(route('ingredients.index', ['status' => 'inactive']));
        $response->assertSee('Daging Sapi Slice');
        $response->assertDontSee('Bayam Hijau Organik');
    }
}
