<?php

namespace App\Models;

use App\Enums\PublishStatus;
use App\Notifications\AdminActivityNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'activities';

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'event_date',
        'content',
        'cover_media_id',
        'status',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected $appends = [
        'cover_url',
    ];

    protected function casts(): array
    {
        return [
            'event_date'     => 'date',
            'published_at'   => 'datetime',
            'status'         => PublishStatus::class,
            'user_id'        => 'integer',
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

        static::created(function (Activity $activity) {
            static::sendAdminNotification('created', $activity);
        });

        static::updated(function (Activity $activity) {
            static::sendAdminNotification('updated', $activity);
        });

        static::deleted(function (Activity $activity) {
            static::sendAdminNotification('deleted', $activity);
        });
    }

    protected static function sendAdminNotification(string $action, Activity $activity): void
    {
        $actor = auth()->user();
        if (! $actor) return;

        // Kirim notifikasi ke admin lain (Kecuali actor yang sedang membuat/mengubah data)
        $admins = User::query()
            ->admin()
            ->active()
            ->where('id', '!=', $actor->id)
            ->get();

        if ($admins->isEmpty()) return;

        $url = \Illuminate\Support\Facades\Route::has('admin.kegiatan.index') 
            ? route('admin.kegiatan.index') 
            : null;

        Notification::send($admins, new AdminActivityNotification(
            actor: $actor,
            action: $action,
            subjectType: 'Kegiatan',
            subjectName: $activity->title,
            subjectUrl: $action === 'deleted' ? null : $url
        ));
    }

    /* ================= ACCESSORS ================= */

    /**
     * Accessor Sampul Kegiatan yang Aman dari N+1 Query.
     */
    protected function coverUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->relationLoaded('cover') && $this->cover && ! empty($this->cover->path)) {
                    return Storage::disk($this->cover->disk ?? 'public')->url($this->cover->path);
                }

                if ($this->relationLoaded('galleries') && $this->galleries->isNotEmpty()) {
                    $firstGallery = $this->galleries->first();
                    if ($firstGallery && $firstGallery->relationLoaded('media') && $firstGallery->media && ! empty($firstGallery->media->path)) {
                        return Storage::disk($firstGallery->media->disk ?? 'public')->url($firstGallery->media->path);
                    }
                }

                return null;
            }
        );
    }

    /* ================= RELATIONS ================= */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

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

    /* ================= SCOPES ================= */

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PublishStatus::Published);
    }

    public function scopeLatest6(Builder $query): Builder
    {
        return $query->published()->orderByDesc('event_date')->limit(6);
    }
}