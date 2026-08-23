<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Activity;
use App\Models\Facility;
use App\Models\Gallery;
use App\Models\IndustryPartner;
use App\Models\Media;
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
            'vision_content' => 'Contoh teks visi (data demo).',
            'mission_content' => 'Contoh teks misi (data demo).',
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

        // Generate Kegiatan lengkap dengan Cover & Galeri Foto tiruan
        Activity::factory()->count(10)->create()->each(function (Activity $activity) {
            // 1. Buat Media Cover
            $coverMedia = Media::create([
                'file_name' => 'demo-cover.jpg',
                'file_path' => 'activities/cover/demo-cover.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 102400,
            ]);

            $activity->update(['cover_media_id' => $coverMedia->id]);

            // 2. Buat 2 - 4 Foto Galeri
            $galleryCount = rand(2, 4);
            for ($i = 0; $i < $galleryCount; $i++) {
                $galleryMedia = Media::create([
                    'file_name' => "demo-gallery-{$i}.jpg",
                    'file_path' => "activities/gallery/demo-gallery-{$i}.jpg",
                    'mime_type' => 'image/jpeg',
                    'size' => 102400,
                ]);

                Gallery::create([
                    'media_id' => $galleryMedia->id,
                    'galleryable_id' => $activity->id,
                    'galleryable_type' => Activity::class,
                    'sort_order' => $i,
                ]);
            }
        });

        IndustryPartner::factory()->count(4)->create();
    }
}