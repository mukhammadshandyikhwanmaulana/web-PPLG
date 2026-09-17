<?php

namespace Database\Seeders;

use App\Models\PrincipalWelcome;
use App\Models\ProfileContent;
use App\Models\StaffMember;
use App\Models\UnitUsahaLink;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Inisialisasi Role & Akun Utama (Admin & Guru Default)
        $this->call([
            RoleAndUserSeeder::class,
        ]);

        // Ambil User Admin utama (dengan fallback ke user pertama jika email berbeda)
        $adminUser = User::where('email', 'admin@smkn1bangsri.sch.id')->first() ?? User::first();

        // Ambil atau buat Staff Member default jika belum ada dari RoleAndUserSeeder
        $defaultStaff = StaffMember::first();

        if (!$defaultStaff && $adminUser) {
            $defaultStaff = StaffMember::create([
                'user_id'   => $adminUser->id,
                'name'      => 'Ketua Program Keahlian PPLG',
                'position'  => 'Ketua Program Keahlian',
                'nip'       => '-',
                'is_active' => true,
            ]);
        }

        // 2. Inisialisasi Data Default Single-Row (Profil, Unit Usaha, & Sambutan KAJUR)
        
        // Profile Content
        ProfileContent::firstOrCreate(
            ['id' => 1], // Mengunci ID 1 untuk single-row pattern
            [
                'about_excerpt'   => 'Selamat datang di website resmi kompetensi keahlian PPLG SMKN 1 Bangsri.',
                'history_content' => 'Kompetensi Keahlian Pengembangan Perangkat Lunak dan Gim (PPLG) berdiri untuk menghasilkan tenaga terampil di bidang pemrograman dan teknologi.',
                'vision_content'  => 'Menjadi program keahlian yang unggul, berkarakter, dan berdaya saing global di bidang rekayasa perangkat lunak dan gim.',
                'mission_content' => 'Menyelenggarakan pembelajaran berbasis proyek (PBL) dan standar industri IT.',
                'updated_by'      => $adminUser?->id,
            ]
        );

        // Unit Usaha Link
        UnitUsahaLink::firstOrCreate(
            ['id' => 1],
            [
                'label'        => 'Unit Usaha PPLG',
                'external_url' => 'https://smkn1bangsri.sch.id',
                'is_active'    => false,
                'updated_by'   => $adminUser?->id,
            ]
        );

        // Principal Welcome (Sambutan Kaprog)
        PrincipalWelcome::firstOrCreate(
            ['id' => 1],
            [
                'staff_member_id' => $defaultStaff?->id,
                'content'         => 'Selamat datang di website resmi Jurusan Pengembangan Perangkat Lunak dan Gim (PPLG) SMKN 1 Bangsri.',
                'updated_by'      => $adminUser?->id,
            ]
        );
    }
}