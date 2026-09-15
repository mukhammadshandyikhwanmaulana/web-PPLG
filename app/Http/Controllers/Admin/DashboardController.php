<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Activity;
use App\Models\Facility;
use App\Models\IndustryPartner;
use App\Models\Media;
use App\Models\StaffMember;
use App\Models\StudentWork;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $partnerCount = IndustryPartner::count();

        // Hitung User ber-role 'guru' yang aktif (sinkron dengan GuruController)
        $guruCount = User::query()
            ->where(function ($q) {
                if (method_exists(User::class, 'scopeRole')) {
                    $q->role('guru');
                } else {
                    $q->whereHas('roles', fn ($r) => $r->where('name', 'guru'))
                      ->orWhere('role', 'guru');
                }
            })
            ->where('is_active', true)
            ->count();

        return view('admin.dashboard', [
            'guruCount'            => $guruCount,
            'facilityCount'        => Facility::count(),
            'achievementCount'     => Achievement::count(),
            'studentWorkCount'     => StudentWork::count(),
            'activityCount'        => Activity::count(),
            'partnerCount'         => $partnerCount,
            'industryPartnerCount' => $partnerCount,
            
            // Statistik Modul Media
            'mediaCount'           => Media::count(),
            'totalMediaSize'       => Media::sum('size') ?? 0,

            // Ringkasan Widget Terbaru
            'recentActivities'     => Activity::latest('created_at')->take(5)->get(),
            'recentStudentWorks'   => StudentWork::latest('created_at')->take(5)->get(),
            'recentMedia'          => Media::latest('created_at')->take(6)->get(),
        ]);
    }
}