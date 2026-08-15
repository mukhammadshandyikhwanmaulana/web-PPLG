<?php

namespace Database\Factories;

use App\Enums\PublishStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ActivityFactory extends Factory
{
    public function definition(): array
    {
        $title = 'Contoh Kegiatan '.fake()->words(3, true);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'event_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'content' => fake()->paragraphs(3, true),
            'status' => PublishStatus::Published->value,
            'published_at' => now(),
        ];
    }
}