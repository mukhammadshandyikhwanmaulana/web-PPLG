<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'question'   => $this->filled('question') ? trim((string) $this->question) : null,
            'answer'     => $this->filled('answer') ? trim((string) $this->answer) : null,
            'sort_order' => $this->filled('sort_order') ? (int) $this->sort_order : null,
            'is_active'  => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        return [
            'question'   => ['required', 'string', 'max:255'],
            'answer'     => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active'  => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'question.required'  => 'Pertanyaan FAQ wajib diisi.',
            'question.max'       => 'Pertanyaan maksimal 255 karakter.',
            'answer.required'    => 'Jawaban FAQ wajib diisi.',
            'sort_order.integer' => 'Urutan harus berupa angka.',
            'sort_order.min'     => 'Urutan minimal bernilai 0.',
        ];
    }
}