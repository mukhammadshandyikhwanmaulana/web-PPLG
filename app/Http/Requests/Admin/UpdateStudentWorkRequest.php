<?php

namespace App\Http\Requests\Admin;

use App\Enums\PublishStatus;
use App\Models\StudentWork;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentWorkRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! auth()->check()) return false;
        $user = auth()->user();

        $isAdmin = (method_exists($user, 'hasRole') && $user->hasRole('admin')) || in_array(strtolower($user->role ?? ''), ['admin', 'superadmin']);
        if ($isAdmin) return true;

        $isGuru = (method_exists($user, 'hasRole') && $user->hasRole('guru')) || (strtolower($user->role ?? '') === 'guru');
        if ($isGuru) {
            $param = $this->route('karya_siswa') ?? $this->route('student_work') ?? collect($this->route()->parameters())->first();
            $studentWork = $param instanceof StudentWork ? $param : StudentWork::find($param);

            return $studentWork && ($studentWork->created_by === $user->id || $studentWork->user_id === $user->id);
        }

        return false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title'            => $this->filled('title') ? trim((string) $this->title) : null,
            'description'      => $this->filled('description') ? trim((string) $this->description) : null,
            'contributor_name' => $this->filled('contributor_name') ? trim((string) $this->contributor_name) : null,
            'supervisor_id'    => $this->filled('supervisor_id') ? $this->supervisor_id : null,
            'demo_url'         => $this->filled('demo_url') ? trim((string) $this->demo_url) : null,
            'is_featured'      => $this->boolean('is_featured'),
            'status'           => $this->filled('status') ? $this->status : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'title'                => ['required', 'string', 'max:255'],
            'description'          => ['nullable', 'string'],
            'contributor_name'     => ['nullable', 'string', 'max:255'],
            'supervisor_id'        => ['nullable', 'exists:staff_members,id'],
            'demo_url'             => ['nullable', 'url', 'max:255'],
            'is_featured'          => ['nullable', 'boolean'],
            'status'               => ['required', class_exists(PublishStatus::class) ? Rule::enum(PublishStatus::class) : 'string'],
            'cover'                => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'images'               => [
                'nullable', 
                'array', 
                function ($attribute, $value, $fail) {
                    $param = $this->route('karya_siswa') ?? $this->route('student_work') ?? collect($this->route()->parameters())->first();
                    $studentWork = $param instanceof StudentWork ? $param : StudentWork::find($param);

                    if ($studentWork) {
                        $removeIds = $this->input('remove_gallery_ids', []);
                        
                        $existingCount = $studentWork->galleries()
                            ->where('is_cover', false)
                            ->whereNotIn('id', (array) $removeIds)
                            ->count();

                        $newCount = is_array($value) ? count(array_filter($value)) : 0;

                        if (($existingCount + $newCount) > 5) {
                            $fail("Foto galeri pendukung tidak boleh lebih dari 5 foto. (Saat ini tersimpan: {$existingCount}, diunggah baru: {$newCount}).");
                        }
                    }
                }
            ],
            'images.*'             => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'remove_gallery_ids'   => ['nullable', 'array'],
            'remove_gallery_ids.*' => ['integer', 'exists:galleries,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'Judul karya siswa wajib diisi.',
            'title.max'            => 'Judul maksimal 255 karakter.',
            'supervisor_id.exists' => 'Guru pembimbing yang dipilih tidak valid.',
            'demo_url.url'         => 'Format URL demo tidak valid.',
            'cover.image'          => 'Cover harus berupa file gambar.',
            'cover.mimes'          => 'Format cover harus jpg, jpeg, png, atau webp.',
            'cover.max'            => 'Ukuran cover maksimal 10 MB.',
            'images.max'           => 'Maksimal 5 foto galeri tambahan.',
            'images.*.image'       => 'File galeri harus berupa gambar.',
            'images.*.max'         => 'Ukuran foto galeri maksimal 10 MB.',
            'status.required'      => 'Status publikasi wajib diisi.',
            'status.enum'          => 'Status publikasi tidak valid.',
        ];
    }
}