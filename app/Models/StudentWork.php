<?php

namespace App\Models;

use App\Enums\PublishStatus;
use App\Notifications\AdminActivityNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Notification;

class StudentWork extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'student_works';

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'contributor_name',
        'supervisor_id',
        'demo_url',
        'cover_media_id',
        'is_featured',
        'status',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_featured'    => 'boolean',
            'published_at'   => 'datetime',
            'status'         => class_exists(PublishStatus::class) ? PublishStatus::class : 'string',
            'user_id'        => 'integer',
            'supervisor_id'  => 'integer',
            'cover_media_id' => 'integer',
            'created_by'     => 'integer',
            'updated_by'     => 'integer',
        ];
    }

    protected static function booted(): void
    {
        if (app()->runningInConsole() || ! auth()->check()) {
            return;
        }

        static::created(function (StudentWork $studentWork) {
            static::sendAdminNotification('created', $studentWork);
        });

        static::updated(function (StudentWork $studentWork) {
            static::sendAdminNotification('updated', $studentWork);
        });

        static::deleted(function (StudentWork $studentWork) {
            static::sendAdminNotification('deleted', $studentWork);
        });
    }

    protected static function sendAdminNotification(string $action, StudentWork $studentWork): void
    {
        $actor = auth()->user();
        if (! $actor) return;

        $admins = User::query()->admin()->active()->get();
        if ($admins->isEmpty()) return;

        $url = \Illuminate\Support\Facades\Route::has('admin.karya-siswa.index') 
            ? route('admin.karya-siswa.index') 
            : null;

        Notification::send($admins, new AdminActivityNotification(
            actor: $actor,
            action: $action,
            subjectType: 'Karya Siswa',
            subjectName: $studentWork->title,
            subjectUrl: $action === 'deleted' ? null : $url
        ));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cover(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_media_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(StaffMember::class, 'supervisor_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function galleries(): MorphMany
    {
        return $this->morphMany(Gallery::class, 'galleryable')->orderBy('sort_order');
    }

    public function getCoverUrlAttribute(): ?string
    {
        $coverMedia = $this->relationLoaded('cover') ? $this->cover : $this->cover()->first();
        if ($coverMedia) {
            return $coverMedia->url;
        }

        if ($this->relationLoaded('galleries') && $this->galleries->isNotEmpty()) {
            $firstGallery = $this->galleries->first();
            if ($firstGallery && $firstGallery->media) {
                return $firstGallery->media->url;
            }
        }

        return null;
    }

    public function scopePublished(Builder $query): Builder
    {
        $publishedValue = class_exists(PublishStatus::class) ? PublishStatus::Published : 'published';
        return $query->where('status', $publishedValue);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->published()->where('is_featured', true);
    }
}