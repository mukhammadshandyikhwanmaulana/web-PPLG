<?php

namespace App\Models;

use App\Enums\AchievementLevel;
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

    protected $appends = [
        'document_url',
    ];

    protected function casts(): array
    {
        return [
            'achievement_date'  => 'date',
            'published_at'      => 'datetime',
            'level'             => AchievementLevel::class,
            'status'            => PublishStatus::class,
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

        // Kirim notifikasi ke admin lain (Kecuali actor yang sedang membuat/mengubah data)
        $admins = User::query()
            ->admin()
            ->active()
            ->where('id', '!=', $actor->id)
            ->get();

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

    /* ================= ACCESSORS ================= */

    /**
     * Accessor URL Dokumen/Sertifikat yang Aman dari N+1 Query.
     */
    protected function documentUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->relationLoaded('document') && $this->document && ! empty($this->document->path)) {
                    return Storage::disk($this->document->disk ?? 'public')->url($this->document->path);
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

    /* ================= SCOPES ================= */

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PublishStatus::Published);
    }

    public function scopeLatest3(Builder $query): Builder
    {
        return $query->published()->orderByDesc('achievement_date')->limit(3);
    }
}