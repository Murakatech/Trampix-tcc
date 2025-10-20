<?php

namespace Database\Factories;

use App\Models\Freelancer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Freelancer>
 */
class FreelancerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'bio' => fake()->paragraph(),
            'portfolio_url' => fake()->url(),
            'cv_url' => null,
            'phone' => fake()->phoneNumber(),
            'location' => fake()->city() . '/' . fake()->stateAbbr(),
            'hourly_rate' => fake()->randomFloat(2, 25, 150),
            'availability' => fake()->randomElement(['full_time', 'part_time', 'freelance']),
            'is_active' => true,
            'profile_photo' => null,
        ];
    }

    /**
     * Indicate that the freelancer is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}