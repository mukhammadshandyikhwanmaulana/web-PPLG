<?php

namespace App\Http\Requests\Admin;

use App\Enums\PublishStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIndustryPartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255', 'starts_with:http://,https://'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::enum(PublishStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama mitra wajib diisi.',
            'website_url.url' => 'Format URL tidak valid (harus diawali http:// atau https://).',
            'website_url.starts_with' => 'URL harus diawali http:// atau https://.',
            'logo.image' => 'Logo harus berupa gambar.',
            'logo.mimes' => 'Format logo harus JPEG, PNG, atau WebP.',
            'logo.max' => 'Ukuran logo tidak boleh melebihi 2 MB.',
            'sort_order.integer' => 'Urutan tampil harus berupa angka.',
            'sort_order.min' => 'Urutan tampil tidak boleh negatif.',
            'status.required' => 'Status publikasi wajib dipilih.',
        ];
    }
}