<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'media_id',
        'galleryable_id',
        'galleryable_type',
        'is_cover',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'media_id' => 'integer',
            'is_cover' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    public function galleryable(): MorphTo
    {
        return $this->morphTo();
    }
}