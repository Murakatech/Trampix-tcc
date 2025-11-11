<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
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
            'display_name' => fake()->company(),
            'name' => fake()->company(),
            'cnpj' => fake()->numerify('##.###.###/####-##'),
            'sector' => fake()->randomElement(['Tecnologia', 'Saúde', 'Educação', 'Varejo', 'Serviços', 'Indústria']),
            'location' => fake()->city() . '/' . fake()->stateAbbr(),
            'description' => fake()->paragraph(),
            'website' => fake()->url(),
            'phone' => fake()->phoneNumber(),
            'employees_count' => fake()->numberBetween(1, 1000),
            'founded_year' => fake()->numberBetween(1980, 2023),
            'is_active' => true,
            'profile_photo' => null,
        ];
    }

    /**
     * Indicate that the company is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}