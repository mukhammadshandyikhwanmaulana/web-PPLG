<?php

namespace App\Http\Requests\Admin;

use App\Enums\PublishStatus;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreActivityRequest extends FormRequest
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

        // Jika dipanggil oleh Guru, paksa status ke Draft
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
            'title'      => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'content'    => ['nullable', 'string'],
            'cover'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'status'     => ['required', Rule::enum(PublishStatus::class)],
            'images'     => ['nullable', 'array', 'max:5'],
            'images.*'   => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
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
            'images.max'          => 'Jumlah foto galeri maksimal 5 foto.',
            'images.*.image'      => 'Setiap foto galeri harus berupa file gambar.',
            'images.*.max'        => 'Ukuran setiap foto galeri maksimal 10 MB.',
        ];
    }
}