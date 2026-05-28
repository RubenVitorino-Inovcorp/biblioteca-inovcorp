<?php

namespace App\Http\Controllers\User;

use App\Enums\LoanStatus;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class BookAlertController extends Controller
{
    public function store(Book $book): RedirectResponse
    {
        // Early return se o livro tiver stock
        if ($book->available_stock > 0) {
            return redirect()->back()->with('error', 'O livro já se encontra disponível para requisição.');
        }

        // Verifica se o utilizador já tem uma requisição pendente/ativa para este livro
        $hasActiveLoan = Loan::where('user_id', auth()->id())
            ->where('book_id', $book->id)
            ->whereIn('status', [
                LoanStatus::PENDING,
                LoanStatus::ACTIVE,
                LoanStatus::RETURN_PENDING,
                LoanStatus::OVERDUE,
            ])
            ->exists();

        if ($hasActiveLoan) {
            return redirect()->back()->with('error', 'Não pode criar um alerta para um livro que já requisitou.');
        }

        $exists = DB::table('book_alerts')
            ->where('user_id', auth()->id())
            ->where('book_id', $book->id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('info', 'Já tem um alerta ativo para este livro.');
        }

        DB::table('book_alerts')->insert([
            'user_id' => auth()->id(),
            'book_id' => $book->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Será notificado por e-mail assim que o livro for devolvido.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        DB::table('book_alerts')
            ->where('user_id', auth()->id())
            ->where('book_id', $book->id)
            ->delete();

        return redirect()->back()->with('success', 'Deixou de receber notificações para este livro.');
    }
}
