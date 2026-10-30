<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
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
                'Nasi Merah Dada Ayam Bakar Madu',
                'Salad Sayur Quinoa Saus Wijen',
                'Sup Ayam Kampung Bening Herbal',
                'Pepes Ikan Nila Daun Kemangi',
                'Oatmeal Pisang Panggang Kayu Manis',
                'Tumis Brokoli Tahu Kukus Bawang Putih',
                'Nasi Jagung Gurame Fillet Asam Manis',
                'Smoothie Bayam Pisang Chia Seed',
            ]).' '.fake()->unique()->randomNumber(3),
            'description' => fake()->sentence(8),
            'unit' => 'porsi',
            'selling_price' => fake()->randomElement([25000, 30000, 35000, 40000, 45000]),
            'is_active' => true,
            'calories' => fake()->numberBetween(250, 650),
            'protein' => fake()->randomFloat(1, 15, 45),
            'fat' => fake()->randomFloat(1, 5, 20),
            'carbohydrates' => fake()->randomFloat(1, 30, 80),
            'dietary_fiber' => fake()->randomFloat(1, 3, 12),
        ];
    }

    /**
     * Indicate that the product is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
