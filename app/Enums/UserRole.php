<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case USER = 'user';

    public function label(): string
    {
        return match ($this) {
            UserRole::ADMIN => 'Administrador',
            UserRole::USER => 'Usuário',
        };
    }
}
