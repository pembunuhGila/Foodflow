<?php

namespace Database\Factories;

use App\Models\Production;
use App\Models\ProductionPlanDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<Production>
 */
class ProductionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = Production::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'production_number' => Production::generateProductionNumber(),
            'production_plan_detail_id' => ProductionPlanDetail::factory(),
            'created_by' => User::factory(),
            'production_date' => now()->toDateString(),
            'actual_quantity' => fake()->numberBetween(10, 50),
            'status' => 'completed',
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * State untuk realisasi berstatus parsial.
     */
    public function partial(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'partial',
        ]);
    }

    /**
     * State untuk realisasi berstatus selesai.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }
}
