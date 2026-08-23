<?php

namespace Database\Factories;

use App\Models\StaffMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StaffMemberFactory extends Factory
{
    protected $model = StaffMember::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'position' => fake()->randomElement(['Guru Produktif', 'Guru Normatif', 'Kepala Kompetensi Keahlian']),
            'expertise' => fake()->randomElement(['Pemrograman Web', 'Pemrograman Mobile', 'Basis Data', 'Jaringan']),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}