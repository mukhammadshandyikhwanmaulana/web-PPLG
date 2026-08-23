<?php

namespace App\Models;

use App\Enums\PublishStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

#[Fillable(['title', 'slug', 'event_date', 'content', 'cover_media_id', 'status', 'published_at', 'created_by', 'updated_by'])]
class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'published_at' => 'datetime',
            'status' => PublishStatus::class,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function cover(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_media_id');
    }

    public function galleries(): MorphMany
    {
        return $this->morphMany(Gallery::class, 'galleryable')->orderBy('sort_order');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan URL gambar cover (atau null jika tidak ada)
     */
    public function getCoverUrlAttribute(): ?string
    {
        if ($this->cover && $this->cover->file_path) {
            return Storage::url($this->cover->file_path);
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PublishStatus::Published);
    }

    public function scopeLatest6(Builder $query): Builder
    {
        return $query->published()->orderByDesc('event_date')->limit(6);
    }
}