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

    protected $appends = [
        'cover_url',
    ];

    protected function casts(): array
    {
        return [
            'is_featured'   => 'boolean',
            'published_at'  => 'datetime',
            'status'        => PublishStatus::class,
            'user_id'       => 'integer',
            'supervisor_id' => 'integer',
            'cover_media_id'=> 'integer',
            'created_by'    => 'integer',
            'updated_by'    => 'integer',
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

        // Kirim notifikasi ke admin lain (Kecuali actor yang sedang membuat/mengubah data)
        $admins = User::query()
            ->admin()
            ->active()
            ->where('id', '!=', $actor->id)
            ->get();

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

    /* ================= ACCESSORS ================= */

    /**
     * Accessor Sampul Karya yang Aman dari N+1 Query.
     */
    protected function coverUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Gunakan relasi yang di-load tanpa memaksa query tambahan ke DB
                if ($this->relationLoaded('cover') && $this->cover) {
                    return $this->cover->url;
                }

                if ($this->relationLoaded('galleries') && $this->galleries->isNotEmpty()) {
                    $firstGallery = $this->galleries->first();
                    if ($firstGallery && $firstGallery->relationLoaded('media') && $firstGallery->media) {
                        return $firstGallery->media->url;
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

    /* ================= SCOPES ================= */

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PublishStatus::Published);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->published()->where('is_featured', true);
    }
}