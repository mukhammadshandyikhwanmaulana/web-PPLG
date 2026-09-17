<?php

namespace App\Http\Requests\Admin;

use App\Enums\AchievementLevel;
use App\Enums\PublishStatus;
use App\Enums\UserRole;
use App\Models\Achievement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAchievementRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! auth()->check()) return false;
        $user = auth()->user();

        $adminRole = UserRole::Admin->value;

        if (method_exists($user, 'hasRole') && $user->hasRole($adminRole)) {
            return true;
        }

        $userRole = strtolower($user->role instanceof UserRole ? $user->role->value : ($user->role ?? ''));
        if ($userRole === $adminRole) {
            return true;
        }

        $isGuru = (method_exists($user, 'hasRole') && $user->hasRole(UserRole::Guru->value)) || ($userRole === UserRole::Guru->value);
        if ($isGuru) {
            $param = $this->route('achievement') ?? $this->route('prestasi') ?? collect($this->route()->parameters())->first();
            $achievement = $param instanceof Achievement ? $param : Achievement::find($param);

            return $achievement && ($achievement->created_by === $user->id || $achievement->user_id === $user->id);
        }

        return false;
    }

    protected function prepareForValidation(): void
    {
        $user = auth()->user();
        $isGuru = false;

        if ($user) {
            $userRole = strtolower($user->role instanceof UserRole ? $user->role->value : ($user->role ?? ''));
            $isGuru = (method_exists($user, 'hasRole') && $user->hasRole(UserRole::Guru->value)) || ($userRole === UserRole::Guru->value);
        }

        // Paksakan status tetap Draft jika diubah oleh Guru
        $status = $isGuru 
            ? PublishStatus::Draft->value 
            : ($this->filled('status') ? $this->status : null);

        $this->merge([
            'title'            => $this->filled('title') ? trim((string) $this->title) : null,
            'achievement_date' => $this->filled('achievement_date') ? $this->achievement_date : null,
            'level'            => $this->filled('level') ? $this->level : null,
            'contributor_name' => $this->filled('contributor_name') ? trim((string) $this->contributor_name) : null,
            'description'      => $this->filled('description') ? trim((string) $this->description) : null,
            'status'           => $status,
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
            'level.enum'            => 'Tingkat prestasi tidak valid.',
            'contributor_name.max'  => 'Nama peraih prestasi maksimal 255 karakter.',
            'document.file'         => 'Dokumen harus berupa berkas valid.',
            'document.mimes'        => 'Dokumen harus berformat PDF, PNG, JPG, JPEG, atau WEBP.',
            'document.max'          => 'Ukuran dokumen maksimal 10 MB.',
            'status.required'       => 'Status publikasi wajib diisi.',
            'status.enum'           => 'Status publikasi tidak valid.',
        ];
    }
}