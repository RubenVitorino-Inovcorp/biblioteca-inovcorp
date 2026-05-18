<?php

namespace Database\Factories;

use App\Enums\LoanStatus;
use App\Models\Author;
use App\Models\Book;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Book>
 */
class LoanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 month', 'now');

        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'user_photo_snapshot' => 'https://api.dicebear.com/9.x/avataaars-neutral/svg?backgroundType=gradientLinear&backgroundColor=006c49,014730&seed=' . $this->faker->uuid(),
            'start_date' => $startDate,
            'estimated_return_date' => Carbon::parse($startDate)->addDays(5),
            'end_date' => null,
            'status' => LoanStatus::ACTIVE
        ];
    }

    /**
     * Estado de requisição já entregue.
     */
    public function returned(): static
    {
        return $this->state(function (array $attributes) {
            $endDate = Carbon::parse($attributes['start_date'])->addDays(rand(1, 10));

            return [
                'status' => LoanStatus::RETURNED,
                'end_date' => $endDate,
            ];
        });
    }

    /**
     * Estado de requisição em atraso.
     */
    public function overdue(): static
    {
        return $this->state(function (array $attributes) {

            $startDate = Carbon::now()->subDays(rand(6, 15));

            return [
                'start_date' => $startDate,
                'estimated_return_date' => $startDate->copy()->addDays(5),
                'status' => LoanStatus::OVERDUE,
                'end_date' => null,
            ];
        });
    }
}
