<?php

namespace Database\Factories;

use App\Enums\AchievementLevel;
use App\Enums\PublishStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AchievementFactory extends Factory
{
    public function definition(): array
    {
        $title = 'Contoh Prestasi '.fake()->words(3, true);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'achievement_date' => fake()->dateTimeBetween('-2 years', 'now'),
            'level' => fake()->randomElement(AchievementLevel::cases())->value,
            'contributor_name' => fake()->name(),
            'description' => fake()->paragraph(),
            'status' => PublishStatus::Published->value,
            'published_at' => now(),
        ];
    }
}