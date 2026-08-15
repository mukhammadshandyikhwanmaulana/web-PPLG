<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['media_id', 'galleryable_id', 'galleryable_type', 'sort_order'])]
class Gallery extends Model
{
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    public function galleryable(): MorphTo
    {
        return $this->morphTo();
    }
}