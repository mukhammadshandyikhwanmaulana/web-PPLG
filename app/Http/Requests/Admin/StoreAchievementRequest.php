<?php

namespace App\Http\Requests\Admin;

use App\Enums\AchievementLevel;
use App\Enums\PublishStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAchievementRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! auth()->check()) return false;
        $user = auth()->user();

        $hasSpatieRole = method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'guru', 'superadmin']);
        $hasStringRole = in_array(strtolower($user->role ?? ''), ['admin', 'guru', 'superadmin']);

        return $hasSpatieRole || $hasStringRole;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title'            => $this->filled('title') ? trim((string) $this->title) : null,
            'achievement_date' => $this->filled('achievement_date') ? $this->achievement_date : null,
            'level'            => $this->filled('level') ? $this->level : null,
            'contributor_name' => $this->filled('contributor_name') ? trim((string) $this->contributor_name) : null,
            'description'      => $this->filled('description') ? trim((string) $this->description) : null,
            'status'           => $this->filled('status') ? $this->status : (class_exists(PublishStatus::class) ? PublishStatus::Draft->value : 'draft'),
        ]);
    }

    public function rules(): array
    {
        return [
            'title'            => ['required', 'string', 'max:255'],
            'achievement_date' => ['nullable', 'date'],
            'level'            => ['nullable', class_exists(AchievementLevel::class) ? Rule::enum(AchievementLevel::class) : 'string'],
            'contributor_name' => ['nullable', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'status'           => ['required', class_exists(PublishStatus::class) ? Rule::enum(PublishStatus::class) : 'string'],
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