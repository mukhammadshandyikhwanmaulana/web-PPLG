<?php

namespace App\Models;

use App\Enums\AchievementLevel;
use App\Enums\PublishStatus;
use App\Notifications\AdminActivityNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class Achievement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'achievements';

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'achievement_date',
        'level',
        'contributor_name',
        'description',
        'document_media_id',
        'status',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'achievement_date'  => 'date',
            'published_at'       => 'datetime',
            'level'             => class_exists(AchievementLevel::class) ? AchievementLevel::class : 'string',
            'status'            => class_exists(PublishStatus::class) ? PublishStatus::class : 'string',
            'user_id'           => 'integer',
            'document_media_id' => 'integer',
            'created_by'        => 'integer',
            'updated_by'        => 'integer',
        ];
    }

    protected static function booted(): void
    {
        if (app()->runningInConsole() || ! auth()->check()) {
            return;
        }

        static::created(function (Achievement $achievement) {
            static::sendAdminNotification('created', $achievement);
        });

        static::updated(function (Achievement $achievement) {
            static::sendAdminNotification('updated', $achievement);
        });

        static::deleted(function (Achievement $achievement) {
            static::sendAdminNotification('deleted', $achievement);
        });
    }

    protected static function sendAdminNotification(string $action, Achievement $achievement): void
    {
        $actor = auth()->user();
        if (! $actor) return;

        $admins = User::query()->admin()->active()->get();
        if ($admins->isEmpty()) return;

        $url = \Illuminate\Support\Facades\Route::has('admin.prestasi.index') 
            ? route('admin.prestasi.index') 
            : null;

        Notification::send($admins, new AdminActivityNotification(
            actor: $actor,
            action: $action,
            subjectType: 'Prestasi',
            subjectName: $achievement->title,
            subjectUrl: $action === 'deleted' ? null : $url
        ));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'document_media_id');
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

    public function getDocumentUrlAttribute(): ?string
    {
        $doc = $this->relationLoaded('document') ? $this->document : $this->document()->first();
        if ($doc && ! empty($doc->path)) {
            return Storage::disk($doc->disk ?? 'public')->url($doc->path);
        }

        return null;
    }

    public function scopePublished(Builder $query): Builder
    {
        $publishedValue = class_exists(PublishStatus::class) ? PublishStatus::Published : 'published';
        return $query->where('status', $publishedValue);
    }

    public function scopeLatest3(Builder $query): Builder
    {
        return $query->published()->orderByDesc('achievement_date')->limit(3);
    }
}