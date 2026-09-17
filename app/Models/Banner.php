<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory;

    protected $table = 'banners';

    protected $fillable = [
        'title',
        'subtitle',
        'image_path',
        'button_text',
        'button_url',
        'order',
        'is_active',
    ];

    protected $appends = [
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order'     => 'integer',
        ];
    }

    public function photos(): HasMany
    {
        return $this->hasMany(BannerPhoto::class)->orderBy('sort_order', 'asc');
    }

    /**
     * Accessor URL Banner yang Aman dari N+1 Query.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->relationLoaded('photos') && $this->photos->isNotEmpty()) {
                    $firstPhoto = $this->photos->first();
                    if ($firstPhoto && ! empty($firstPhoto->file_path)) {
                        return Storage::disk('public')->url($firstPhoto->file_path);
                    }
                }

                if (! empty($this->image_path)) {
                    return Storage::disk('public')->url($this->image_path);
                }

                return asset('images/hero-bg.jpg');
            }
        );
    }
}