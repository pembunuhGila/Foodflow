<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Ingredient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IngredientMinimumStockTest extends TestCase
{
    use RefreshDatabase;

    private User $gudang;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gudang = User::factory()->create(['role' => UserRole::Gudang]);
        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
    }

    /**
     * Test model helper methods for detecting stock below or equal to minimum stock (BR-06).
     */
    public function test_ingredient_model_helpers_detect_low_stock_and_out_of_stock(): void
    {
        $ingredient = Ingredient::factory()->create([
            'name' => 'Tepung Terigu Cakra',
            'minimum_stock' => 10.00,
            'current_stock' => 5.00,
        ]);

        // Helper using model's current_stock attribute
        $this->assertTrue($ingredient->isLowStock());
        $this->assertTrue($ingredient->isUnderMinimumStock());
        $this->assertTrue($ingredient->needsRestock());
        $this->assertFalse($ingredient->isOutOfStock());
        $this->assertSame('low_stock', $ingredient->getStockStatusLevel());
        $this->assertSame(5.00, $ingredient->getStockDeficit());

        // Boundary test: stock exactly equal to minimum_stock (BR-06 requirement)
        $ingredient->current_stock = 10.00;
        $this->assertTrue($ingredient->isLowStock());
        $this->assertTrue($ingredient->isUnderMinimumStock());
        $this->assertSame(0.00, $ingredient->getStockDeficit());

        // Stock above minimum_stock
        $ingredient->current_stock = 15.00;
        $this->assertFalse($ingredient->isLowStock());
        $this->assertFalse($ingredient->isUnderMinimumStock());
        $this->assertSame('sufficient', $ingredient->getStockStatusLevel());
        $this->assertSame(0.00, $ingredient->getStockDeficit());

        // Stock completely out (0.00)
        $ingredient->current_stock = 0.00;
        $this->assertTrue($ingredient->isLowStock());
        $this->assertTrue($ingredient->isOutOfStock());
        $this->assertSame('out_of_stock', $ingredient->getStockStatusLevel());
        $this->assertSame(10.00, $ingredient->getStockDeficit());

        // Helper accepts optional custom stock argument
        $this->assertTrue($ingredient->isLowStock(3.00));
        $this->assertTrue($ingredient->isLowStock(10.00));
        $this->assertFalse($ingredient->isLowStock(10.01));
        $this->assertFalse($ingredient->isLowStock(20.00));
    }

    /**
     * Test query scopes for filtering ingredients at or below minimum stock.
     */
    public function test_ingredient_query_scopes_filter_low_stock_and_out_of_stock(): void
    {
        $itemCritical = Ingredient::factory()->create([
            'name' => 'Daging Sapi Giling',
            'minimum_stock' => 20.00,
            'current_stock' => 12.00,
            'is_active' => true,
        ]);

        $itemBoundary = Ingredient::factory()->create([
            'name' => 'Minyak Goreng Sawit',
            'minimum_stock' => 15.00,
            'current_stock' => 15.00,
            'is_active' => true,
        ]);

        $itemSufficient = Ingredient::factory()->create([
            'name' => 'Garam Dapur Halus',
            'minimum_stock' => 5.00,
            'current_stock' => 25.00,
            'is_active' => true,
        ]);

        $itemOutOfStock = Ingredient::factory()->create([
            'name' => 'Beras Ketan Putih',
            'minimum_stock' => 10.00,
            'current_stock' => 0.00,
            'is_active' => true,
        ]);

        // Scope low stock / under minimum stock includes critical, boundary, and zero stock
        $lowStockIds = Ingredient::query()->lowStock()->pluck('id')->all();
        $this->assertContains($itemCritical->id, $lowStockIds);
        $this->assertContains($itemBoundary->id, $lowStockIds);
        $this->assertContains($itemOutOfStock->id, $lowStockIds);
        $this->assertNotContains($itemSufficient->id, $lowStockIds);

        // Alias scope underMinimumStock
        $underMinIds = Ingredient::query()->underMinimumStock()->pluck('id')->all();
        $this->assertSame($lowStockIds, $underMinIds);

        // Scope out of stock only includes 0 stock
        $outOfStockIds = Ingredient::query()->outOfStock()->pluck('id')->all();
        $this->assertContains($itemOutOfStock->id, $outOfStockIds);
        $this->assertNotContains($itemCritical->id, $outOfStockIds);
        $this->assertNotContains($itemBoundary->id, $outOfStockIds);
        $this->assertNotContains($itemSufficient->id, $outOfStockIds);

        // Scope sufficient stock
        $sufficientIds = Ingredient::query()->sufficientStock()->pluck('id')->all();
        $this->assertContains($itemSufficient->id, $sufficientIds);
        $this->assertNotContains($itemCritical->id, $sufficientIds);
        $this->assertNotContains($itemBoundary->id, $sufficientIds);
        $this->assertNotContains($itemOutOfStock->id, $sufficientIds);
    }

    /**
     * Test visual alert badges and banner on ingredients index page.
     */
    public function test_ingredients_index_displays_visual_alert_badges_and_banner(): void
    {
        $lowStockItem = Ingredient::factory()->create([
            'name' => 'Wortel Kritis Organik',
            'minimum_stock' => 20.00,
            'current_stock' => 8.00,
            'is_active' => true,
        ]);

        $outOfStockItem = Ingredient::factory()->create([
            'name' => 'Telur Habis Total',
            'minimum_stock' => 50.00,
            'current_stock' => 0.00,
            'is_active' => true,
        ]);

        $safeItem = Ingredient::factory()->create([
            'name' => 'Bayam Aman Subur',
            'minimum_stock' => 10.00,
            'current_stock' => 30.00,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->gudang)->get(route('ingredients.index'));

        $response->assertOk();
        // Check for alert banner
        $response->assertSee('Peringatan Batas Minimum Stok');
        $response->assertSee('2'); // 2 critical/low stock items

        // Check for visual badges
        $response->assertSee('Stok Menipis');
        $response->assertSee('Stok Habis');
        $response->assertSee('Stok Aman');

        // Check table row names
        $response->assertSee('Wortel Kritis Organik');
        $response->assertSee('Telur Habis Total');
        $response->assertSee('Bayam Aman Subur');
    }

    /**
     * Test filter by stock_status on ingredients index page.
     */
    public function test_ingredients_index_can_filter_by_stock_status(): void
    {
        $lowItem = Ingredient::factory()->create([
            'name' => 'Bahan Baku Kritis',
            'minimum_stock' => 10.00,
            'current_stock' => 3.00,
            'is_active' => true,
        ]);

        $safeItem = Ingredient::factory()->create([
            'name' => 'Bahan Baku Aman',
            'minimum_stock' => 10.00,
            'current_stock' => 50.00,
            'is_active' => true,
        ]);

        // Filter low_stock
        $response = $this->actingAs($this->gudang)->get(route('ingredients.index', ['stock_status' => 'low_stock']));
        $response->assertOk();
        $response->assertSee('Bahan Baku Kritis');
        $response->assertDontSee('Bahan Baku Aman');

        // Filter sufficient
        $response = $this->actingAs($this->gudang)->get(route('ingredients.index', ['stock_status' => 'sufficient']));
        $response->assertOk();
        $response->assertSee('Bahan Baku Aman');
        $response->assertDontSee('Bahan Baku Kritis');
    }

    /**
     * Test ingredient detail (show) page displays visual alert box and badges.
     */
    public function test_ingredients_show_displays_stock_alert_when_at_or_below_minimum(): void
    {
        $lowStockIngredient = Ingredient::factory()->create([
            'name' => 'Kacang Merah Basah',
            'minimum_stock' => 20.00,
            'current_stock' => 12.00,
            'unit' => 'kg',
            'is_active' => true,
        ]);

        $outOfStockIngredient = Ingredient::factory()->create([
            'name' => 'Susu Kedelai Cair',
            'minimum_stock' => 10.00,
            'current_stock' => 0.00,
            'unit' => 'liter',
            'is_active' => true,
        ]);

        $safeIngredient = Ingredient::factory()->create([
            'name' => 'Gula Merah Aren',
            'minimum_stock' => 5.00,
            'current_stock' => 30.00,
            'unit' => 'kg',
            'is_active' => true,
        ]);

        // 1. Low stock show page
        $responseLow = $this->actingAs($this->gudang)->get(route('ingredients.show', $lowStockIngredient));
        $responseLow->assertOk();
        $responseLow->assertSee('Peringatan Batas Minimum Stok (BR-06)');
        $responseLow->assertSee('Stok Menipis');
        $responseLow->assertSee('12.00 kg');
        $responseLow->assertSee('20.00 kg');

        // 2. Out of stock show page
        $responseOut = $this->actingAs($this->gudang)->get(route('ingredients.show', $outOfStockIngredient));
        $responseOut->assertOk();
        $responseOut->assertSee('Peringatan: Stok Bahan Baku Habis! (BR-06)');
        $responseOut->assertSee('Stok Habis');

        // 3. Safe stock show page
        $responseSafe = $this->actingAs($this->gudang)->get(route('ingredients.show', $safeIngredient));
        $responseSafe->assertOk();
        $responseSafe->assertDontSee('Peringatan Batas Minimum Stok (BR-06)');
        $responseSafe->assertDontSee('Peringatan: Stok Bahan Baku Habis! (BR-06)');
        $responseSafe->assertSee('Stok Aman');
    }

    /**
     * Test Dashboard Gudang displays warning widget summarizing critical stock items.
     */
    public function test_gudang_dashboard_displays_warning_widget_with_critical_stock_summary(): void
    {
        Ingredient::factory()->create([
            'name' => 'Beras Pulen Kritis',
            'minimum_stock' => 30.00,
            'current_stock' => 10.00,
            'unit' => 'kg',
            'is_active' => true,
        ]);

        Ingredient::factory()->create([
            'name' => 'Daging Ayam Kritis',
            'minimum_stock' => 25.00,
            'current_stock' => 0.00,
            'unit' => 'kg',
            'is_active' => true,
        ]);

        Ingredient::factory()->create([
            'name' => 'Minyak Goreng Aman',
            'minimum_stock' => 10.00,
            'current_stock' => 50.00,
            'unit' => 'liter',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->gudang)->get(route('gudang.dashboard'));

        $response->assertOk();
        $response->assertSee('Peringatan Batas Minimum Stok (BR-06)');
        $response->assertSee('2 Bahan Butuh Restock');
        $response->assertSee('Beras Pulen Kritis');
        $response->assertSee('Daging Ayam Kritis');
        $response->assertSee('Kelola Bahan Kritis');
    }

    /**
     * Test Dashboard Gudang displays controlled/safe status when all ingredients are sufficient.
     */
    public function test_gudang_dashboard_displays_safe_status_when_no_ingredients_low_stock(): void
    {
        Ingredient::factory()->create([
            'name' => 'Bahan Segar 1',
            'minimum_stock' => 10.00,
            'current_stock' => 40.00,
            'is_active' => true,
        ]);

        Ingredient::factory()->create([
            'name' => 'Bahan Segar 2',
            'minimum_stock' => 15.00,
            'current_stock' => 50.00,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->gudang)->get(route('gudang.dashboard'));

        $response->assertOk();
        $response->assertSee('Batas Minimum Stok Terkendali (BR-06)');
        $response->assertSee('Semua persediaan bahan baku aktif saat ini berada dalam kondisi aman');
    }
}
