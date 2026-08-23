<?php

namespace Database\Factories;

use App\Enums\PublishStatus;
use App\Models\Media;
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
            'event_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'content' => fake()->paragraphs(3, true),
            'cover_media_id' => null,
            'status' => PublishStatus::Published->value,
            'published_at' => now(),
        ];
    }

    /**
     * State untuk kegiatan berstatus Draft
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PublishStatus::Draft->value,
            'published_at' => null,
        ]);
    }

    /**
     * State untuk kegiatan berstatus Published
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PublishStatus::Published->value,
            'published_at' => now(),
        ]);
    }

    /**
     * State otomatis membuatkan Media Cover
     */
    public function withCover(): static
    {
        return $this->state(fn (array $attributes) => [
            'cover_media_id' => Media::factory(),
        ]);
    }
}