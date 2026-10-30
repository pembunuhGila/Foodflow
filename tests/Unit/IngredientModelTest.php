<?php

namespace Tests\Unit;

use App\Models\Ingredient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IngredientModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_ingredient_can_be_instantiated_with_attributes(): void
    {
        $ingredient = new Ingredient([
            'name' => 'Beras Merah',
            'category' => 'Karbohidrat',
            'unit' => 'kg',
            'minimum_stock' => 20.50,
            'calories' => 350.00,
            'protein' => 7.50,
            'fat' => 2.50,
            'carbohydrates' => 75.00,
            'notes' => 'Beras organik',
            'is_active' => true,
        ]);

        $this->assertSame('Beras Merah', $ingredient->name);
        $this->assertSame('Karbohidrat', $ingredient->category);
        $this->assertSame('kg', $ingredient->unit);
        $this->assertTrue($ingredient->isActive());
    }

    public function test_is_low_stock_correctly_evaluates_threshold(): void
    {
        $ingredient = new Ingredient([
            'minimum_stock' => 15.00,
        ]);

        $this->assertTrue($ingredient->isLowStock(10.00));
        $this->assertTrue($ingredient->isLowStock(15.00));
        $this->assertFalse($ingredient->isLowStock(15.01));
        $this->assertFalse($ingredient->isLowStock(20.00));
    }
}
