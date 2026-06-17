<?php

use App\Enums\LoanStatus;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

uses(RefreshDatabase::class, TestCase::class);

// Teste 1
test('Permitir que um utilizador requisitar um livro', function () {

    $user = User::factory()->create();
    $book = Book::factory()->create(['available_stock' => 1]);

    $response = $this->actingAs($user)->post(route('catalog.requisicoes.store'), [
        'book_id' => $book->id,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('loans', [
        'user_id' => $user->id,
        'book_id' => $book->id,
        'status' => LoanStatus::PENDING,
    ]);

    $this->assertDatabaseHas('books', [
        'id' => $book->id,
        'available_stock' => 0,
    ]);
});

// Teste 2
test('Não permitir criar uma requisição sem um livro válido', function () {

    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('catalog.requisicoes.store'), [
        'book_id' => 9999999,
    ]);

    $response->assertInvalid(['book_id']);
    $this->assertDatabaseCount('loans', 0);
});

// Teste 3
test('Permitir a um utilizador solicitar a devolucao de um livro', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create(['available_stock' => 1]);

    $loan = Loan::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'status' => LoanStatus::ACTIVE,
    ]);

    $response = $this->actingAs($user)->post(route('catalog.requisicoes.devolver', $loan->id));

    $response->assertRedirect(route('catalog.requisicoes.index'));
    $response->assertSessionHas('success', 'Devolução solicitada com sucesso!');

    $this->assertDatabaseHas('loans', [
        'id' => $loan->id,
        'status' => LoanStatus::RETURN_PENDING,
    ]);
});

// Teste 4
test('Permitir que o utilizador consiga ver as suas requisições', function () {

    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Loan::factory(3)
        ->recycle($user)
        ->create();

    Loan::factory(5)
        ->recycle($otherUser)
        ->create();

    $response = $this->actingAs($user)->get(route('catalog.requisicoes.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('User/Loans/Index')
        ->has('loans.data', 3));
});

// Teste 5
test('Não permitir que um utilizador requisitar um livro se não houver stock', function () {

    $user = User::factory()->create();
    $book = Book::factory()->create(['available_stock' => 0]);

    $response = $this->actingAs($user)->post(route('catalog.requisicoes.store'), [
        'book_id' => $book->id,
    ]);

    $response->assertSessionHasErrors('book_id');
    $this->assertDatabaseCount('loans', 0);
});
