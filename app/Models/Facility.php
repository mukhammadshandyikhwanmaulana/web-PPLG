<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Facility extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'facilities';

    protected $fillable = [
        'name',
        'description',
        'photo_media_id',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $appends = [
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'sort_order'     => 'integer',
            'photo_media_id' => 'integer',
            'created_by'     => 'integer',
            'updated_by'     => 'integer',
        ];
    }

    public function photo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'photo_media_id');
    }

    public function foto(): BelongsTo
    {
        return $this->photo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Accessor Gambar Fasilitas Aman dari N+1 Query.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->relationLoaded('photo') && $this->photo && ! empty($this->photo->path)) {
                    return Storage::disk($this->photo->disk ?? 'public')->url($this->photo->path);
                }

                return asset('images/placeholder-facility.webp');
            }
        );
    }
}