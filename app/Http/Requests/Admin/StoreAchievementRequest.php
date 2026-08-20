<?php

namespace App\Http\Requests\Admin;

use App\Enums\AchievementLevel;
use App\Enums\PublishStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAchievementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'achievement_date' => ['required', 'date'],
            'level' => ['required', Rule::in(array_column(AchievementLevel::cases(), 'value'))],
            'contributor_name' => ['nullable', 'string', 'max:255'],
            'document' => ['nullable', 'file', 'mimes:jpeg,png,webp,pdf', 'max:5120'],
            'status' => ['required', Rule::in(array_column(PublishStatus::cases(), 'value'))],
        ];
    }
}