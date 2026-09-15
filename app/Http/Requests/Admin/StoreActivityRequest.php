<?php

namespace App\Http\Requests\Admin;

use App\Enums\PublishStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreActivityRequest extends FormRequest
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
        $inputContent = $this->input('content');
        $cleanContent = is_string($inputContent) ? trim($inputContent) : null;

        $this->merge([
            'title'   => $this->filled('title') ? trim((string) $this->title) : null,
            'content' => $cleanContent !== '' ? $cleanContent : null,
            'status'  => $this->filled('status') ? $this->status : (class_exists(PublishStatus::class) ? PublishStatus::Draft->value : 'draft'),
        ]);
    }

    public function rules(): array
    {
        return [
            'title'      => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'content'    => ['nullable', 'string'],
            'cover'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'status'     => ['required', class_exists(PublishStatus::class) ? Rule::enum(PublishStatus::class) : 'string'],
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