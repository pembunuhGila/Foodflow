<?php

namespace Database\Factories;

use App\Models\Ingredient;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaterialRequestDetail>
 */
class MaterialRequestDetailFactory extends Factory
{
    protected $model = MaterialRequestDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'material_request_id' => MaterialRequest::factory(),
            'ingredient_id' => Ingredient::factory(),
            'requested_quantity' => 10.000,
            'approved_quantity' => 0.000,
            'issued_quantity' => 0.000,
            'unit' => 'kg',
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
