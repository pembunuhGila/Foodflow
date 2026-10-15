<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
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
                'SD Negeri Harapan Bangsa',
                'SMP Islam Terpadu Al-Falah',
                'SMA Unggulan Nusantara',
                'PT Teknologi Maju Sejahtera',
                'Klinik Pratama Sehat Prima',
                'Puskesmas Kebon Jeruk',
                'Kantor Wilayah Pajak Pratama',
                'Komunitas Senam Sehat Lansia',
            ]).' '.fake()->unique()->randomNumber(3),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'address' => fake()->address(),
            'notes' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the customer is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
