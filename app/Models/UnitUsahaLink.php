<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitUsahaLink extends Model
{
    use HasFactory;

    protected $table = 'unit_usaha_links';

    protected $fillable = [
        'label',
        'external_url',
        'is_active',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'updated_by' => 'integer',
        ];
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function isDisplayable(): bool
    {
        return $this->is_active && ! empty($this->external_url);
    }
}