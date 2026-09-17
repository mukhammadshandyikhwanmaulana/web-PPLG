<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BannerPhoto extends Model
{
    use HasFactory;

    protected $table = 'banner_photos';

    protected $fillable = [
        'banner_id', 
        'file_path', 
        'sort_order',
    ];

    protected $appends = [
        'url',
    ];

    protected function casts(): array
    {
        return [
            'banner_id'  => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function banner(): BelongsTo
    {
        return $this->belongsTo(Banner::class, 'banner_id');
    }

    /**
     * Accessor URL Foto Banner.
     */
    protected function url(): Attribute
    {
        return Attribute::make(
            get: function () {
                return ! empty($this->file_path)
                    ? Storage::disk('public')->url($this->file_path)
                    : asset('images/placeholder-pplg.webp');
            }
        );
    }
}