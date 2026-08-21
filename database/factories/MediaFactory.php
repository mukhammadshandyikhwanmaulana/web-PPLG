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
        return [
            'file_name'   => $this->faker->word() . '.jpg',
            'file_path'   => 'uploads/' . $this->faker->uuid() . '.jpg',
            'mime_type'   => 'image/jpeg',
            'size'        => 1024,
            'uploaded_by' => User::factory(),
        ];
    }
}