<?php

namespace Database\Factories;

use App\Models\MaterialRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<MaterialRequest>
 */
class MaterialRequestFactory extends Factory
{
    protected $model = MaterialRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'production_plan_id' => null,
            'requested_by' => User::factory(),
            'verified_by' => null,
            'request_date' => Carbon::today()->format('Y-m-d'),
            'status' => 'pending',
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * State: Approved.
     */
    public function approved(?User $verifier = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'verified_by' => $verifier?->id ?? User::factory(),
        ]);
    }
}
