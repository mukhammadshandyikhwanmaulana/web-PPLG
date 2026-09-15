<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUnitUsahaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'label'        => $this->filled('label') ? trim((string) $this->label) : null,
            'external_url' => $this->filled('external_url') ? trim((string) $this->external_url) : null,
            'is_active'    => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        return [
            'label'        => ['nullable', 'string', 'max:255'],
            'external_url' => ['nullable', 'url', 'max:255'],
            'is_active'    => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'label.max'          => 'Label maksimal 255 karakter.',
            'external_url.url'   => 'Format URL eksternal tidak valid.',
            'is_active.required' => 'Status aktif wajib diisi.',
        ];
    }
}