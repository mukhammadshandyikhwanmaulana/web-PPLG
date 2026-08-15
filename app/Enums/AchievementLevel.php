<?php

namespace App\Enums;

enum AchievementLevel: string
{
    case Sekolah = 'sekolah';
    case Kabupaten = 'kabupaten';
    case Provinsi = 'provinsi';
    case Nasional = 'nasional';
    case Internasional = 'internasional';

    public function label(): string
    {
        return match ($this) {
            self::Sekolah => 'Sekolah',
            self::Kabupaten => 'Kabupaten',
            self::Provinsi => 'Provinsi',
            self::Nasional => 'Nasional',
            self::Internasional => 'Internasional',
        };
    }
}