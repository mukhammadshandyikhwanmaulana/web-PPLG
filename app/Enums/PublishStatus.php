<?php

namespace App\Enums;

enum PublishStatus: string
{
    case Draft = 'draft';
    case Published = 'published';

    /**
     * Mengembalikan teks label untuk tampilan Blade.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft     => 'Draft',
            self::Published => 'Diterbitkan',
        };
    }

    /**
     * Class warna Tailwind CSS untuk badge status publikasi.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft     => 'bg-slate-100 text-slate-600 border-slate-200',
            self::Published => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        };
    }

    /**
     * Mengembalikan array nilai enum.
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