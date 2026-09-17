<?php

namespace App\Http\Requests\Admin;

use App\Enums\PublishStatus;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentWorkRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! auth()->check()) return false;
        $user = auth()->user();

        $allowedRoles = [UserRole::Admin->value, UserRole::Guru->value];

        if (method_exists($user, 'hasAnyRole')) {
            return $user->hasAnyRole($allowedRoles);
        }

        $userRole = strtolower($user->role instanceof UserRole ? $user->role->value : ($user->role ?? ''));
        return in_array($userRole, $allowedRoles, true);
    }

    protected function prepareForValidation(): void
    {
        $user = auth()->user();
        $isGuru = false;
        
        if ($user) {
            $userRole = strtolower($user->role instanceof UserRole ? $user->role->value : ($user->role ?? ''));
            $isGuru = (method_exists($user, 'hasRole') && $user->hasRole(UserRole::Guru->value)) || ($userRole === UserRole::Guru->value);
        }

        // Jika user adalah Guru, paksakan status publikasi menjadi Draft
        $status = $isGuru 
            ? PublishStatus::Draft->value 
            : ($this->filled('status') ? $this->status : PublishStatus::Draft->value);

        $this->merge([
            'title'            => $this->filled('title') ? trim((string) $this->title) : null,
            'description'      => $this->filled('description') ? trim((string) $this->description) : null,
            'contributor_name' => $this->filled('contributor_name') ? trim((string) $this->contributor_name) : null,
            'supervisor_id'    => $this->filled('supervisor_id') ? $this->supervisor_id : null,
            'demo_url'         => $this->filled('demo_url') ? trim((string) $this->demo_url) : null,
            'is_featured'      => $this->boolean('is_featured'),
            'status'           => $status,
        ]);
    }

    public function rules(): array
    {
        return [
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'contributor_name' => ['nullable', 'string', 'max:255'],
            'supervisor_id'    => ['nullable', 'exists:staff_members,id'],
            'demo_url'         => ['nullable', 'url', 'max:255'],
            'is_featured'      => ['nullable', 'boolean'],
            'status'           => ['required', Rule::enum(PublishStatus::class)],
            'cover'            => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'images'           => ['nullable', 'array', 'max:5'],
            'images.*'         => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
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