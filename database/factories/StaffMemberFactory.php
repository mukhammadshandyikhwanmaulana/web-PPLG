<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StaffMemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'position' => fake()->randomElement(['Guru Produktif', 'Guru Normatif', 'Kepala Kompetensi Keahlian']),
            'expertise' => fake()->randomElement(['Pemrograman Web', 'Pemrograman Mobile', 'Basis Data', 'Jaringan']),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}