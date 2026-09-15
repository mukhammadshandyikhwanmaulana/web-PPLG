<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'site_name',
        'site_tagline',
        'site_description',
        'contact_email',
        'contact_phone',
        'contact_address',
        'tiktok_url',
        'instagram_url',
        'youtube_url',
    ];

    /**
     * Memastikan hanya ada 1 baris pengaturan yang dipanggil (dengan caching aman).
     */
    public static function current(): self
    {
        $data = Cache::rememberForever('site_settings', function () {
            $setting = static::first() ?? static::create([
                'site_name' => 'PPLG System',
                'site_tagline' => 'Pengembangan Perangkat Lunak dan Gim',
                'site_description' => 'Website Resmi Kompetensi Keahlian PPLG',
            ]);

            return $setting->toArray();
        });

        if (! is_array($data)) {
            Cache::forget('site_settings');
            return static::current();
        }

        return (new static())->newFromBuilder($data);
    }

    /**
     * Otomatis bersihkan cache pengaturan saat ada perubahan data.
     */
    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('site_settings');
        });

        static::deleted(function () {
            Cache::forget('site_settings');
        });
    }
}