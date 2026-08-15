<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Activity;
use App\Models\Facility;
use App\Models\IndustryPartner;
use App\Models\PrincipalWelcome;
use App\Models\ProfileContent;
use App\Models\StaffMember;
use App\Models\StudentWork;
use App\Models\UnitUsahaLink;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CmsDemoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Data development/demo — BUKAN konten sekolah asli/final.
     * Tidak ada truncate/delete — murni menambah data baru.
     */
    public function run(): void
    {
        ProfileContent::firstOrCreate([], [
            'history_content' => 'Contoh teks sejarah RPL menjadi PPLG (data demo, belum konten final).',
            'vision_mission_content' => 'Contoh teks visi & misi (data demo).',
            'about_excerpt' => 'Contoh ringkasan Tentang PPLG untuk Beranda (data demo).',
        ]);

        UnitUsahaLink::firstOrCreate([], [
            'label' => 'Kunjungi Unit Usaha Kami',
            'external_url' => null,
            'is_active' => false,
        ]);

        $staff = StaffMember::factory()->count(5)->create();

        PrincipalWelcome::firstOrCreate([], [
            'staff_member_id' => $staff->first()->id,
            'content' => 'Contoh teks sambutan Kepala Jurusan (data demo, belum konten final).',
        ]);

        Facility::factory()->count(6)->create();

        Achievement::factory()->count(8)->create();

        StudentWork::factory()->count(6)->create()->each(function ($work) use ($staff) {
            $work->supervisor_id = $staff->random()->id;
            $work->save();
        });

        Activity::factory()->count(10)->create();

        IndustryPartner::factory()->count(4)->create();
    }
}