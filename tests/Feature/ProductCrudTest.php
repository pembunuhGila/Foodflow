<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\CateringOrderDetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCrudTest extends TestCase
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
        $response = $this->get(route('products.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_all_four_roles_can_view_products_list(): void
    {
        Product::factory()->count(2)->create();

        foreach ([$this->admin, $this->gudang, $this->produksi, $this->owner] as $user) {
            $response = $this->actingAs($user)->get(route('products.index'));
            $response->assertOk();
            $response->assertViewIs('products.index');
        }
    }

    public function test_admin_and_produksi_can_access_create_page(): void
    {
        foreach ([$this->admin, $this->produksi] as $user) {
            $response = $this->actingAs($user)->get(route('products.create'));
            $response->assertOk();
            $response->assertViewIs('products.create');
        }
    }

    public function test_gudang_and_owner_cannot_access_create_page(): void
    {
        foreach ([$this->gudang, $this->owner] as $user) {
            $response = $this->actingAs($user)->get(route('products.create'));
            $response->assertForbidden();
        }
    }

    public function test_produksi_can_store_product_with_nutrition_facts(): void
    {
        $payload = [
            'name' => 'Nasi Bakar Beras Merah Cakalang',
            'description' => 'Nasi bakar beras merah rempah dengan isian ikan cakalang suwir pedas manis.',
            'unit' => 'porsi',
            'selling_price' => 32000,
            'is_active' => '1',
            'calories' => 450,
            'protein' => 30.5,
            'fat' => 8.2,
            'carbohydrates' => 52.0,
            'dietary_fiber' => 5.4,
        ];

        $response = $this->actingAs($this->produksi)->post(route('products.store'), $payload);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'name' => 'Nasi Bakar Beras Merah Cakalang',
            'unit' => 'porsi',
            'is_active' => true,
        ]);

        $product = Product::where('name', 'Nasi Bakar Beras Merah Cakalang')->first();
        $this->assertNotNull($product);
        $this->assertEquals(450.00, (float) $product->calories);
        $this->assertEquals(30.50, (float) $product->protein);
        $this->assertEquals(8.20, (float) $product->fat);
        $this->assertEquals(52.00, (float) $product->carbohydrates);
        $this->assertEquals(5.40, (float) $product->dietary_fiber);
    }

    public function test_admin_can_store_product_without_inputting_nutrition(): void
    {
        // Admin membuat menu dasar tanpa wajib menginput nilai gizi
        $payload = [
            'name' => 'Sup Ayam Bening Sehat',
            'description' => 'Sup ayam kaldu murni tanpa msg.',
            'unit' => 'porsi',
            'selling_price' => 25000,
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->admin)->post(route('products.store'), $payload);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', [
            'name' => 'Sup Ayam Bening Sehat',
            'selling_price' => 25000,
            'calories' => 0,
        ]);
    }

    public function test_produksi_requires_nutrition_fields(): void
    {
        // Bagian produksi wajib mengisi nilai gizi saat menyimpan
        $response = $this->actingAs($this->produksi)->post(route('products.store'), [
            'name' => 'Menu Uji Produksi',
            'unit' => 'porsi',
            'selling_price' => 20000,
            'calories' => '',
            'protein' => '',
            'fat' => '',
            'carbohydrates' => '',
        ]);

        $response->assertSessionHasErrors([
            'calories',
            'protein',
            'fat',
            'carbohydrates',
        ]);
    }

    public function test_all_four_roles_can_view_product_detail_and_nutrition(): void
    {
        $product = Product::factory()->create([
            'name' => 'Salad Buah Sehat Granola',
            'calories' => 220,
            'protein' => 6.5,
        ]);

        foreach ([$this->admin, $this->gudang, $this->produksi, $this->owner] as $user) {
            $response = $this->actingAs($user)->get(route('products.show', $product));
            $response->assertOk();
            $response->assertViewIs('products.show');
            $response->assertSee('Salad Buah Sehat Granola');
            $response->assertSee('220');
        }
    }

    public function test_produksi_can_update_product_and_nutrition(): void
    {
        $product = Product::factory()->create([
            'name' => 'Menu Lama',
            'calories' => 300,
            'protein' => 20,
        ]);

        $payload = [
            'name' => 'Menu Baru Diperbarui',
            'description' => 'Resep baru lebih rendah garam.',
            'unit' => 'box',
            'selling_price' => 38000,
            'is_active' => '1',
            'calories' => 280,
            'protein' => 34,
            'fat' => 6,
            'carbohydrates' => 20,
            'dietary_fiber' => 4,
        ];

        $response = $this->actingAs($this->produksi)->put(route('products.update', $product), $payload);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success');

        $product->refresh();
        $this->assertEquals('Menu Baru Diperbarui', $product->name);
        $this->assertEquals(280.00, (float) $product->calories);
        $this->assertEquals(34.00, (float) $product->protein);
    }

    public function test_admin_update_does_not_overwrite_nutrition_facts(): void
    {
        // Nilai gizi awal sudah diset oleh Ahli Gizi / Produksi
        $product = Product::factory()->create([
            'name' => 'Menu Asli Dapur',
            'selling_price' => 20000,
            'calories' => 500,
            'protein' => 35,
            'fat' => 12,
            'carbohydrates' => 60,
            'dietary_fiber' => 7,
        ]);

        // Admin mengubah nama dan harga jual, mencoba/tidak mengirim gizi
        $payload = [
            'name' => 'Menu Asli Dapur Harga Naik',
            'description' => 'Deskripsi diedit admin.',
            'unit' => 'porsi',
            'selling_price' => 25000,
            'is_active' => '1',
            'calories' => 10, // Nilai gizi diabaikan untuk Admin
            'protein' => 1,
        ];

        $response = $this->actingAs($this->admin)->put(route('products.update', $product), $payload);

        $response->assertRedirect(route('products.index'));
        $product->refresh();

        $this->assertEquals('Menu Asli Dapur Harga Naik', $product->name);
        $this->assertEquals(25000.00, (float) $product->selling_price);

        // Nilai gizi tetap sama persis seperti yang diset bagian produksi!
        $this->assertEquals(500.00, (float) $product->calories);
        $this->assertEquals(35.00, (float) $product->protein);
        $this->assertEquals(12.00, (float) $product->fat);
        $this->assertEquals(60.00, (float) $product->carbohydrates);
        $this->assertEquals(7.00, (float) $product->dietary_fiber);
    }

    public function test_gudang_and_owner_cannot_update_product(): void
    {
        $product = Product::factory()->create();

        foreach ([$this->gudang, $this->owner] as $user) {
            $response = $this->actingAs($user)->put(route('products.update', $product), [
                'name' => 'Menu Ilegal',
                'unit' => 'porsi',
                'selling_price' => 10000,
            ]);

            $response->assertForbidden();
        }
    }

    public function test_admin_and_produksi_can_toggle_product_status(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->admin)->patch(route('products.toggle-status', $product));

        $response->assertRedirect(route('products.index'));
        $this->assertFalse($product->fresh()->isActive());

        $this->actingAs($this->produksi)->patch(route('products.toggle-status', $product));
        $this->assertTrue($product->fresh()->isActive());
    }

    public function test_admin_and_produksi_can_delete_unused_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('products.destroy', $product));

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_product_with_relations_is_soft_deactivated_instead_of_hard_deleted(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        // Simulasikan riwayat relasi
        CateringOrderDetail::factory()->create([
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('products.destroy', $product));

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('warning');

        // Pastikan masih ada di database, tetapi is_active = false
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'is_active' => false,
        ]);
    }

    public function test_search_filter_works_correctly(): void
    {
        Product::factory()->create(['name' => 'Ayam Suwir Balado', 'description' => 'Ayam pedas']);
        Product::factory()->create(['name' => 'Smoothie Buah Naga', 'description' => 'Minuman segar']);

        $response = $this->actingAs($this->admin)->get(route('products.index', ['search' => 'Ayam']));
        $response->assertSee('Ayam Suwir Balado');
        $response->assertDontSee('Smoothie Buah Naga');
    }
}
