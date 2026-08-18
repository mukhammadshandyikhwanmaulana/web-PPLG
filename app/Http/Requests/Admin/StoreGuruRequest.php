<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreGuruRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Defense-in-depth — middleware route:admin sudah menjadi lapisan utama.
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            // Akun
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'is_active' => ['sometimes', 'boolean'],

            // Profil (F-008) — seluruhnya nullable agar tidak memaksa
            // pengisian sekaligus tidak merusak test/flow F-005 lama.
            'position' => ['nullable', 'string', 'max:255'],
            'expertise' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'staff_is_active' => ['sometimes', 'boolean'],
        ];
    }
}