<?php

namespace Database\Factories;

use App\Models\Faq;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Faq>
 */
class FaqFactory extends Factory
{
    protected $model = Faq::class;

    public function definition(): array
    {
        return [
            'question'   => rtrim(fake()->sentence(8), '.') . '?',
            'answer'     => fake()->paragraph(3),
            'sort_order' => fake()->numberBetween(0, 100),
            'is_active'  => true,
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}