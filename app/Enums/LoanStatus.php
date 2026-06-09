<?php

declare(strict_types=1);

namespace App\Enums;

enum LoanStatus: string
{
    case PENDING = 'pending';
    case REJECTED = 'rejected';
    case ACTIVE = 'active';
    case RETURN_PENDING = 'return_pending';
    case RETURNED = 'returned';
    case OVERDUE = 'overdue';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendente de Aprovação',
            self::REJECTED => 'Rejeitado',
            self::ACTIVE => 'Em Uso',
            self::RETURN_PENDING => 'Devolução Pendente',
            self::RETURNED => 'Devolvido',
            self::OVERDUE => 'Em Atraso',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'badge-info',
            self::REJECTED => 'badge-error',
            self::ACTIVE => 'badge-secondary',
            self::RETURNED => 'badge-primary',
            self::RETURN_PENDING => 'badge-accent animate-pulse',
            self::OVERDUE => 'badge-warning',
        };
    }
}
