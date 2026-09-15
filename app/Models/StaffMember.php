<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    /**
     * Sembunyikan accessor bawaan jika di-serialize ke array/JSON (opsional)
     */
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
     * Accessor untuk memformat jabatan agar ringkas & tidak bertumpuk/double.
     * Dipanggil dengan: $staff->display_position
     */
    public function getDisplayPositionAttribute(): string
    {
        $originalPosition = $this->attributes['position'] ?? '';

        if (empty($originalPosition)) {
            return 'GURU PENGAJAR PPLG';
        }

        $positionLower = strtolower($originalPosition);

        // 1. UTAMAKAN GURU: Jika mengandung kata Guru / Pengajar / Produktif, 
        // kembalikan sebagai Guru (agar di grid daftar guru halaman profil tampil sebagai Guru)
        if (
            str_contains($positionLower, 'guru') || 
            str_contains($positionLower, 'pengajar') || 
            str_contains($positionLower, 'produktif')
        ) {
            return 'GURU PRODUKTIF PPLG';
        }

        // 2. Jika HANYA mengandung kata Ketua / Kaprog / Kepala (tanpa ada kata Guru)
        if (
            str_contains($positionLower, 'ketua') || 
            str_contains($positionLower, 'kaprog') || 
            str_contains($positionLower, 'kajur') || 
            str_contains($positionLower, 'kepala')
        ) {
            return 'KETUA KOMPETENSI KEAHLIAN PPLG';
        }

        // 3. Jika hanya Staf / Laboran
        if (
            str_contains($positionLower, 'staf') || 
            str_contains($positionLower, 'staff') || 
            str_contains($positionLower, 'laboran') || 
            str_contains($positionLower, 'admin')
        ) {
            return 'STAF / LABORAN PPLG';
        }

        // Fallback: Ambil pecahan pertama sebelum koma
        $positions = explode(',', $originalPosition);
        return strtoupper(trim($positions[0]));
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