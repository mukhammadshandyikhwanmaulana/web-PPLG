<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'site_name'        => $this->filled('site_name') ? trim((string) $this->site_name) : null,
            'site_tagline'     => $this->filled('site_tagline') ? trim((string) $this->site_tagline) : null,
            'site_description' => $this->filled('site_description') ? trim((string) $this->site_description) : null,
            'contact_email'    => $this->filled('contact_email') ? trim(strtolower((string) $this->contact_email)) : null,
            'contact_phone'    => $this->filled('contact_phone') ? trim((string) $this->contact_phone) : null,
            'contact_address'  => $this->filled('contact_address') ? trim((string) $this->contact_address) : null,
            'tiktok_url'       => $this->filled('tiktok_url') ? trim((string) $this->tiktok_url) : null,
            'instagram_url'    => $this->filled('instagram_url') ? trim((string) $this->instagram_url) : null,
            'youtube_url'      => $this->filled('youtube_url') ? trim((string) $this->youtube_url) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'site_name'        => ['nullable', 'string', 'max:255'],
            'site_tagline'     => ['nullable', 'string', 'max:255'],
            'site_description' => ['nullable', 'string'],
            'contact_email'    => ['nullable', 'email:filter', 'max:255'],
            'contact_phone'    => ['nullable', 'string', 'max:50'],
            'contact_address'  => ['nullable', 'string'],
            'tiktok_url'       => ['nullable', 'url', 'max:255'],
            'instagram_url'    => ['nullable', 'url', 'max:255'],
            'youtube_url'      => ['nullable', 'url', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'contact_email.email' => 'Format email kontak tidak valid.',
            'contact_phone.max'   => 'Nomor telepon maksimal 50 karakter.',
            'site_name.max'       => 'Nama website maksimal 255 karakter.',
            'site_tagline.max'    => 'Tagline maksimal 255 karakter.',
            'tiktok_url.url'      => 'Tautan TikTok harus berupa URL valid.',
            'instagram_url.url'   => 'Tautan Instagram harus berupa URL valid.',
            'youtube_url.url'     => 'Tautan YouTube harus berupa URL valid.',
        ];
    }
}