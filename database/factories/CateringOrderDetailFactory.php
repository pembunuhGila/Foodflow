<?php

namespace Database\Factories;

use App\Models\CateringOrder;
use App\Models\CateringOrderDetail;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CateringOrderDetail>
 */
class CateringOrderDetailFactory extends Factory
{
    protected $model = CateringOrderDetail::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $unitPrice = $this->faker->randomFloat(2, 10000, 200000);
        $quantity = $this->faker->numberBetween(1, 20);

        return [
            'catering_order_id' => CateringOrder::factory(),
            'product_id' => Product::factory(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $unitPrice * $quantity,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
