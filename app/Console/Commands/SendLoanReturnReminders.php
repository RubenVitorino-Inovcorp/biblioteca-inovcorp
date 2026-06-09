<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\LoanStatus;
use App\Mail\LoanReturnReminderMail;
use App\Models\Loan;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

#[Signature('loans:send-reminders')]
#[Description('Envio de alertas por email para requisições que terminam amanhã')]
class SendLoanReturnReminders extends Command
{
    public function handle(): int
    {
        $loans = Loan::query()
            ->with(['user', 'book'])
            ->where('status', LoanStatus::ACTIVE->value)
            ->whereDate('estimated_return_date', now()->addDay()->toDateString())
            ->get();

        $count = 0;
        foreach ($loans as $index => $loan) {
            Mail::to($loan->user)->later(now()->addSeconds($index * 3), new LoanReturnReminderMail($loan));
            $count++;
        }

        $this->info("Foram enviados {$count} lembretes de devolução.");

        return Command::SUCCESS;
    }
}
