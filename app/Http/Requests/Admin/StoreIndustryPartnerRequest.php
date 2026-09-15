<?php

namespace App\Http\Requests\Admin;

use App\Enums\PublishStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIndustryPartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'        => $this->filled('name') ? trim((string) $this->name) : null,
            'website_url' => $this->filled('website_url') ? trim((string) $this->website_url) : null,
            'sort_order'  => $this->filled('sort_order') ? (int) $this->sort_order : null,
            'status'      => $this->filled('status') ? $this->status : PublishStatus::Draft->value,
        ]);
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'logo'        => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:10240'],
            'sort_order'  => ['nullable', 'integer', 'min:1'],
            'status'      => ['required', Rule::enum(PublishStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Nama mitra industri wajib diisi.',
            'name.max'           => 'Nama mitra maksimal 255 karakter.',
            'website_url.url'    => 'Format URL website tidak valid (contoh: https://example.com).',
            'logo.mimes'         => 'Logo harus berformat jpg, jpeg, png, webp, atau svg.',
            'logo.max'           => 'Ukuran logo maksimal 10MB.',
            'sort_order.integer' => 'Urutan harus berupa angka.',
            'sort_order.min'     => 'Urutan minimal bernilai 1.',
            'status.required'    => 'Status publikasi wajib dipilih.',
            'status.enum'        => 'Status publikasi tidak valid.',
        ];
    }
}