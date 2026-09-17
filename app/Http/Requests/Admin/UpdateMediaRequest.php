<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMediaRequest extends FormRequest
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
            'alt_text' => $this->filled('alt_text') ? trim((string) $this->alt_text) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'file' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,gif,svg,pdf',
                'max:10240',
            ],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.file'    => 'Upload harus berupa berkas yang valid.',
            'file.mimes'   => 'Format berkas harus jpg, jpeg, png, webp, gif, svg, atau pdf.',
            'file.max'     => 'Ukuran file maksimal 10 MB.',
            'alt_text.max' => 'Deskripsi media maksimal 255 karakter.',
        ];
    }
}