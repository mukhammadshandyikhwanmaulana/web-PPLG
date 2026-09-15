<?php

namespace App\Enums;

enum AchievementLevel: string
{
    case Kota = 'kota';
    case Provinsi = 'provinsi';
    case Nasional = 'nasional';
    case Internasional = 'internasional';

    /**
     * Mengembalikan teks label untuk tampilan Blade.
     */
    public function label(): string
    {
        return match ($this) {
            self::Kota          => 'Kabupaten / Kota',
            self::Provinsi      => 'Provinsi',
            self::Nasional      => 'Nasional',
            self::Internasional => 'Internasional',
        };
    }

    /**
     * Class warna Tailwind CSS untuk badge tingkat prestasi.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Kota          => 'bg-slate-100 text-slate-700 border-slate-200',
            self::Provinsi      => 'bg-sky-50 text-sky-700 border-sky-200',
            self::Nasional      => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            self::Internasional => 'bg-amber-50 text-amber-700 border-amber-200',
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