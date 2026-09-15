<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'image_path',
        'button_text',
        'button_url',
        'order',
        'is_active',
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

    public function getImageUrlAttribute(): string
    {
        $firstPhoto = $this->relationLoaded('photos') ? $this->photos->first() : $this->photos()->first();
        if ($firstPhoto) {
            return $firstPhoto->url;
        }

        if (! empty($this->image_path)) {
            return Storage::disk('public')->url($this->image_path);
        }

        return 'https://placehold.co/1280x720/4f46e5/white?text=Banner+PPLG';
    }
}