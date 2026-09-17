<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\StudentWork;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentWorkController extends Controller
{
    /**
     * Menampilkan daftar karya siswa publik
     */
    public function index(Request $request): View
    {
        $query = StudentWork::with(['cover', 'supervisor'])->published();

        // Fitur pencarian kata kunci
        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('contributor_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $studentWorks = $query->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(9)
            ->withQueryString();

        return view('public.student-works.index', [
            'studentWorks' => $studentWorks,
            'search'       => $search,
        ]);
    }

    /**
     * Menampilkan detail karya siswa berdasarkan slug
     */
    public function show(string $slug): View
    {
        $studentWork = StudentWork::with(['cover', 'supervisor', 'galleries.media', 'creator'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Rekomendasi karya siswa lainnya (ditambahkan with(['cover', 'galleries.media']))
        $otherWorks = StudentWork::with(['cover', 'galleries.media'])
            ->published()
            ->where('id', '!=', $studentWork->id)
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        return view('public.student-works.show', [
            'studentWork' => $studentWork,
            'otherWorks'  => $otherWorks,
        ]);
    }
}