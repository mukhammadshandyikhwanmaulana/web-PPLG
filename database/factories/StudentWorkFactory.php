<?php

namespace Database\Factories;

use App\Enums\PublishStatus;
use App\Models\StaffMember;
use App\Models\User;
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
            'demo_url' => fake()->boolean(70) ? fake()->url() : null,
            'is_featured' => false,
            'status' => PublishStatus::Published->value,
            'published_at' => now(),
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }

    /**
     * State untuk karya berstatus Draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PublishStatus::Draft->value,
            'published_at' => null,
        ]);
    }

    /**
     * State untuk karya unggulan (Featured).
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * State dengan guru pembimbing.
     */
    public function withSupervisor(?StaffMember $staff = null): static
    {
        return $this->state(fn (array $attributes) => [
            'supervisor_id' => $staff?->id ?? StaffMember::factory(),
        ]);
    }
}