<?php

namespace Database\Factories;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        return [
            'site_name'        => 'SMK Negeri 1 Bangsri',
            'site_tagline'     => 'Kompetensi Keahlian PPLG',
            'site_description' => fake()->paragraph(),
            'contact_email'    => fake()->safeEmail(),
            'contact_phone'    => '0812' . fake()->numerify('########'),
            'contact_address'  => fake()->address(),
            'whatsapp_url'     => 'https://wa.me/6281234567890',
            'instagram_url'    => 'https://instagram.com/smkn1bangsri',
            'facebook_url'     => 'https://facebook.com/smkn1bangsri',
            'youtube_url'      => 'https://youtube.com/@smkn1bangsri',
        ];
    }
}