<?php

namespace App\Http\Requests\Admin;

use App\Enums\PublishStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'content' => ['nullable', 'string'],
            'cover' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
            'status' => ['required', Rule::enum(PublishStatus::class)],
            'images' => ['nullable', 'array', 'max:8'],
            'images.*' => ['image', 'mimes:jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul kegiatan wajib diisi.',
            'event_date.required' => 'Tanggal kegiatan wajib diisi.',
            'event_date.date' => 'Format tanggal tidak valid.',
            'status.required' => 'Status publikasi wajib dipilih.',
            'cover.image' => 'Cover harus berupa gambar.',
            'cover.mimes' => 'Format cover harus JPEG, PNG, atau WebP.',
            'cover.max' => 'Ukuran cover tidak boleh melebihi 2 MB.',
            'images.max' => 'Maksimal hanya boleh mengunggah 8 gambar galeri.',
            'images.*.image' => 'File yang diunggah harus berupa gambar.',
            'images.*.mimes' => 'Format gambar galeri harus JPEG, PNG, atau WebP.',
            'images.*.max' => 'Ukuran masing-masing gambar galeri tidak boleh melebihi 2 MB.',
        ];
    }
}