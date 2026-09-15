<?php

namespace Database\Factories;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MediaFactory extends Factory
{
    protected $model = Media::class;

    public function definition(): array
    {
        $fileName = 'unique-' . fake()->unique()->uuid() . '.jpg';

        return [
            'original_name' => 'example.jpg',
            'file_name'     => $fileName,
            'disk'          => 'public',
            'path'          => 'media/' . $fileName,
            'mime_type'     => 'image/jpeg',
            'size'          => fake()->numberBetween(10_000, 500_000),
            'alt_text'      => 'Example image',
            'created_by'    => User::factory(),
            'updated_by'    => User::factory(),
        ];
    }
}