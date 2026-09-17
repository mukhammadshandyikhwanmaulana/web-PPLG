<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'avatar',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Mengurangi beban N+1 query dengan tidak memasukkan role_name ke $appends global
    protected $appends = [
        'avatar_url',
    ];

    /**
     * Accessor Foto Profil / Avatar yang Aman
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->avatar)) {
                    return null;
                }

                // Jika avatar sudah berupa URL eksternal (http/https)
                if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
                    return $this->avatar;
                }

                $v = $this->updated_at ? $this->updated_at->timestamp : time();
                return Storage::disk('public')->url($this->avatar) . '?v=' . $v;
            }
        );
    }

    /**
     * Accessor Nama Role (Hanya dipanggil sesuai kebutuhan tanpa memicu N+1 Query)
     */
    protected function roleName(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->relationLoaded('roles') && $this->roles->isNotEmpty()) {
                    return $this->roles->first()->name;
                }

                return $this->role instanceof UserRole 
                    ? $this->role->value 
                    : ($this->role ?? 'guru');
            }
        );
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'role'              => UserRole::class, // Cast otomatis ke Enum UserRole
        ];
    }

    public function scopeAdmin($query)
    {
        return $query->where(function ($q) {
            $q->whereHas('roles', function ($r) {
                $r->whereIn('name', ['admin', 'super-admin', 'superadmin']);
            })->orWhereIn('role', ['admin', 'super-admin', 'superadmin']);
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function staffMember(): HasOne
    {
        return $this->hasOne(StaffMember::class, 'user_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }
}