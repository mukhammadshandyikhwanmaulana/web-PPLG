<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFacilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'        => $this->filled('name') ? trim((string) $this->name) : null,
            'description' => $this->filled('description') ? trim((string) $this->description) : null,
            'sort_order'  => $this->filled('sort_order') ? (int) $this->sort_order : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'photo'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'sort_order'  => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Nama fasilitas wajib diisi.',
            'name.string'        => 'Nama fasilitas harus berupa teks.',
            'name.max'           => 'Nama fasilitas tidak boleh lebih dari 255 karakter.',
            'photo.image'        => 'Berkas foto harus berupa gambar.',
            'photo.mimes'        => 'Format foto harus berupa jpg, jpeg, png, atau webp.',
            'photo.max'          => 'Ukuran foto tidak boleh lebih dari 10MB.',
            'sort_order.integer' => 'Urutan tampilan harus berupa angka.',
            'sort_order.min'     => 'Urutan tampilan minimal harus bernilai 1.',
        ];
    }
}