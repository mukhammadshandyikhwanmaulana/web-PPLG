<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Activity;
use App\Models\Gallery;
use App\Models\StudentWork;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function __invoke(Request $request): View
    {
        $category = $request->query('category');
        $allItems = collect([]);

        // 1. Ambil Foto dari Kegiatan (Activity)
        if (empty($category) || $category === 'kegiatan') {
            $activities = Activity::with(['cover', 'galleries.media'])
                ->published()
                ->where(function ($q) {
                    $q->whereNotNull('cover_media_id')->orWhereHas('galleries');
                })
                ->get();

            foreach ($activities as $act) {
                // Masukkan Cover Utama
                if ($act->cover_url) {
                    $allItems->push([
                        'title'        => $act->title,
                        'photo_url'    => $act->cover_url,
                        'category'     => 'Kegiatan',
                        'detail_route' => route('public.activities.show', $act->slug ?? $act->id),
                        'created_at'   => $act->created_at,
                    ]);
                }
                // Masukkan Galeri Tambahan (jika ada)
                foreach ($act->galleries as $gal) {
                    if ($gal->media) {
                        $allItems->push([
                            'title'        => $gal->caption ?? $act->title,
                            'photo_url'    => $gal->media->url ?? (Storage::disk($gal->media->disk ?? 'public')->url($gal->media->path)),
                            'category'     => 'Kegiatan',
                            'detail_route' => route('public.activities.show', $act->slug ?? $act->id),
                            'created_at'   => $gal->created_at,
                        ]);
                    }
                }
            }
        }

        // 2. Ambil Foto dari Prestasi (Achievement)
        if (empty($category) || $category === 'prestasi') {
            $achievements = Achievement::with(['document', 'galleries.media'])
                ->published()
                ->where(function ($q) {
                    $q->whereNotNull('document_media_id')->orWhereHas('galleries');
                })
                ->get();

            foreach ($achievements as $ach) {
                if ($ach::class && $ach->document_url) {
                    // Cek jika dokumen adalah gambar (bukan PDF)
                    $isPdf = str_contains(strtolower($ach->document?->mime_type ?? ''), 'pdf') 
                          || str_ends_with(strtolower($ach->document_url), '.pdf');

                    if (! $isPdf) {
                        $allItems->push([
                            'title'        => $ach->title,
                            'photo_url'    => $ach->document_url,
                            'category'     => 'Prestasi',
                            'detail_route' => route('public.achievements.show', $ach->slug ?? $ach->id),
                            'created_at'   => $ach->created_at,
                        ]);
                    }
                }
            }
        }

        // 3. Ambil Foto dari Karya Siswa (StudentWork)
        if (empty($category) || $category === 'karya') {
            $studentWorks = StudentWork::with(['cover', 'galleries.media'])
                ->published()
                ->where(function ($q) {
                    $q->whereNotNull('cover_media_id')->orWhereHas('galleries');
                })
                ->get();

            foreach ($studentWorks as $work) {
                if ($work->cover_url) {
                    $allItems->push([
                        'title'        => $work->title,
                        'photo_url'    => $work->cover_url,
                        'category'     => 'Karya Siswa',
                        'detail_route' => route('public.student-works.show', $work->slug ?? $work->id),
                        'created_at'   => $work->created_at,
                    ]);
                }
            }
        }

        // Urutkan foto terbaru di atas
        $sortedItems = $allItems->sortByDesc('created_at')->values();

        // Manual Pagination untuk Collection
        $page = (int) $request->query('page', 1);
        $perPage = 12;
        $paginatedItems = new LengthAwarePaginator(
            $sortedItems->forPage($page, $perPage),
            $sortedItems->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('public.galleries.index', [
            'galleries'      => $paginatedItems,
            'activeCategory' => $category,
        ]);
    }
}