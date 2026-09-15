<?php

namespace Database\Factories;

use App\Enums\AchievementLevel;
use App\Enums\PublishStatus;
use App\Models\Achievement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Achievement>
 */
class AchievementFactory extends Factory
{
    protected $model = Achievement::class;

    public function definition(): array
    {
        $title = 'Contoh Prestasi '.fake()->words(3, true);

        return [
            'title'            => $title,
            'slug'             => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'achievement_date' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'level'            => fake()->randomElement(AchievementLevel::cases()),
            'contributor_name' => fake()->name(),
            'description'      => fake()->paragraph(),
            'document_media_id' => null,
            'status'           => PublishStatus::Published,
            'published_at'     => now(),
            'created_by'       => User::factory(),
            'updated_by'       => User::factory(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'       => PublishStatus::Draft,
            'published_at' => null,
        ]);
    }
}