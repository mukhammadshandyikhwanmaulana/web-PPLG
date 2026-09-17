<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StaffMember extends Model
{
    use HasFactory;

    protected $table = 'staff_members';

    protected $fillable = [
        'user_id',
        'name',
        'position',
        'expertise',
        'photo_media_id',
        'is_active',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $appends = [
        'display_position',
    ];

    protected function casts(): array
    {
        return [
            'is_active'      => 'boolean',
            'sort_order'     => 'integer',
            'user_id'        => 'integer',
            'photo_media_id' => 'integer',
            'created_by'     => 'integer',
            'updated_by'     => 'integer',
        ];
    }

    /* ================= ACCESSORS ================= */

    /**
     * Accessor aman untuk memformat jabatan staf.
     */
    protected function displayPosition(): Attribute
    {
        return Attribute::make(
            get: function () {
                $originalPosition = $this->position ?? '';

                if (empty($originalPosition)) {
                    return 'GURU PENGAJAR PPLG';
                }

                $positionLower = strtolower($originalPosition);

                if (
                    str_contains($positionLower, 'guru') || 
                    str_contains($positionLower, 'pengajar') || 
                    str_contains($positionLower, 'produktif')
                ) {
                    return 'GURU PRODUKTIF PPLG';
                }

                if (
                    str_contains($positionLower, 'ketua') || 
                    str_contains($positionLower, 'kaprog') || 
                    str_contains($positionLower, 'kajur') || 
                    str_contains($positionLower, 'kepala')
                ) {
                    return 'KETUA KOMPETENSI KEAHLIAN PPLG';
                }

                if (
                    str_contains($positionLower, 'staf') || 
                    str_contains($positionLower, 'staff') || 
                    str_contains($positionLower, 'laboran') || 
                    str_contains($positionLower, 'admin')
                ) {
                    return 'STAF / LABORAN PPLG';
                }

                $positions = explode(',', $originalPosition);
                return strtoupper(trim($positions[0]));
            }
        );
    }

    /* ================= RELATIONS ================= */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function photo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'photo_media_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function studentWorks(): HasMany
    {
        return $this->hasMany(StudentWork::class, 'supervisor_id');
    }

    public function principalWelcome(): HasOne
    {
        return $this->hasOne(PrincipalWelcome::class, 'staff_member_id');
    }

    /* ================= SCOPES ================= */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrderByPositionHierarchy(Builder $query): Builder
    {
        return $query->orderByRaw("
            CASE 
                WHEN LOWER(COALESCE(position, '')) LIKE '%ketua kompetensi keahlian%'
                  OR LOWER(COALESCE(position, '')) LIKE '%kepala jurusan%' 
                  OR LOWER(COALESCE(position, '')) LIKE '%kaprog%' 
                  OR LOWER(COALESCE(position, '')) LIKE '%kajur%' 
                  OR LOWER(COALESCE(position, '')) LIKE '%kepala program%' THEN 1
                WHEN LOWER(COALESCE(position, '')) LIKE '%guru%' THEN 2
                ELSE 3
            END ASC
        ")
        ->orderBy('name', 'asc');
    }
}