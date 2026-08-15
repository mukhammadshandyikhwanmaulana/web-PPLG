<?php

namespace Database\Factories;

use App\Enums\PublishStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StudentWorkFactory extends Factory
{
    public function definition(): array
    {
        $title = 'Contoh Karya '.fake()->words(3, true);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'description' => fake()->paragraph(),
            'contributor_name' => fake()->name(),
            'is_featured' => false,
            'status' => PublishStatus::Published->value,
            'published_at' => now(),
        ];
    }
}