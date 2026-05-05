<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
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

        Book::factory(30)
            ->hasAttached($authors->random(rand(0,3)))->create();


        User::create([
            'name' => 'Admin',
            'email' => 'admin@biblioteca-inovcorp.test',
            'password' => bcrypt('admin12345'),
        ]);
    }
}
