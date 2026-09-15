<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BannerPhoto extends Model
{
    use HasFactory;

    protected $fillable = ['banner_id', 'file_path', 'sort_order'];

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

    public function getUrlAttribute(): string
    {
        return ! empty($this->file_path)
            ? Storage::disk('public')->url($this->file_path)
            : 'https://placehold.co/1280x720/4f46e5/white?text=No+Image';
    }
}