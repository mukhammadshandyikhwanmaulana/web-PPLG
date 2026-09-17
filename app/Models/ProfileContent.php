<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileContent extends Model
{
    use HasFactory;

    protected $table = 'profile_contents';

    protected $fillable = [
        'history_content',
        'vision_content',
        'mission_content',
        'about_excerpt',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'updated_by' => 'integer',
        ];
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}