<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\StaffMember;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        // Password diambil dari .env (SEED_ADMIN_PASSWORD / SEED_GURU_PASSWORD) jika ada.
        // Jika tidak diisi, generate password acak dan tampilkan sekali di terminal.
        // Dengan cara ini, tidak ada password tetap yang tersimpan di dalam kode/repo.
        $adminPassword = env('SEED_ADMIN_PASSWORD') ?: Str::password(16);
        $guruPassword  = env('SEED_GURU_PASSWORD') ?: Str::password(16);

        DB::transaction(function () use ($adminPassword, $guruPassword) {
            // Reset cached roles dan permissions jika Spatie terpasang
            if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            }

            // 1. Buat Roles jika Spatie terpasang
            $adminRole = null;
            $guruRole  = null;

            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => UserRole::Admin->value, 'guard_name' => 'web']);
                $guruRole  = \Spatie\Permission\Models\Role::firstOrCreate(['name' => UserRole::Guru->value, 'guard_name' => 'web']);
            }

            // 2. Buat Akun Admin Utama
            $admin = User::firstOrCreate(
                ['email' => 'admin@smkn1bangsri.sch.id'],
                [
                    'name'              => 'Administrator Utama',
                    'password'          => Hash::make($adminPassword),
                    'role'              => UserRole::Admin->value,
                    'is_active'         => true,
                    'email_verified_at' => now(),
                ]
            );

            // Pastikan Role Spatie selalu tersinkronisasi
            if ($adminRole && method_exists($admin, 'assignRole')) {
                if (!$admin->hasRole(UserRole::Admin->value)) {
                    $admin->assignRole($adminRole);
                }
            }

            // 3. Buat Akun Guru Default
            $guruUser = User::firstOrCreate(
                ['email' => 'guru@smkn1bangsri.sch.id'],
                [
                    'name'              => 'Guru Pengajar Default',
                    'password'          => Hash::make($guruPassword),
                    'role'              => UserRole::Guru->value,
                    'is_active'         => true,
                    'email_verified_at' => now(),
                ]
            );

            // Pastikan Role Spatie selalu tersinkronisasi
            if ($guruRole && method_exists($guruUser, 'assignRole')) {
                if (!$guruUser->hasRole(UserRole::Guru->value)) {
                    $guruUser->assignRole($guruRole);
                }
            }

            // 4. Buat Profil StaffMember terkait untuk Guru
            StaffMember::firstOrCreate(
                ['user_id' => $guruUser->id],
                [
                    'name'       => $guruUser->name,
                    'position'   => 'Guru Produktif PPLG',
                    'expertise'  => 'Rekayasa Perangkat Lunak',
                    'is_active'  => true,
                    'sort_order' => 1,
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id,
                ]
            );
        });

        if ($this->command) {
            $this->command->newLine();
            $this->command->warn('=== Kredensial Akun Default (catat sekarang, tidak akan ditampilkan lagi) ===');
            $this->command->line("Admin  -> admin@smkn1bangsri.sch.id / {$adminPassword}");
            $this->command->line("Guru   -> guru@smkn1bangsri.sch.id / {$guruPassword}");
            $this->command->newLine();
        }
    }
}