<?php

namespace Database\Factories;

use App\Enums\PurchaseOrderStatus;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $orderDate = Carbon::instance(fake()->dateTimeBetween('-30 days', 'now'));
        $expectedDate = $orderDate->copy()->addDays(fake()->numberBetween(3, 14));

        return [
            'vendor_id' => Supplier::factory(),
            'created_by' => User::factory(),
            'order_number' => PurchaseOrder::generateOrderNumber($orderDate),
            'order_date' => $orderDate->format('Y-m-d'),
            'expected_date' => $expectedDate->format('Y-m-d'),
            'status' => PurchaseOrderStatus::Draft,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Status: Dipesan.
     */
    public function ordered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PurchaseOrderStatus::Ordered,
        ]);
    }

    /**
     * Status: Diterima.
     */
    public function received(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PurchaseOrderStatus::Received,
        ]);
    }

    /**
     * Status: Dibatalkan.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PurchaseOrderStatus::Cancelled,
        ]);
    }
}
