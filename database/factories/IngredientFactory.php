<?php

namespace Database\Factories;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ingredient>
 */
class IngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Beras Merah Organik',
                'Dada Ayam Fillet',
                'Daging Sapi Has Luar',
                'Telur Ayam Kampung',
                'Wortel Segar',
                'Bayam Organik',
                'Tempe Kedelai',
                'Tahu Putih Sutra',
                'Minyak Zaitun',
                'Bawang Putih Tunggal',
            ]).' '.fake()->unique()->randomNumber(4),
            'category' => fake()->randomElement(['Karbohidrat', 'Protein Hewani', 'Protein Nabati', 'Sayuran', 'Bumbu & Minyak']),
            'unit' => fake()->randomElement(['kg', 'gram', 'liter', 'ml', 'pcs', 'butir', 'ikat']),
            'minimum_stock' => fake()->randomFloat(2, 5, 50),
            'calories' => fake()->randomFloat(2, 50, 400),
            'protein' => fake()->randomFloat(2, 1, 30),
            'fat' => fake()->randomFloat(2, 0.5, 20),
            'carbohydrates' => fake()->randomFloat(2, 0, 60),
            'notes' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the ingredient is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
