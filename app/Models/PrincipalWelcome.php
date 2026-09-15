<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrincipalWelcome extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_member_id',
        'content',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'staff_member_id' => 'integer',
            'updated_by'      => 'integer',
        ];
    }

    public function staffMember(): BelongsTo
    {
        return $this->belongsTo(StaffMember::class, 'staff_member_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}