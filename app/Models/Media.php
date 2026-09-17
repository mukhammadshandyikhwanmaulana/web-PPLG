<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'media';

    protected $fillable = [
        'original_name',
        'file_name',
        'disk',
        'path',
        'mime_type',
        'size',
        'alt_text',
        'created_by',
        'updated_by',
    ];

    protected $appends = [
        'url',
    ];

    protected function casts(): array
    {
        return [
            'size'       => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        // Otomatis hapus file fisik di disk storage saat model terhapus permanen (Force Delete)
        static::forceDeleted(function (Media $media) {
            if (! empty($media->path)) {
                $disk = $media->disk ?? 'public';
                if (Storage::disk($disk)->exists($media->path)) {
                    Storage::disk($disk)->delete($media->path);
                }
            }
        });
    }

    /* ================= ACCESSORS ================= */

    /**
     * Accessor URL Media yang Aman dari Path Kosong.
     */
    protected function url(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->path)) {
                    return null;
                }

                $diskName = $this->disk ?? 'public';
                return Storage::disk($diskName)->url($this->path);
            }
        );
    }

    public function getFilePathAttribute(): string
    {
        return $this->path ?? '';
    }

    public function getUploadedByAttribute(): ?int
    {
        return $this->created_by;
    }

    /* ================= RELATIONS ================= */

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}