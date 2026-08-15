<?php

namespace App\Models;

use App\Enums\AchievementLevel;
use App\Enums\PublishStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['title', 'slug', 'achievement_date', 'level', 'contributor_name', 'description', 'document_media_id', 'status', 'published_at', 'created_by', 'updated_by'])]
class Achievement extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'achievement_date' => 'date',
            'published_at' => 'datetime',
            'level' => AchievementLevel::class,
            'status' => PublishStatus::class,
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'document_media_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PublishStatus::Published);
    }

    public function scopeLatest3(Builder $query): Builder
    {
        return $query->published()->orderByDesc('achievement_date')->limit(3);
    }
}