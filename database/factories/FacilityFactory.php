<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FacilityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Contoh Fasilitas '.fake()->words(2, true),
            'description' => fake()->paragraph(),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}