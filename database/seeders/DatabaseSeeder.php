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

        // Ambil User Admin dan Staff Member Guru untuk nilai awal foreign key
        $adminUser = User::where('email', 'admin@smkn1bangsri.sch.id')->first();
        $defaultStaff = StaffMember::first();

        // 2. Inisialisasi Data Default Single-Row (Profil, Unit Usaha, & Sambutan KAJUR)
        ProfileContent::firstOrCreate([], [
            'about_excerpt'   => 'Selamat datang di website resmi kompetensi keahlian PPLG.',
            'history_content' => 'Kompetensi Keahlian Pengembangan Perangkat Lunak dan Gim (PPLG) berdiri untuk menghasilkan tenaga terampil di bidang pemrograman dan teknologi.',
            'vision_content'  => 'Menjadi program keahlian yang unggul, berkarakter, dan berdaya saing global di bidang rekayasa perangkat lunak dan gim.',
            'mission_content' => 'Menyelenggarakan pembelajaran berbasis proyek (PBL) dan standar industri IT.',
            'updated_by'      => $adminUser?->id,
        ]);

        UnitUsahaLink::firstOrCreate([], [
            'label'        => 'Unit Usaha PPLG',
            'external_url' => 'https://smkn1bangsri.sch.id', // <-- DIUBAH MENJADI 'external_url'
            'is_active'    => false,
            'updated_by'   => $adminUser?->id,
        ]);

        PrincipalWelcome::firstOrCreate([], [
            'staff_member_id' => $defaultStaff?->id,
            'content'         => 'Selamat datang di website resmi Jurusan Pengembangan Perangkat Lunak dan Gim (PPLG).',
            'updated_by'      => $adminUser?->id,
        ]);
    }
}