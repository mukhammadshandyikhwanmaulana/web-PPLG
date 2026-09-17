<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePrincipalWelcomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! auth()->check()) return false;
        $user = auth()->user();

        $adminRole = UserRole::Admin->value;

        if (method_exists($user, 'hasRole')) {
            return $user->hasRole($adminRole);
        }

        $userRole = strtolower($user->role instanceof UserRole ? $user->role->value : ($user->role ?? ''));
        return $userRole === $adminRole;
    }

    protected function prepareForValidation(): void
    {
        $rawContent = $this->input('content');

        if ($rawContent) {
            $iteration = 0;
            // Decode berulang secara aman (maksimal 3x) untuk mencegah infinite loop
            while ($iteration < 3 && (str_contains($rawContent, '%25') || str_contains($rawContent, '%3D') || str_contains($rawContent, '%26'))) {
                $rawContent = urldecode($rawContent);
                $iteration++;
            }

            if (str_contains($rawContent, 'content=')) {
                $parts = explode('content=', $rawContent);
                $rawContent = end($parts);
            }

            $rawContent = trim((string) $rawContent);
        }

        $this->merge([
            'content' => $rawContent ?: null,
        ]);
    }

    public function rules(): array
    {
        return [
            'staff_member_id' => ['required', 'exists:staff_members,id'],
            'content'         => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'staff_member_id.required' => 'Ketua Kompetensi Keahlian wajib dipilih dari daftar guru/staf.',
            'staff_member_id.exists'   => 'Data guru/staf yang dipilih tidak ditemukan.',
            'content.required'         => 'Isi teks sambutan wajib diisi.',
        ];
    }
}