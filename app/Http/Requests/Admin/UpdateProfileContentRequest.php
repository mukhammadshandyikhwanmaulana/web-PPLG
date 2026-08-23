<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'history_content' => ['nullable', 'string'],
            'vision_content' => ['nullable', 'string'],
            'mission_content' => ['nullable', 'string'],
            'about_excerpt' => ['nullable', 'string'],
        ];
    }
}