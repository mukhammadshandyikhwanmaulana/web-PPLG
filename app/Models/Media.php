<?php

namespace App\Models;

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

    protected function casts(): array
    {
        return [
            'size'       => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getUrlAttribute(): string
    {
        $diskName = $this->disk ?? 'public';
        return Storage::disk($diskName)->url($this->path);
    }

    public function getFilePathAttribute(): string
    {
        return $this->path;
    }

    public function getUploadedByAttribute(): ?int
    {
        return $this->created_by;
    }
}