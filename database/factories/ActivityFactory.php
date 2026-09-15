<?php

namespace Database\Factories;

use App\Enums\PublishStatus;
use App\Models\Activity;
use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        $title = 'Contoh Kegiatan '.fake()->words(3, true);

        return [
            'title'          => $title,
            'slug'           => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'event_date'     => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'content'        => fake()->paragraphs(3, true),
            'cover_media_id' => null,
            'status'         => PublishStatus::Published,
            'published_at'   => now(),
            'created_by'     => User::factory(),
            'updated_by'     => User::factory(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'       => PublishStatus::Draft,
            'published_at' => null,
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'       => PublishStatus::Published,
            'published_at' => now(),
        ]);
    }

    public function withCover(): static
    {
        return $this->state(fn (array $attributes) => [
            'cover_media_id' => Media::factory(),
        ]);
    }
}