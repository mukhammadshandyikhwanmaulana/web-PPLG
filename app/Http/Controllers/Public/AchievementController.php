<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AchievementController extends Controller
{
    /**
     * Menampilkan daftar prestasi publik
     */
    public function index(Request $request): View
    {
        $query = Achievement::with('document')->published();

        // Fitur pencarian kata kunci
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('contributor_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $achievements = $query->orderByDesc('achievement_date')
            ->orderByDesc('created_at')
            ->paginate(9)
            ->withQueryString();

        return view('public.achievements.index', [
            'achievements' => $achievements,
            'search'       => $search,
        ]);
    }

    /**
     * Menampilkan detail prestasi berdasarkan slug
     */
    public function show(string $slug): View
    {
        $achievement = Achievement::with(['document', 'user', 'creator'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Rekomendasi prestasi lainnya (ditambahkan with('document'))
        $otherAchievements = Achievement::with('document')
            ->published()
            ->where('id', '!=', $achievement->id)
            ->orderByDesc('achievement_date')
            ->take(3)
            ->get();

        return view('public.achievements.show', [
            'achievement'       => $achievement,
            'otherAchievements' => $otherAchievements,
        ]);
    }
}