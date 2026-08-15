<?php

namespace Database\Factories;

use App\Enums\PublishStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class IndustryPartnerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Contoh Mitra '.fake()->company(),
            'website_url' => fake()->url(),
            'sort_order' => fake()->numberBetween(0, 100),
            'status' => PublishStatus::Published->value,
            'published_at' => now(),
        ];
    }
}