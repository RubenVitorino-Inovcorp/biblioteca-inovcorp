<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Author;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $authors = Author::factory(50)->create();
        $publishers = Publisher::factory(25)->create();

        $books = Book::factory(120)
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
            'role' => UserRole::ADMIN,
        ]);

        User::create([
            'name' => 'Usuário Normal',
            'email' => 'user@example.test',
            'password' => bcrypt('user12345'),
            'role' => UserRole::USER,
        ]);

        $users = User::factory(15)->create(['role' => UserRole::USER]);
        if ($normalUser = User::where('email', 'user@example.test')->first()) {
            $users->push($normalUser);
        }


        // Requisições ativas
        Loan::factory(15)
            ->recycle($users)
            ->recycle($books)
            ->create();

        // Requisições devolvidas
        Loan::factory(20)
            ->returned()
            ->recycle($users)
            ->recycle($books)
            ->create();

        // Requisições em atraso
        Loan::factory(5)
            ->overdue()
            ->recycle($users)
            ->recycle($books)
            ->create();
    }
}
