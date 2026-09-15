<?php

namespace App\Http\Controllers\Guru;

use App\Enums\PublishStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreActivityRequest;
use App\Http\Requests\Admin\UpdateActivityRequest;
use App\Models\Activity;
use App\Models\Gallery;
use App\Models\Media;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $userId = auth()->id();

        $activities = Activity::with(['cover', 'galleries.media'])
            ->where('created_by', $userId)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));
                $query->where('title', 'like', "%{$search}%");
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->orderByDesc('event_date')
            ->paginate(10)
            ->withQueryString();

        return view('guru.kegiatan.index', compact('activities'));
    }

    public function create(): View
    {
        return view('guru.kegiatan.create');
    }

    public function store(StoreActivityRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $data) {
            $title = $data['title'];
            $content = $data['content'] ?? null;

            $coverMediaId = $this->storeCoverIfPresent($request->file('cover'), $title);
            $imagesToStore = $request->hasFile('images') ? array_slice($request->file('images'), 0, 5) : [];
            $galleryMediaIds = $this->storeGalleryImages($imagesToStore, $title);

            if (! $coverMediaId && ! empty($galleryMediaIds)) {
                $coverMediaId = array_shift($galleryMediaIds);
            }

            $rawStatus = $data['status'] ?? '';
            $status = $rawStatus instanceof PublishStatus 
                ? $rawStatus 
                : (PublishStatus::tryFrom((string)$rawStatus) ?? PublishStatus::Draft);

            $activity = Activity::create([
                'title'          => $title,
                'slug'           => $this->generateUniqueSlug($title),
                'event_date'     => $data['event_date'],
                'content'        => $content,
                'cover_media_id' => $coverMediaId,
                'status'         => $status,
                'published_at'   => $status === PublishStatus::Published ? now() : null,
                'created_by'     => auth()->id(),
                'updated_by'     => auth()->id(),
            ]);

            foreach ($galleryMediaIds as $index => $mediaId) {
                Gallery::create([
                    'media_id'         => $mediaId,
                    'galleryable_id'   => $activity->id,
                    'galleryable_type' => Activity::class,
                    'sort_order'       => $index + 1,
                ]);
            }

            if (class_exists(ActivityLogger::class)) {
                app(ActivityLogger::class)->log(
                    action: 'create_activity_guru',
                    description: "Guru mencatat laporan kegiatan: \"{$activity->title}\"",
                    subject: $activity
                );
            }
        });

        return redirect()->route('guru.kegiatan.index')->with('success', 'Laporan kegiatan berhasil disimpan!');
    }

    public function edit(Activity $activity): View
    {
        $this->authorizeAccess($activity);
        $activity->load(['cover', 'galleries.media']);

        return view('guru.kegiatan.edit', compact('activity'));
    }

    public function update(UpdateActivityRequest $request, Activity $activity): RedirectResponse
    {
        $this->authorizeAccess($activity);
        $data = $request->validated();

        $removeIds = $data['remove_gallery_ids'] ?? [];
        $filesToDelete = [];

        DB::transaction(function () use ($request, $data, $removeIds, $activity, &$filesToDelete) {
            $title = $data['title'];
            $content = $data['content'] ?? null;

            $newCoverMediaId = $this->storeCoverIfPresent($request->file('cover'), $title);

            $galleryMediaIds = [];
            if ($request->hasFile('images')) {
                $currentCount = $activity->galleries()->whereNotIn('id', (array) $removeIds)->count();
                $allowedSlots = max(0, 5 - $currentCount);
                $filesToStore = array_slice($request->file('images'), 0, $allowedSlots);
                if (! empty($filesToStore)) {
                    $galleryMediaIds = $this->storeGalleryImages($filesToStore, $title);
                }
            }

            $rawStatus = $data['status'] ?? '';
            $status = $rawStatus instanceof PublishStatus 
                ? $rawStatus 
                : (PublishStatus::tryFrom((string)$rawStatus) ?? $activity->status);

            $updateData = [
                'title'      => $title,
                'slug'       => $this->generateUniqueSlug($title, $activity->id),
                'event_date' => $data['event_date'],
                'content'    => $content,
                'status'     => $status,
                'updated_by' => auth()->id(),
            ];

            if ($status === PublishStatus::Published && $activity->published_at === null) {
                $updateData['published_at'] = now();
            }

            if ($newCoverMediaId !== null) {
                if ($activity->cover_media_id) {
                    $oldMedia = Media::find($activity->cover_media_id);
                    if ($oldMedia) {
                        $filesToDelete[] = ['disk' => $oldMedia->disk, 'path' => $oldMedia->path];
                        $oldMedia->forceDelete();
                    }
                }
                $updateData['cover_media_id'] = $newCoverMediaId;
            }

            $activity->update($updateData);

            $this->syncGalleries($activity, $galleryMediaIds, $removeIds, $filesToDelete);

            $activity->unsetRelation('galleries');
            if (! $activity->cover_media_id && $activity->galleries()->exists()) {
                $firstGallery = $activity->galleries()->with('media')->first();
                if ($firstGallery && $firstGallery->media) {
                    $activity->update(['cover_media_id' => $firstGallery->media_id]);
                    $firstGallery->delete();
                }
            }

            if (class_exists(ActivityLogger::class)) {
                app(ActivityLogger::class)->log(
                    action: 'update_activity_guru',
                    description: "Guru memperbarui kegiatan: \"{$activity->title}\"",
                    subject: $activity
                );
            }
        });

        foreach ($filesToDelete as $file) {
            if (!empty($file['path'])) {
                Storage::disk($file['disk'] ?? 'public')->delete($file['path']);
            }
        }

        return redirect()->route('guru.kegiatan.index')->with('success', 'Laporan kegiatan berhasil diperbarui!');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        $this->authorizeAccess($activity);

        $filesToDelete = [];

        DB::transaction(function () use ($activity, &$filesToDelete) {
            if ($activity->cover) {
                $filesToDelete[] = ['disk' => $activity->cover->disk, 'path' => $activity->cover->path];
                $activity->cover->forceDelete();
            }

            $galleries = $activity->galleries()->with('media')->get();
            foreach ($galleries as $gallery) {
                if ($gallery->media) {
                    $filesToDelete[] = ['disk' => $gallery->media->disk, 'path' => $gallery->media->path];
                    $gallery->media->forceDelete();
                }
                $gallery->delete();
            }

            $activity->forceDelete();

            if (class_exists(ActivityLogger::class)) {
                app(ActivityLogger::class)->log(
                    action: 'delete_activity_guru',
                    description: "Guru menghapus kegiatan: \"{$activity->title}\""
                );
            }
        });

        foreach ($filesToDelete as $file) {
            if (!empty($file['path'])) {
                Storage::disk($file['disk'] ?? 'public')->delete($file['path']);
            }
        }

        return redirect()->route('guru.kegiatan.index')->with('success', 'Laporan kegiatan berhasil dihapus!');
    }

    protected function authorizeAccess(Activity $activity): void
    {
        $user = auth()->user();
        $isAdmin = (method_exists($user, 'hasRole') && $user->hasRole('admin')) || (strtolower($user->role ?? '') === 'admin');

        if ($activity->created_by !== $user->id && ! $isAdmin) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola laporan kegiatan ini.');
        }
    }

    protected function storeCoverIfPresent($cover, string $title): ?int
    {
        if (! $cover) return null;
        $path = $cover->store('activities/cover', 'public');
        $media = Media::create([
            'original_name' => $cover->getClientOriginalName(),
            'file_name'     => basename($path),
            'disk'          => 'public',
            'path'          => $path,
            'mime_type'     => $cover->getClientMimeType(),
            'size'          => $cover->getSize(),
            'alt_text'      => Str::limit($title, 250),
            'created_by'    => auth()->id(),
        ]);
        return $media->id;
    }

    protected function storeGalleryImages(array $images, string $title): array
    {
        $mediaIds = [];
        foreach (array_filter($images) as $index => $image) {
            $path = $image->store('activities/gallery', 'public');
            $media = Media::create([
                'original_name' => $image->getClientOriginalName(),
                'file_name'     => basename($path),
                'disk'          => 'public',
                'path'          => $path,
                'mime_type'     => $image->getClientMimeType(),
                'size'          => $image->getSize(),
                'alt_text'      => Str::limit("{$title} - Galeri " . ($index + 1), 250),
                'created_by'    => auth()->id(),
            ]);
            $mediaIds[] = $media->id;
        }
        return $mediaIds;
    }

    protected function syncGalleries(Activity $activity, array $newMediaIds, array $removeGalleryIds, array &$filesToDelete = []): void
    {
        if (! empty($removeGalleryIds)) {
            $galleriesToRemove = $activity->galleries()->whereIn('id', $removeGalleryIds)->with('media')->get();
            foreach ($galleriesToRemove as $gallery) {
                if ($gallery->media) {
                    $filesToDelete[] = ['disk' => $gallery->media->disk, 'path' => $gallery->media->path];
                    $gallery->media->forceDelete();
                }
                $gallery->delete();
            }
        }

        $nextOrder = (int) ($activity->galleries()->max('sort_order') ?? 0);
        $nextOrder++;

        foreach ($newMediaIds as $mediaId) {
            Gallery::create([
                'media_id'         => $mediaId,
                'galleryable_id'   => $activity->id,
                'galleryable_type' => Activity::class,
                'sort_order'       => $nextOrder,
            ]);
            $nextOrder++;
        }
    }

    protected function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $suffix = 1;

        while (
            Activity::withTrashed()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}