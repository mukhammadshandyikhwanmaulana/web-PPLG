<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'history_content' => $this->filled('history_content') ? trim((string) $this->history_content) : null,
            'vision_content'  => $this->filled('vision_content') ? trim((string) $this->vision_content) : null,
            'mission_content' => $this->filled('mission_content') ? trim((string) $this->mission_content) : null,
            'about_excerpt'   => $this->filled('about_excerpt') ? trim((string) $this->about_excerpt) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'history_content' => ['nullable', 'string'],
            'vision_content'  => ['nullable', 'string'],
            'mission_content' => ['nullable', 'string'],
            'about_excerpt'   => ['nullable', 'string', 'max:1000'],
        ];
    }
}