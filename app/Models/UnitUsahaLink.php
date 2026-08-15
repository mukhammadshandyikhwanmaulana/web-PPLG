<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['label', 'external_url', 'is_active', 'updated_by'])]
class UnitUsahaLink extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Fallback behavior F-006 §14 — navbar tidak boleh mengarah ke halaman kosong.
     */
    public function isDisplayable(): bool
    {
        return $this->is_active && ! empty($this->external_url);
    }
}