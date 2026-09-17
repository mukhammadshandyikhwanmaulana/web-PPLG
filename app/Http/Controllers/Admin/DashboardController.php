<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Activity;
use App\Models\Facility;
use App\Models\IndustryPartner;
use App\Models\Media;
use App\Models\StudentWork;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $partnerCount = IndustryPartner::count();

        $guruCount = User::query()
            ->active()
            ->where(function ($q) {
                $guruRoleValue = UserRole::Guru->value;
                $q->whereHas('roles', fn ($r) => $r->where('name', $guruRoleValue))
                  ->orWhere('role', $guruRoleValue);
            })
            ->count();

        return view('admin.dashboard', [
            'guruCount'            => $guruCount,
            'facilityCount'        => Facility::count(),
            'achievementCount'     => Achievement::count(),
            'studentWorkCount'     => StudentWork::count(),
            'activityCount'        => Activity::count(),
            'partnerCount'         => $partnerCount,
            'industryPartnerCount' => $partnerCount,
            
            'mediaCount'           => Media::count(),
            'totalMediaSize'       => Media::sum('size') ?? 0,

            'recentActivities'     => Activity::with('cover')->latest('created_at')->take(5)->get(),
            'recentStudentWorks'   => StudentWork::with('cover')->latest('created_at')->take(5)->get(),
            'recentMedia'          => Media::latest('created_at')->take(6)->get(),
        ]);
    }
}