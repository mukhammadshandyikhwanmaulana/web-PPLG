<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Activity;
use App\Models\StudentWork;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $userId = auth()->id();

        $studentWorkCount = StudentWork::where('created_by', $userId)->count();
        $activityCount    = Activity::where('created_by', $userId)->count();
        $achievementCount = Achievement::where('created_by', $userId)->count();

        $recentStudentWorks = StudentWork::with('cover')
            ->where('created_by', $userId)
            ->latest()
            ->take(5)
            ->get();

        $recentActivities = Activity::with('cover')
            ->where('created_by', $userId)
            ->latest()
            ->take(5)
            ->get();

        return view('guru.dashboard', compact(
            'studentWorkCount',
            'activityCount',
            'achievementCount',
            'recentStudentWorks',
            'recentActivities'
        ));
    }
}