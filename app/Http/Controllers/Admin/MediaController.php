<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMediaRequest;
use App\Http\Requests\Admin\UpdateMediaRequest;
use App\Models\Achievement;
use App\Models\Activity;
use App\Models\Facility;
use App\Models\Gallery;
use App\Models\IndustryPartner;
use App\Models\Media;
use App\Models\StudentWork;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $category = trim((string) $request->query('category', ''));

        $query = Media::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('original_name', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%")
                  ->orWhere('alt_text', 'like', "%{$search}%");
            });
        }

        if ($category !== '') {
            $query->where(function ($q) use ($category) {
                $q->where('path', 'like', "{$category}/%");

                $modelClass = match($category) {
                    'facilities'    => Facility::class,
                    'achievements'  => Achievement::class,
                    'student-works' => StudentWork::class,
                    'activities'    => Activity::class,
                    'partners'      => IndustryPartner::class,
                    default         => null
                };

                if ($modelClass) {
                    $galleryMediaIds = Gallery::where('galleryable_type', $modelClass)->pluck('media_id');
                    $q->orWhereIn('id', $galleryMediaIds);

                    if ($category === 'achievements') {
                        $q->orWhereIn('id', Achievement::whereNotNull('document_media_id')->pluck('document_media_id'));
                    } elseif ($category === 'facilities') {
                        $q->orWhereIn('id', Facility::whereNotNull('photo_media_id')->pluck('photo_media_id'));
                    } elseif ($category === 'activities') {
                        $q->orWhereIn('id', Activity::whereNotNull('cover_media_id')->pluck('cover_media_id'));
                    } elseif ($category === 'partners') {
                        $q->orWhereIn('id', IndustryPartner::whereNotNull('logo_media_id')->pluck('logo_media_id'));
                    }
                }
            });
        }

        $mediaPaginated = $query->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        $mediaIds = $mediaPaginated->pluck('id')->toArray();

        $galleryMap = Gallery::whereIn('media_id', $mediaIds)
            ->with('galleryable')
            ->get()
            ->keyBy('media_id');

        $activitiesCover   = Activity::whereIn('cover_media_id', $mediaIds)->get()->keyBy('cover_media_id');
        $achievementsCover = Achievement::whereIn('document_media_id', $mediaIds)->get()->keyBy('document_media_id');
        $facilitiesCover   = Facility::whereIn('photo_media_id', $mediaIds)->get()->keyBy('photo_media_id');
        $partnersLogo      = IndustryPartner::whereIn('logo_media_id', $mediaIds)->get()->keyBy('logo_media_id');

        $groupedItems = collect();
        $processedGroupKeys = [];

        $allGalleriesGrouped = Gallery::whereIn('media_id', $mediaIds)
            ->with('media')
            ->get()
            ->groupBy(fn ($g) => $g->galleryable_type . '_' . $g->galleryable_id);

        foreach ($mediaPaginated as $item) {
            $item->file_url = Storage::disk($item->disk ?? 'public')->url($item->path);

            $gallery = $galleryMap->get($item->id);
            $groupKey = null;

            if ($gallery && $gallery->galleryable) {
                $groupKey = get_class($gallery->galleryable) . '_' . $gallery->galleryable_id;
            } elseif ($act = $activitiesCover->get($item->id)) {
                $groupKey = Activity::class . '_' . $act->id;
            } elseif ($ach = $achievementsCover->get($item->id)) {
                $groupKey = Achievement::class . '_' . $ach->id;
            } elseif ($fac = $facilitiesCover->get($item->id)) {
                $groupKey = Facility::class . '_' . $fac->id;
            } elseif ($ptr = $partnersLogo->get($item->id)) {
                $groupKey = IndustryPartner::class . '_' . $ptr->id;
            }

            if ($groupKey && in_array($groupKey, $processedGroupKeys, true)) {
                continue;
            }

            if ($groupKey) {
                $processedGroupKeys[] = $groupKey;

                [$entityType, $entityId] = explode('_', $groupKey, 2);
                $entityModel = $gallery ? $gallery->galleryable : ($activitiesCover->get($item->id) ?? $achievementsCover->get($item->id) ?? $facilitiesCover->get($item->id) ?? $partnersLogo->get($item->id));
                
                $galleryMedia = ($allGalleriesGrouped->get($groupKey) ?? collect())
                    ->pluck('media')
                    ->filter();

                if (isset($entityModel->cover_media_id) && $entityModel->cover_media_id) {
                    $coverMedia = Media::find($entityModel->cover_media_id);
                    if ($coverMedia) { $galleryMedia->prepend($coverMedia); }
                } elseif (isset($entityModel->document_media_id) && $entityModel->document_media_id) {
                    $coverMedia = Media::find($entityModel->document_media_id);
                    if ($coverMedia) { $galleryMedia->prepend($coverMedia); }
                } elseif (isset($entityModel->photo_media_id) && $entityModel->photo_media_id) {
                    $coverMedia = Media::find($entityModel->photo_media_id);
                    if ($coverMedia) { $galleryMedia->prepend($coverMedia); }
                } elseif (isset($entityModel->logo_media_id) && $entityModel->logo_media_id) {
                    $coverMedia = Media::find($entityModel->logo_media_id);
                    if ($coverMedia) { $galleryMedia->prepend($coverMedia); }
                }

                $albumMedia = $galleryMedia->unique('id')->values()->map(function ($m) {
                    $m->file_url = Storage::disk($m->disk ?? 'public')->url($m->path);
                    return $m;
                });

                if ($albumMedia->isEmpty()) {
                    $albumMedia = collect([$item]);
                }

                $title = $entityModel->name ?? $entityModel->title ?? $item->original_name;

                $folderCategory = match($entityType) {
                    Achievement::class     => 'achievements',
                    Activity::class        => 'activities',
                    StudentWork::class     => 'student-works',
                    Facility::class        => 'facilities',
                    IndustryPartner::class => 'partners',
                    default                => explode('/', $item->path)[0] ?? 'media'
                };

                $groupedItems->push((object) [
                    'is_album'    => $albumMedia->count() > 1,
                    'group_key'   => $groupKey,
                    'cover'       => $albumMedia->first() ?? $item,
                    'title'       => $title,
                    'total_count' => $albumMedia->count(),
                    'all_media'   => $albumMedia,
                    'main_media'  => $item,
                    'folder'      => $folderCategory,
                ]);
            } else {
                $groupedItems->push((object) [
                    'is_album'    => false,
                    'group_key'   => 'single_' . $item->id,
                    'cover'       => $item,
                    'title'       => $item->original_name,
                    'total_count' => 1,
                    'all_media'   => collect([$item]),
                    'main_media'  => $item,
                    'folder'      => explode('/', $item->path)[0] ?? 'media',
                ]);
            }
        }

        return view('admin.media.index', [
            'media'        => $mediaPaginated,
            'groupedItems' => $groupedItems,
            'search'       => $search,
            'category'     => $category,
        ]);
    }

    public function create(): View
    {
        return view('admin.media.create');
    }

    public function store(StoreMediaRequest $request): RedirectResponse
    {
        $file = $request->file('file');
        $path = $file->store('media', 'public');

        Media::create([
            'original_name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'file_name'     => basename($path),
            'disk'          => 'public',
            'path'          => $path,
            'mime_type'     => $file->getMimeType(),
            'size'          => $file->getSize(),
            'alt_text'      => $request->validated('alt_text'),
            'created_by'    => auth()->id(),
        ]);

        return redirect()
            ->route('admin.media.index')
            ->with('success', 'Media berhasil diunggah.');
    }

    public function edit(Media $medium): View
    {
        $media = $medium;

        $gallery = Gallery::where('media_id', $media->id)->with('galleryable')->first();
        $relatedEntity = $gallery ? $gallery->galleryable : null;

        if (!$relatedEntity) {
            $relatedEntity = Activity::where('cover_media_id', $media->id)->first()
                ?? Facility::where('photo_media_id', $media->id)->first()
                ?? Achievement::where('document_media_id', $media->id)->first()
                ?? IndustryPartner::where('logo_media_id', $media->id)->first();
        }

        $albumMedia = collect([$media]);

        if ($relatedEntity) {
            $galleryMedia = Gallery::where('galleryable_type', get_class($relatedEntity))
                ->where('galleryable_id', $relatedEntity->id)
                ->with('media')
                ->get()
                ->pluck('media')
                ->filter();

            if ($galleryMedia->isNotEmpty()) {
                if (isset($relatedEntity->cover_media_id) && $relatedEntity->cover_media_id) {
                    $coverMedia = Media::find($relatedEntity->cover_media_id);
                    if ($coverMedia) { $galleryMedia->prepend($coverMedia); }
                } elseif (isset($relatedEntity->photo_media_id) && $relatedEntity->photo_media_id) {
                    $coverMedia = Media::find($relatedEntity->photo_media_id);
                    if ($coverMedia) { $galleryMedia->prepend($coverMedia); }
                } elseif (isset($relatedEntity->logo_media_id) && $relatedEntity->logo_media_id) {
                    $coverMedia = Media::find($relatedEntity->logo_media_id);
                    if ($coverMedia) { $galleryMedia->prepend($coverMedia); }
                }

                $albumMedia = $galleryMedia->unique('id')->values();
            }
        }

        $description = $media->alt_text;

        return view('admin.media.edit', [
            'media'         => $media,
            'albumMedia'    => $albumMedia,
            'relatedEntity' => $relatedEntity,
            'description'   => $description,
        ]);
    }

    public function update(UpdateMediaRequest $request, Media $medium): RedirectResponse
    {
        $media = $medium;
        $newAltText = $request->validated('alt_text');

        DB::transaction(function () use ($request, $media, $newAltText) {
            $data = [
                'alt_text'   => $newAltText,
                'updated_by' => auth()->id(),
            ];

            if ($request->hasFile('file')) {
                $disk = $media->disk ?? 'public';

                if ($media->path && Storage::disk($disk)->exists($media->path)) {
                    Storage::disk($disk)->delete($media->path);
                }

                $file = $request->file('file');
                $path = $file->store('media', $disk);

                $data['original_name'] = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $data['file_name']     = basename($path);
                $data['path']          = $path;
                $data['mime_type']     = $file->getMimeType();
                $data['size']          = $file->getSize();
            }

            $media->update($data);

            $galleries = Gallery::where('media_id', $media->id)->get();
            foreach ($galleries as $gal) {
                if ($gal->galleryable_type && $gal->galleryable_id) {
                    $siblingMediaIds = Gallery::where('galleryable_type', $gal->galleryable_type)
                        ->where('galleryable_id', $gal->galleryable_id)
                        ->pluck('media_id');

                    Media::whereIn('id', $siblingMediaIds)->update([
                        'alt_text'   => $newAltText,
                        'updated_by' => auth()->id(),
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.media.index')
            ->with('success', 'Informasi media berhasil diperbarui.');
    }

    public function destroy(Media $medium): RedirectResponse
    {
        $media = $medium;

        DB::transaction(function () use ($media) {
            $this->detachAndDeleteMedia($media);
        });

        return redirect()
            ->route('admin.media.index')
            ->with('success', 'Media berhasil dihapus.');
    }

    public function destroyGroup(Request $request): RedirectResponse
    {
        $mediaIds = $request->input('media_ids', []);

        if (is_array($mediaIds) && count($mediaIds) > 0) {
            DB::transaction(function () use ($mediaIds) {
                $medias = Media::whereIn('id', $mediaIds)->get();

                foreach ($medias as $media) {
                    $this->detachAndDeleteMedia($media);
                }
            });

            return redirect()
                ->route('admin.media.index')
                ->with('success', count($mediaIds) . ' media berhasil dihapus.');
        }

        return redirect()->route('admin.media.index');
    }

    private function detachAndDeleteMedia(Media $media): void
    {
        $mediaId = $media->id;

        $galleryItems = Gallery::where('media_id', $mediaId)->get();
        foreach ($galleryItems as $gallery) {
            $galleryable = $gallery->galleryable;
            $gallery->delete();

            if ($galleryable && isset($galleryable->cover_media_id) && $galleryable->cover_media_id == $mediaId) {
                $nextGallery = Gallery::where('galleryable_type', get_class($galleryable))
                    ->where('galleryable_id', $galleryable->id)
                    ->first();

                $galleryable->update(['cover_media_id' => $nextGallery ? $nextGallery->media_id : null]);
            }
        }

        Activity::where('cover_media_id', $mediaId)->update(['cover_media_id' => null]);
        Facility::where('photo_media_id', $mediaId)->update(['photo_media_id' => null]);
        Achievement::where('document_media_id', $mediaId)->update(['document_media_id' => null]);
        IndustryPartner::where('logo_media_id', $mediaId)->update(['logo_media_id' => null]);

        $disk = $media->disk ?? 'public';
        if ($media->path && Storage::disk($disk)->exists($media->path)) {
            Storage::disk($disk)->delete($media->path);
        }

        $media->forceDelete();
    }
}