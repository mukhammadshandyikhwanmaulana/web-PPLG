<?php

namespace App\Models;

use App\Enums\PublishStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class IndustryPartner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'logo_media_id',
        'website_url',
        'sort_order',
        'status',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'published_at'  => 'datetime',
            'status'        => class_exists(PublishStatus::class) ? PublishStatus::class : 'string',
            'sort_order'    => 'integer',
            'logo_media_id' => 'integer',
            'created_by'    => 'integer',
            'updated_by'    => 'integer',
        ];
    }

    public function logo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo_media_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getLogoUrlAttribute(): ?string
    {
        $logoMedia = $this->relationLoaded('logo') ? $this->logo : $this->logo()->first();
        if ($logoMedia && ! empty($logoMedia->path)) {
            return Storage::disk($logoMedia->disk ?? 'public')->url($logoMedia->path);
        }

        return null;
    }

    public function scopePublished(Builder $query): Builder
    {
        $publishedValue = class_exists(PublishStatus::class) ? PublishStatus::Published : 'published';
        return $query->where('status', $publishedValue);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}