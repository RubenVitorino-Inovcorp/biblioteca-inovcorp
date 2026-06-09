<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isbn = $this->faker->unique()->isbn13();

        return [
            'isbn' => $isbn,
            'title' => $this->faker->sentence(3),
            'bibliography' => $this->faker->paragraph(),
            'image_path' => "https://picsum.photos/seed/{$isbn}/600/600",
            'price' => $this->faker->randomfloat(2, 5, 50),
            'publisher_id' => Publisher::factory(),
        ];
    }
}
