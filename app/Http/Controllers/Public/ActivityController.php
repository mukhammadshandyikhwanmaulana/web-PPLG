<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    /**
     * Menampilkan daftar seluruh kegiatan publik
     */
    public function index(Request $request): View
    {
        $query = Activity::with(['cover', 'galleries.media'])->published();

        // Fitur pencarian kata kunci aman
        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $activities = $query->orderByDesc('event_date')
            ->orderByDesc('created_at')
            ->paginate(9)
            ->withQueryString();

        return view('public.activities.index', [
            'activities' => $activities,
            'search'     => $search,
        ]);
    }

    /**
     * Menampilkan detail satu kegiatan berdasarkan slug
     */
    public function show(string $slug): View
    {
        $activity = Activity::with(['cover', 'galleries.media', 'creator'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Kegiatan terbaru lainnya sebagai rekomendasi bacaan
        $recentActivities = Activity::published()
            ->where('id', '!=', $activity->id)
            ->orderByDesc('event_date')
            ->take(4)
            ->get();

        return view('public.activities.show', [
            'activity'         => $activity,
            'recentActivities' => $recentActivities,
        ]);
    }
}