<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StaffMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'position',
        'expertise',
        'photo_media_id',
        'is_active',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function photo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'photo_media_id');
    }

    public function studentWorks(): HasMany
    {
        return $this->hasMany(StudentWork::class, 'supervisor_id');
    }

    public function principalWelcome(): HasOne
    {
        return $this->hasOne(PrincipalWelcome::class);
    }
}