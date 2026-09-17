<?php

namespace App\Http\Requests\Admin;

use App\Enums\AchievementLevel;
use App\Enums\PublishStatus;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAchievementRequest extends FormRequest
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
        $this->merge([
            'title'            => $this->filled('title') ? trim((string) $this->title) : null,
            'achievement_date' => $this->filled('achievement_date') ? $this->achievement_date : null,
            'level'            => $this->filled('level') ? $this->level : null,
            'contributor_name' => $this->filled('contributor_name') ? trim((string) $this->contributor_name) : null,
            'description'      => $this->filled('description') ? trim((string) $this->description) : null,
            'status'           => $this->filled('status') ? $this->status : PublishStatus::Draft->value,
        ]);
    }

    public function rules(): array
    {
        return [
            'title'            => ['required', 'string', 'max:255'],
            'achievement_date' => ['nullable', 'date'],
            'level'            => ['nullable', Rule::enum(AchievementLevel::class)],
            'contributor_name' => ['nullable', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'status'           => ['required', Rule::enum(PublishStatus::class)],
            'document'         => ['nullable', 'file', 'mimes:pdf,png,jpg,jpeg,webp', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'        => 'Judul prestasi wajib diisi.',
            'title.max'             => 'Judul prestasi tidak boleh lebih dari 255 karakter.',
            'achievement_date.date' => 'Format tanggal prestasi tidak valid.',
            'level.enum'            => 'Tingkat prestasi yang dipilih tidak valid.',
            'contributor_name.max'  => 'Nama peraih prestasi tidak boleh lebih dari 255 karakter.',
            'document.file'         => 'Dokumen yang diunggah harus berupa berkas valid.',
            'document.mimes'        => 'Dokumen harus berupa berkas berformat PDF, PNG, JPG, JPEG, atau WEBP.',
            'document.max'          => 'Ukuran berkas dokumen tidak boleh lebih dari 10 MB.',
            'status.required'       => 'Status publikasi wajib diisi.',
            'status.enum'           => 'Status publikasi tidak valid.',
        ];
    }
}