<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Artist>
 */
class ArtistFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $birthDate = fake()->dateTimeBetween('-100 years', '-20 years');

        return [
            'name' => fake()->name(),
            'bio' => fake()->optional(0.7)->paragraph(),
            'birth_date' => fake()->optional(0.8)->dateTimeBetween('-100 years', '-20 years'),
            'death_date' => fake()->optional(0.3)->dateTimeBetween($birthDate, 'now'),
        ];
    }

    /**
     * Artist with no death date (still alive).
     */
    public function alive(): static
    {
        return $this->state(fn (array $attributes) => [
            'death_date' => null,
        ]);
    }

    /**
     * Artist with minimal data (only name).
     */
    public function minimal(): static
    {
        return $this->state(fn (array $attributes) => [
            'bio' => null,
            'birth_date' => null,
            'death_date' => null,
        ]);
    }
}
