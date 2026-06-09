<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case RETURNED = 'returned';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendente de Pagamento',
            self::PAID => 'Pago',
            self::PROCESSING => 'Em Processamento',
            self::SHIPPED => 'Enviado',
            self::DELIVERED => 'Entregue',
            self::RETURNED => 'Devolvido',
            self::CANCELLED => 'Cancelado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'badge-warning',
            self::PAID => 'badge-success',
            self::PROCESSING => 'badge-info',
            self::SHIPPED => 'badge-primary',
            self::DELIVERED => 'badge-success',
            self::RETURNED => 'badge-secondary',
            self::CANCELLED => 'badge-error',
        };
    }
}
