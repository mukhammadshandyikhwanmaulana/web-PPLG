<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! auth()->check()) return false;
        $user = auth()->user();

        $adminRole = UserRole::Admin->value;

        if (method_exists($user, 'hasRole')) {
            return $user->hasRole($adminRole);
        }

        $userRole = strtolower($user->role instanceof UserRole ? $user->role->value : ($user->role ?? ''));
        return $userRole === $adminRole;
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

    public function messages(): array
    {
        return [
            'about_excerpt.max' => 'Ringkasan singkat profil tidak boleh lebih dari 1000 karakter.',
        ];
    }
}