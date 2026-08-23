<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Facility>
 */
class FacilityFactory extends Factory
{
    protected $model = Facility::class;

    public function definition(): array
    {
        return [
            'name' => ucwords(fake()->words(2, true)),
            'description' => fake()->paragraph(),
            'sort_order' => fake()->numberBetween(0, 50),
            'photo_media_id' => null,
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }

    /**
     * State untuk membuat fasilitas yang langsung terhubung dengan foto media.
     */
    public function withPhoto(): static
    {
        return $this->state(fn (array $attributes) => [
            'photo_media_id' => Media::factory(),
        ]);
    }
}