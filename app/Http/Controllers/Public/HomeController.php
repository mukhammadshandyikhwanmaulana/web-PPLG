<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Activity;
use App\Models\Banner;
use App\Models\Facility;
use App\Models\Faq;
use App\Models\IndustryPartner;
use App\Models\PrincipalWelcome;
use App\Models\ProfileContent;
use App\Models\StaffMember;
use App\Models\StudentWork;
use App\Models\UnitUsahaLink;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        // Ambil Data Profil Jurusan (Termasuk about_excerpt / Ringkasan PPLG)
        $profile = ProfileContent::first();

        // Ambil Banner Hero aktif
        $banners = Banner::query()
            ->where('is_active', true)
            ->orderBy('order', 'asc')
            ->orderByDesc('created_at')
            ->get();

        // Ambil Sambutan Kepala Jurusan beserta relasi Staff dan Foto Staf
        $principalWelcome = PrincipalWelcome::with(['staffMember.photo'])->first();

        // Format Teks Jabatan Sambutan Kakom/Kaprog agar Ringkas & Tidak Double Teks
        if ($principalWelcome && $principalWelcome->staffMember) {
            $principalWelcome->staffMember->display_position = 'KETUA KOMPETENSI KEAHLIAN PPLG';
        }

        // Hitung Total Data Otomatis untuk Statistik
        $totalAchievements = Achievement::published()->count();
        $totalStudentWorks = StudentWork::published()->count();
        $totalFacilities   = Facility::count();
        
        // Tahun Kelahiran / Berdiri Jurusan (Tetap & Statis)
        $tahunBerdiri = 2010; 

        // Ambil Kegiatan Terbaru
        $activities = Activity::with('cover')
            ->published()
            ->orderByDesc('event_date')
            ->take(6)
            ->get();

        // Ambil Prestasi Terbaru (3 Data)
        $achievements = Achievement::with('document')
            ->published()
            ->orderByDesc('achievement_date')
            ->take(3)
            ->get();

        // Ambil Karya Siswa Terbaru (4 Data)
        $studentWorks = StudentWork::with(['cover', 'supervisor', 'galleries'])
            ->published()
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->take(4)
            ->get();

        // Ambil Data Guru & Staf Aktif di Beranda (Filter / Saring Ketua agar tidak double dengan Sambutan)
        $staffMembers = StaffMember::with('photo')
            ->active()
            ->orderByPositionHierarchy()
            ->get()
            ->reject(function ($staff) {
                $pos = strtolower($staff->position ?? '');
                return str_contains($pos, 'ketua') || str_contains($pos, 'kaprog') || str_contains($pos, 'kajur');
            })
            ->take(6)
            ->values();

        // Ambil SELURUH Data Fasilitas (Tanpa dibatasi ->take(6) agar realtime dengan Admin)
        $facilities = Facility::with('photo')
            ->orderBy('sort_order')
            ->get();

        $partners = IndustryPartner::with('logo')
            ->published()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $faqs = Faq::active()
            ->orderBy('sort_order')
            ->take(5)
            ->get();

        // Ambil data Unit Usaha Link yang aktif
        $unitUsahaLink = UnitUsahaLink::where('is_active', true)->first();

        return view('public.home', [
            'profile'           => $profile,
            'banners'           => $banners,
            'principalWelcome'  => $principalWelcome,
            'totalAchievements' => $totalAchievements,
            'totalStudentWorks' => $totalStudentWorks,
            'totalFacilities'   => $totalFacilities,
            'tahunBerdiri'      => $tahunBerdiri,
            'activities'        => $activities,
            'achievements'      => $achievements,
            'studentWorks'      => $studentWorks,
            'staffMembers'      => $staffMembers,
            'facilities'        => $facilities,
            'partners'          => $partners,
            'faqs'              => $faqs,
            'unitUsahaLink'     => $unitUsahaLink,
        ]);
    }
}