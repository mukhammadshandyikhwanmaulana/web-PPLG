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

        // Akun Guru
        $guru = User::updateOrCreate(
            ['email' => 'guru@dev.local'],
            [
                'name' => 'Guru PPLG',
                'password' => bcrypt('dev-password-guru'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $guru->assignRole(UserRole::Guru->value);

        // Akun Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@dev.local'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('dev-password-admin'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole(UserRole::Admin->value);
    }
}