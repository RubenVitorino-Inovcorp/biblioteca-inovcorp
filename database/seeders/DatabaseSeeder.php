<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Importação de livros da Google Books API (+ criação de autores, editoras e tags)
        $this->call(GoogleBooksSeeder::class);

        $books = Book::all();

        if ($books->isEmpty()) {
            $this->command->error('Nenhum livro importado pelo GoogleBooksSeeder. A abortar.');

            return;
        }

        // 2. Criação de utilizadores (admin e normal)
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

        // Criação de mais utilizadores normais
        $users = User::factory(15)->create(['role' => UserRole::USER]);
        if ($normalUser = User::where('email', 'user@example.test')->first()) {
            $users->push($normalUser);
        }

        // Requisições (ativas, devolvidas e em atraso)
        $activeLoans = Loan::factory(15)
            ->recycle($users)
            ->recycle($books)
            ->create();

        $returnedLoans = Loan::factory(25)
            ->returned()
            ->recycle($users)
            ->recycle($books)
            ->create();

        $overdueLoans = Loan::factory(5)
            ->overdue()
            ->recycle($users)
            ->recycle($books)
            ->create();

        // Criação de reviews (apenas em requisições devolvidas)
        $reviewedLoans = $returnedLoans
            ->load('book')
            ->random(min(18, $returnedLoans->count()))
            ->unique(fn (Loan $loan) => $loan->user_id.'-'.$loan->book_id);

        foreach ($reviewedLoans as $loan) {
            /** @var Book $book */
            $book = $loan->book;

            if (! $book) {
                continue;
            }

            Review::factory()
                ->forBookAndLoan($book, $loan)
                ->create();
        }
    }
}
