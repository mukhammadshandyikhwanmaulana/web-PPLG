<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePrincipalWelcomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    protected function prepareForValidation(): void
    {
        $rawContent = $this->input('content');

        if ($rawContent) {
            // Decode berulang jika teks mengalami URL encoding bertumpuk (%253D, %26, dll)
            while (str_contains($rawContent, '%25') || str_contains($rawContent, '%3D') || str_contains($rawContent, '%26')) {
                $rawContent = urldecode($rawContent);
            }

            // Jika teks diawali/mengandung pecahan query string (_token=...&content=...), ambil isi teks murni paling akhir
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