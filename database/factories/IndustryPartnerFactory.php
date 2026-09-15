<?php

namespace Database\Factories;

use App\Enums\PublishStatus;
use App\Models\IndustryPartner;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class IndustryPartnerFactory extends Factory
{
    protected $model = IndustryPartner::class;

    public function definition(): array
    {
        return [
            'name'          => 'Contoh Mitra '.fake()->company(),
            'website_url'   => fake()->url(),
            'sort_order'    => fake()->numberBetween(0, 100),
            'status'        => PublishStatus::Published,
            'published_at'  => now(),
            'created_by'    => User::factory(),
            'updated_by'    => User::factory(),
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