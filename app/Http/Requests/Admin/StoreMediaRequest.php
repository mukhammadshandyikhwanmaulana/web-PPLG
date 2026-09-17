<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! auth()->check()) return false;
        $user = auth()->user();

        $allowedRoles = [UserRole::Admin->value, UserRole::Guru->value];

        if (method_exists($user, 'hasAnyRole')) {
            return $user->hasAnyRole($allowedRoles);
        }

        $userRole = strtolower($user->role instanceof UserRole ? $user->role->value : ($user->role ?? ''));
        return in_array($userRole, $allowedRoles, true);
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
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp,gif,pdf',
                'max:10240',
            ],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File wajib diunggah.',
            'file.file'     => 'Upload harus berupa berkas yang valid.',
            'file.mimes'    => 'Format berkas harus jpg, jpeg, png, webp, gif, atau pdf.',
            'file.max'      => 'Ukuran file maksimal 10 MB.',
            'alt_text.max'  => 'Deskripsi media maksimal 255 karakter.',
        ];
    }
}