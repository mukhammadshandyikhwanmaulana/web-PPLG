<?php

namespace App\Http\Requests\Admin;

use App\Enums\AchievementLevel;
use App\Enums\PublishStatus;
use App\Models\Achievement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAchievementRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! auth()->check()) return false;
        $user = auth()->user();

        $isAdmin = (method_exists($user, 'hasRole') && $user->hasRole('admin')) || in_array(strtolower($user->role ?? ''), ['admin', 'superadmin']);
        if ($isAdmin) return true;

        $isGuru = (method_exists($user, 'hasRole') && $user->hasRole('guru')) || (strtolower($user->role ?? '') === 'guru');
        if ($isGuru) {
            $param = $this->route('achievement') ?? $this->route('prestasi') ?? collect($this->route()->parameters())->first();
            $achievement = $param instanceof Achievement ? $param : Achievement::find($param);

            return $achievement && ($achievement->created_by === $user->id || $achievement->user_id === $user->id);
        }

        return false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title'            => $this->filled('title') ? trim((string) $this->title) : null,
            'achievement_date' => $this->filled('achievement_date') ? $this->achievement_date : null,
            'level'            => $this->filled('level') ? $this->level : null,
            'contributor_name' => $this->filled('contributor_name') ? trim((string) $this->contributor_name) : null,
            'description'      => $this->filled('description') ? trim((string) $this->description) : null,
            'status'           => $this->filled('status') ? $this->status : null,
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