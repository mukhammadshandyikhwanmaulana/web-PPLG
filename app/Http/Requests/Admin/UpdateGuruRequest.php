<?php

namespace App\Http\Requests\Admin;

use App\Models\StaffMember;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateGuruRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! auth()->check()) return false;
        $user = auth()->user();

        return (method_exists($user, 'hasRole') && $user->hasRole('admin')) 
            || in_array(strtolower($user->role ?? ''), ['admin', 'superadmin']);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'            => $this->filled('name') ? trim((string) $this->name) : null,
            'email'           => $this->filled('email') ? trim(strtolower((string) $this->email)) : null,
            'expertise'       => $this->filled('expertise') ? trim((string) $this->expertise) : null,
            'is_active'       => $this->boolean('is_active'),
            'staff_is_active' => $this->boolean('staff_is_active'),
        ]);
    }

    public function rules(): array
    {
        $guruParam = $this->route('guru');
        $guruId = is_object($guruParam) ? $guruParam->id : $guruParam;

        return [
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'string', 'email:filter', 'max:255', Rule::unique('users', 'email')->ignore($guruId)],
            'password'        => [
                'nullable',
                'confirmed',
                Password::min(8)->letters()->numbers(),
            ],
            'positions'       => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) use ($guruId) {
                    if (is_array($value) && (in_array('Ketua Kompetensi Keahlian PPLG', $value, true) || in_array('Kepala Jurusan PPLG', $value, true))) {
                        $exists = StaffMember::where(function ($query) {
                            $query->where('position', 'like', '%Ketua Kompetensi Keahlian%')
                                  ->orWhere('position', 'like', '%Kepala Jurusan%');
                        })
                        ->where('user_id', '!=', $guruId)
                        ->exists();
                        if ($exists) {
                            $fail('Jabatan Ketua Kompetensi Keahlian sudah terisi oleh guru lain.');
                        }
                    }
                },
            ],
            'positions.*'     => ['string', 'max:255'],
            'expertise'       => ['nullable', 'string', 'max:255'],
            'photo'           => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'is_active'       => ['nullable', 'boolean'],
            'staff_is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'email.required'     => 'Alamat email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Alamat email ini sudah terdaftar.',
            'password.min'       => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'photo.image'        => 'Berkas foto profil harus berupa gambar.',
            'photo.mimes'        => 'Foto profil harus berformat JPG, JPEG, PNG, atau WEBP.',
            'photo.max'          => 'Ukuran foto profil tidak boleh melebihi 10MB.',
        ];
    }
}