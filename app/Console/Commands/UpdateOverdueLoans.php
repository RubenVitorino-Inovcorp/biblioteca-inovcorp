<?php
declare(strict_types=1);
namespace App\Console\Commands;

use App\Enums\LoanStatus;
use App\Models\Loan;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('loans:update-overdue')]
#[Description('Verifica e atualiza automaticamente as requisições ativas que ultrapassaram a data estimada de devolução')]


class UpdateOverdueLoans extends Command
{
    public function handle(): int
    {
        $updatedRows = Loan::query()
            ->where('status', LoanStatus::ACTIVE->value)
            ->where('estimated_return_date', '<', now()->startOfDay())
            ->update([
                'status' => LoanStatus::OVERDUE->value,
            ]);

        $this->info("Processamento concluído. {$updatedRows} requisições foram marcadas 'Em Atraso'.");

        return Command::SUCCESS;
    }
}
