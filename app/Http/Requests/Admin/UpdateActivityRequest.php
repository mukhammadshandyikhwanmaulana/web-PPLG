<?php

namespace App\Http\Requests\Admin;

use App\Enums\PublishStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateActivityRequest extends FormRequest
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
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,webp', 'max:2048'],
            'remove_gallery_ids' => ['nullable', 'array'],
            'remove_gallery_ids.*' => ['integer', 'exists:galleries,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $activity = $this->route('activity');

            if (! $activity) {
                return;
            }

            $removeIds = $this->input('remove_gallery_ids', []);
            $existingCount = $activity->galleries()
                ->whereNotIn('id', $removeIds)
                ->count();
            $newCount = count(array_filter($this->file('images', []) ?? []));

            if (($existingCount + $newCount) > 8) {
                $validator->errors()->add('images', 'Total gambar galeri (gambar tersisa + gambar baru) tidak boleh lebih dari 8.');
            }
        });
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
            'images.*.image' => 'File yang diunggah harus berupa gambar.',
            'images.*.mimes' => 'Format gambar galeri harus JPEG, PNG, atau WebP.',
            'images.*.max' => 'Ukuran masing-masing gambar galeri tidak boleh melebihi 2 MB.',
        ];
    }
}