<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateGuruRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'staff_is_active' => $this->boolean('staff_is_active'),
        ]);
    }

    public function rules(): array
    {
        $guruId = $this->route('guru')?->id ?? $this->route('guru');

        return [
            // Akun
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($guruId),
            ],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'is_active' => ['boolean'],

            // Profil Publik
            'position' => ['nullable', 'string', 'max:255'],
            'expertise' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'staff_is_active' => ['boolean'],
        ];
    }
}