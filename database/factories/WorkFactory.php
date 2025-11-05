<?php

namespace Database\Factories;

use App\Models\Artist;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Work>
 */
class WorkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->optional(0.7)->paragraph(),
            'year_created' => fake()->optional(0.8)->numberBetween(1500, 2024),
            'artist_id' => Artist::factory(),
        ];
    }

    /**
     * Work with minimal data (only required fields).
     */
    public function minimal(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => null,
            'year_created' => null,
        ]);
    }

    /**
     * Work for a specific artist.
     */
    public function forArtist(Artist|int $artist): static
    {
        $artistId = $artist instanceof Artist ? $artist->id : $artist;

        return $this->state(fn (array $attributes) => [
            'artist_id' => $artistId,
        ]);
    }
}
