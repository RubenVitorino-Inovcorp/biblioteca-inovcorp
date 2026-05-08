<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $authors = Author::factory(10)->create();
        $publishers = Publisher::factory(5)->create();

        Book::factory(30)
            ->recycle($publishers)
            ->create()
            ->each(function ($book) use ($authors) {
            $book->authors()->attach(
                $authors->random(rand(1, 3))->pluck('id')
            );
        });


        User::create([
            'name' => 'Admin',
            'email' => 'admin@biblioteca-inovcorp.test',
            'password' => bcrypt('admin12345'),
        ]);
    }
}
