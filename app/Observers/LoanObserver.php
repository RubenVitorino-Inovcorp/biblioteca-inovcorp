<?php

namespace App\Observers;

use App\Models\Loan;
use App\Services\ActivityLogger;

class LoanObserver
{
    /**
     * Handle the Loan "created" event.
     */
    public function created(Loan $loan): void
    {
        ActivityLogger::log(
            module: 'loans',
            objectId: (string) $loan->id,
            action: "Criação de requisição para o livro: #{$loan->book_id} - ".($loan->book ? $loan->book->title : 'Desconhecido'),
        );
    }

    /**
     * Handle the Loan "updated" event.
     */
    public function updated(Loan $loan): void
    {
        if ($loan->wasChanged('status')) {
            ActivityLogger::log(
                module: 'loans',
                objectId: (string) $loan->id,
                action: "Atualização do estado da requisição #{$loan->loan_number} para {$loan->status_label}",
            );
        }
    }

    /**
     * Handle the Loan "deleted" event.
     */
    public function deleted(Loan $loan): void
    {
        //
    }

    /**
     * Handle the Loan "restored" event.
     */
    public function restored(Loan $loan): void
    {
        //
    }

    /**
     * Handle the Loan "force deleted" event.
     */
    public function forceDeleted(Loan $loan): void
    {
        //
    }
}
