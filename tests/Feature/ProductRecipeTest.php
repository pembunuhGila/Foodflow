<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductRecipeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $gudang;

    private User $produksi;

    private User $owner;

    private Product $product;

    private Ingredient $ingredient1;

    private Ingredient $ingredient2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
        $this->gudang = User::factory()->create(['role' => UserRole::Gudang]);
        $this->produksi = User::factory()->create(['role' => UserRole::Produksi]);
        $this->owner = User::factory()->create(['role' => UserRole::Owner]);

        $this->product = Product::factory()->create([
            'name' => 'Paket Nasi Kuning Cakalang',
            'unit' => 'porsi',
            'selling_price' => 28000,
        ]);

        $this->ingredient1 = Ingredient::factory()->create([
            'name' => 'Beras Organik',
            'unit' => 'kg',
        ]);

        $this->ingredient2 = Ingredient::factory()->create([
            'name' => 'Ikan Cakalang Segar',
            'unit' => 'kg',
        ]);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('products.recipe.edit', $this->product));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_and_produksi_can_view_recipe_builder_page(): void
    {
        foreach ([$this->admin, $this->produksi] as $user) {
            $response = $this->actingAs($user)->get(route('products.recipe.edit', $this->product));
            $response->assertOk();
            $response->assertViewIs('products.recipe');
            $response->assertSee('Paket Nasi Kuning Cakalang');
            $response->assertSee('Beras Organik');
        }
    }

    public function test_gudang_and_owner_cannot_access_recipe_builder_page(): void
    {
        foreach ([$this->gudang, $this->owner] as $user) {
            $response = $this->actingAs($user)->get(route('products.recipe.edit', $this->product));
            $response->assertForbidden();
        }
    }

    public function test_admin_and_produksi_can_save_recipe_composition(): void
    {
        $payload = [
            'items' => [
                [
                    'ingredient_id' => $this->ingredient1->id,
                    'quantity_per_unit' => 0.120,
                    'unit' => 'kg',
                ],
                [
                    'ingredient_id' => $this->ingredient2->id,
                    'quantity_per_unit' => 0.150,
                    'unit' => 'kg',
                ],
            ],
        ];

        $response = $this->actingAs($this->produksi)->put(route('products.recipe.update', $this->product), $payload);

        $response->assertRedirect(route('products.show', $this->product));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('product_ingredients', [
            'product_id' => $this->product->id,
            'ingredient_id' => $this->ingredient1->id,
            'quantity_per_unit' => 0.120,
            'unit' => 'kg',
        ]);

        $this->assertDatabaseHas('product_ingredients', [
            'product_id' => $this->product->id,
            'ingredient_id' => $this->ingredient2->id,
            'quantity_per_unit' => 0.150,
            'unit' => 'kg',
        ]);

        $this->assertEquals(2, $this->product->recipeItems()->count());
    }

    public function test_duplicate_ingredients_in_same_recipe_are_rejected(): void
    {
        $payload = [
            'items' => [
                [
                    'ingredient_id' => $this->ingredient1->id,
                    'quantity_per_unit' => 0.100,
                    'unit' => 'kg',
                ],
                [
                    'ingredient_id' => $this->ingredient1->id, // Duplikat bahan yang sama
                    'quantity_per_unit' => 0.050,
                    'unit' => 'kg',
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->put(route('products.recipe.update', $this->product), $payload);

        $response->assertSessionHasErrors(['items.0.ingredient_id', 'items.1.ingredient_id']);
    }

    public function test_quantity_per_unit_must_be_greater_than_zero(): void
    {
        $payload = [
            'items' => [
                [
                    'ingredient_id' => $this->ingredient1->id,
                    'quantity_per_unit' => 0, // Tidak boleh <= 0
                    'unit' => 'kg',
                ],
            ],
        ];

        $response = $this->actingAs($this->produksi)->put(route('products.recipe.update', $this->product), $payload);

        $response->assertSessionHasErrors(['items.0.quantity_per_unit']);
    }

    public function test_all_four_roles_can_view_recipe_table_on_product_show(): void
    {
        // Pasang resep terlebih dahulu
        $this->product->ingredients()->attach($this->ingredient1->id, [
            'quantity_per_unit' => 0.200,
            'unit' => 'kg',
        ]);

        foreach ([$this->admin, $this->gudang, $this->produksi, $this->owner] as $user) {
            $response = $this->actingAs($user)->get(route('products.show', $this->product));
            $response->assertOk();
            $response->assertSee('Beras Organik');
            $response->assertSee('0,2');
            $response->assertSee('Komposisi Resep Bahan Baku');
        }
    }

    public function test_recipe_can_be_updated_and_cleared(): void
    {
        // 1. Pasang resep awal
        $this->product->ingredients()->attach($this->ingredient1->id, [
            'quantity_per_unit' => 0.100,
            'unit' => 'kg',
        ]);
        $this->assertEquals(1, $this->product->recipeItems()->count());

        // 2. Update dengan bahan baru
        $response = $this->actingAs($this->admin)->put(route('products.recipe.update', $this->product), [
            'items' => [
                [
                    'ingredient_id' => $this->ingredient2->id,
                    'quantity_per_unit' => 0.250,
                    'unit' => 'kg',
                ],
            ],
        ]);
        $response->assertRedirect(route('products.show', $this->product));
        $this->assertEquals(1, $this->product->fresh()->recipeItems()->count());
        $this->assertEquals($this->ingredient2->id, $this->product->fresh()->recipeItems()->first()->ingredient_id);

        // 3. Kosongkan resep
        $response = $this->actingAs($this->admin)->put(route('products.recipe.update', $this->product), [
            'items' => [],
        ]);
        $response->assertRedirect(route('products.show', $this->product));
        $this->assertEquals(0, $this->product->fresh()->recipeItems()->count());
    }

    public function test_product_with_recipe_items_is_soft_deactivated_when_delete_attempted(): void
    {
        // Pasang resep
        $this->product->ingredients()->attach($this->ingredient1->id, [
            'quantity_per_unit' => 0.100,
            'unit' => 'kg',
        ]);

        // Coba hapus menu yang sudah ada resepnya
        $response = $this->actingAs($this->admin)->delete(route('products.destroy', $this->product));

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('warning');

        // Pastikan tidak dihapus permanen, melainkan dinonaktifkan
        $this->assertDatabaseHas('products', [
            'id' => $this->product->id,
            'is_active' => false,
        ]);
    }
}
