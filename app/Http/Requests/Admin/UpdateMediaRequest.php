<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
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