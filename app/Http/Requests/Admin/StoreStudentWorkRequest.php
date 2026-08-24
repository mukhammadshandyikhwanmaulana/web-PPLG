<?php

namespace App\Http\Requests\Admin;

use App\Enums\PublishStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentWorkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'contributor_name' => ['nullable', 'string', 'max:255'],
            'supervisor_id' => ['nullable', 'exists:staff_members,id'],
            'demo_url' => ['nullable', 'url', 'max:255', 'starts_with:http://,https://'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['required', Rule::enum(PublishStatus::class)],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul karya wajib diisi.',
            'status.required' => 'Status publikasi wajib dipilih.',
            'demo_url.url' => 'Format link demo/website tidak valid (harus diawali http:// atau https://).',
            'images.max' => 'Maksimal hanya boleh mengunggah 5 gambar.',
            'images.*.image' => 'File yang diunggah harus berupa gambar.',
            'images.*.mimes' => 'Format gambar harus JPEG, PNG, atau WebP.',
            'images.*.max' => 'Ukuran masing-masing gambar tidak boleh melebihi 2 MB.',
        ];
    }
}