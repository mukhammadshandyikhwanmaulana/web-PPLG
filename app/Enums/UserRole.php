<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Guru = 'guru';

    /**
     * Mengembalikan teks label peran pengguna.
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Guru  => 'Tenaga Pendidik / Guru',
        };
    }

    /**
     * Class warna Tailwind CSS untuk badge peran pengguna.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Admin => 'bg-purple-50 text-purple-700 border-purple-200',
            self::Guru  => 'bg-blue-50 text-blue-700 border-blue-200',
        };
    }

    /**
     * Mengembalikan array nilai role.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Mengembalikan array key-value untuk elemen <select> di Blade.
     */
    public static function options(): array
    {
        return array_reduce(self::cases(), function ($carry, $item) {
            $carry[$item->value] = $item->label();
            return $carry;
        }, []);
    }
}