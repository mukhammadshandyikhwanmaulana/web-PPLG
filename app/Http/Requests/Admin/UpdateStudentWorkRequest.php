<?php

namespace App\Http\Requests\Admin;

use App\Enums\PublishStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateStudentWorkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    protected function prepareForValidation(): void
    {
        // Memastikan checkbox is_featured selalu bernilai boolean true/false
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
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,webp', 'max:2048'],
            'remove_gallery_ids' => ['nullable', 'array'],
            'remove_gallery_ids.*' => ['integer', 'exists:galleries,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            // Mengakomodasi penamaan parameter route: student_work, studentWork, maupun karya_siswa
            $studentWork = $this->route('student_work');

            if (! $studentWork) {
                return;
            }

            $removeIds = $this->input('remove_gallery_ids', []);
            $existingCount = $studentWork->galleries()
                ->whereNotIn('id', $removeIds)
                ->count();
            $newCount = count(array_filter($this->file('images', []) ?? []));

            if (($existingCount + $newCount) > 5) {
                $validator->errors()->add('images', 'Total gambar (gambar tersisa + gambar baru) tidak boleh lebih dari 5.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul karya wajib diisi.',
            'status.required' => 'Status publikasi wajib dipilih.',
            'demo_url.url' => 'Format link demo/website tidak valid (harus diawali http:// atau https://).',
            'images.*.image' => 'File yang diunggah harus berupa gambar.',
            'images.*.mimes' => 'Format gambar harus JPEG, PNG, atau WebP.',
            'images.*.max' => 'Ukuran masing-masing gambar tidak boleh melebihi 2 MB.',
        ];
    }
}