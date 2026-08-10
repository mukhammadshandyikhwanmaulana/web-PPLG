<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleAndUserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * DEVELOPMENT ONLY. Password di bawah bukan credential production —
     * jangan pernah dipakai di environment production.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => UserRole::Guru->value, 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => UserRole::Admin->value, 'guard_name' => 'web']);

        $guru = User::factory()->create([
            'name' => 'Dev Guru',
            'email' => 'guru@dev.local',
            'password' => 'dev-password-guru',
            'is_active' => true,
        ]);
        $guru->assignRole(UserRole::Guru->value);

        $admin = User::factory()->create([
            'name' => 'Dev Admin',
            'email' => 'admin@dev.local',
            'password' => 'dev-password-admin',
            'is_active' => true,
        ]);
        $admin->assignRole(UserRole::Admin->value);
    }
}