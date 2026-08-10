<?php

namespace App\Enums;

enum UserRole: string
{
    case Guru = 'guru';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Guru => 'Guru',
            self::Admin => 'Admin',
        };
    }
}