<?php

declare(strict_types=1);

namespace App\Enums;

enum CartStatus: string
{
    case ACTIVE = 'active';
    case ABANDONED = 'abandoned';
    case CONVERTED = 'converted';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Ativo',
            self::ABANDONED => 'Abandonado',
            self::CONVERTED => 'Convertido em Encomenda',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'badge-primary',
            self::ABANDONED => 'badge-warning',
            self::CONVERTED => 'badge-success',
        };
    }
}
