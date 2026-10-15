<?php

namespace Database\Factories;

use App\Models\CateringOrder;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CateringOrder>
 */
class CateringOrderFactory extends Factory
{
    protected $model = CateringOrder::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'order_number' => CateringOrder::generateOrderNumber(),
            'customer_id' => Customer::factory(),
            'created_by' => User::factory(),
            'order_date' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'delivery_date' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'processing', 'delivered', 'cancelled']),
            'total_amount' => $this->faker->randomFloat(2, 50000, 500000),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
