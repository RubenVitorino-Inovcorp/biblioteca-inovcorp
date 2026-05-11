<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Author>
 */
class AuthorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'photo_path' => 'https://api.dicebear.com/9.x/thumbs/svg?backgroundColor=b4f0dc&seed=' . $this->faker->uuid(),        ];
    }
}
