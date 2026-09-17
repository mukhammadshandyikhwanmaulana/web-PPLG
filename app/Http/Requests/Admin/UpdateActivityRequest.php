<?php

namespace App\Http\Requests\Admin;

use App\Enums\PublishStatus;
use App\Enums\UserRole;
use App\Models\Activity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateActivityRequest extends FormRequest
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
            $param = $this->route('kegiatan') ?? $this->route('activity') ?? collect($this->route()->parameters())->first();
            $activity = $param instanceof Activity ? $param : Activity::find($param);

            return $activity && ($activity->created_by === $user->id || $activity->user_id === $user->id);
        }

        return false;
    }

    protected function prepareForValidation(): void
    {
        $inputContent = $this->input('content');
        $cleanContent = is_string($inputContent) ? trim($inputContent) : null;
        $user = auth()->user();

        // Pengecekan role Admin secara presisi
        $isAdmin = false;
        if ($user) {
            if (method_exists($user, 'hasRole')) {
                $isAdmin = $user->hasRole(UserRole::Admin->value);
            } else {
                $userRole = strtolower($user->role instanceof UserRole ? $user->role->value : ($user->role ?? ''));
                $isAdmin = $userRole === UserRole::Admin->value;
            }
        }

        // Ambil status dari input, jika user adalah Guru paksa status tetap Draft
        $requestedStatus = $this->filled('status') ? $this->status : PublishStatus::Draft->value;
        $finalStatus = $isAdmin ? $requestedStatus : PublishStatus::Draft->value;

        $this->merge([
            'title'   => $this->filled('title') ? trim((string) $this->title) : null,
            'content' => $cleanContent !== '' ? $cleanContent : null,
            'status'  => $finalStatus,
        ]);
    }

    public function rules(): array
    {
        return [
            'title'                => ['required', 'string', 'max:255'],
            'event_date'           => ['required', 'date'],
            'content'              => ['nullable', 'string'],
            'cover'                => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'status'               => ['required', Rule::enum(PublishStatus::class)],
            'images'               => [
                'nullable', 
                'array', 
                function ($attribute, $value, $fail) {
                    $param = $this->route('kegiatan') ?? $this->route('activity') ?? collect($this->route()->parameters())->first();
                    $activity = $param instanceof Activity ? $param : Activity::find($param);

                    if ($activity) {
                        $removeIds = (array) $this->input('remove_gallery_ids', []);
                        
                        $existingCount = $activity->galleries()
                            ->whereNotIn('id', $removeIds)
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
            'title.required'      => 'Judul kegiatan wajib diisi.',
            'title.max'           => 'Judul kegiatan maksimal 255 karakter.',
            'event_date.required' => 'Tanggal kegiatan wajib diisi.',
            'event_date.date'     => 'Format tanggal kegiatan tidak valid.',
            'cover.image'         => 'Cover kegiatan harus berupa file gambar.',
            'cover.mimes'         => 'Format cover harus berupa jpg, jpeg, png, atau webp.',
            'cover.max'           => 'Ukuran cover maksimal 10 MB.',
            'status.required'     => 'Status publikasi wajib dipilih.',
            'status.enum'         => 'Status publikasi tidak valid.',
            'images.max'          => 'Jumlah foto galeri pendukung maksimal 5 foto.',
            'images.*.image'      => 'Setiap foto galeri harus berupa file gambar.',
            'images.*.max'        => 'Ukuran setiap foto galeri maksimal 10 MB.',
        ];
    }
}