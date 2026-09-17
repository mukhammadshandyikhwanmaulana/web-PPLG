<?php

namespace App\Models;

use App\Enums\PublishStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class IndustryPartner extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'industry_partners';

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

    protected $appends = [
        'logo_url',
    ];

    protected function casts(): array
    {
        return [
            'published_at'  => 'datetime',
            'status'        => PublishStatus::class,
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

    /**
     * Accessor Logo URL yang Aman dari N+1 Query.
     */
    protected function logoUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->relationLoaded('logo') && $this->logo && ! empty($this->logo->path)) {
                    return Storage::disk($this->logo->disk ?? 'public')->url($this->logo->path);
                }

                return null;
            }
        );
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PublishStatus::Published);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }
}