<?php

namespace App\Http\Controllers\Admin;

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
use Illuminate\Support\Str;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $query = Activity::query()->with(['cover', 'galleries.media', 'creator']);

        if ($search = trim((string) $request->query('search'))) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $activities = $query
            ->orderByDesc('event_date')
            ->orderBy('title')
            ->paginate(15)
            ->withQueryString();

        return view('admin.kegiatan.index', compact('activities'));
    }

    public function create(): View
    {
        return view('admin.kegiatan.create');
    }

    public function store(StoreActivityRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $data) {
            $title = $data['title'];
            $content = $data['content'] ?? null;

            $coverMediaId = $this->storeCoverIfPresent($request->file('cover'), $title, $content);
            $galleryMediaIds = $this->storeGalleryImages($request->file('images', []), $title, $content);
            $slug = $this->generateUniqueSlug($title);

            if (! $coverMediaId && ! empty($galleryMediaIds)) {
                $coverMediaId = array_shift($galleryMediaIds);
            }

            $statusValue = $data['status'] instanceof PublishStatus 
                ? $data['status']->value 
                : ($data['status'] ?? PublishStatus::Draft->value);

            $publishedAt = ($statusValue === PublishStatus::Published->value) ? now() : null;

            $activity = Activity::create([
                'user_id'        => auth()->id(),
                'title'          => $title,
                'slug'           => $slug,
                'event_date'     => $data['event_date'],
                'content'        => $content,
                'cover_media_id' => $coverMediaId,
                'status'         => $statusValue,
                'published_at'   => $publishedAt,
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
                    action: 'create_activity',
                    description: "Menambahkan kegiatan baru: \"{$activity->title}\"",
                    subject: $activity
                );
            }
        });

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function edit(Activity $activity): View
    {
        $activity->load(['cover', 'galleries.media', 'creator']);
        return view('admin.kegiatan.edit', compact('activity'));
    }

    public function update(UpdateActivityRequest $request, Activity $activity): RedirectResponse
    {
        $data = $request->validated();
        $removeIds = $data['remove_gallery_ids'] ?? [];

        DB::transaction(function () use ($request, $data, $removeIds, $activity) {
            $title = $data['title'];
            $content = $data['content'] ?? null;

            $newCoverMediaId = $this->storeCoverIfPresent($request->file('cover'), $title, $content);
            $galleryMediaIds = $this->storeGalleryImages($request->file('images', []), $title, $content);

            $slug = $activity->slug;
            if (Str::slug($title) !== Str::slug($activity->title)) {
                $slug = $this->generateUniqueSlug($title, $activity->id);
            }

            $statusValue = $data['status'] instanceof PublishStatus 
                ? $data['status']->value 
                : ($data['status'] ?? (is_object($activity->status) ? $activity->status->value : $activity->status));

            $publishedAt = $activity->published_at;
            if ($statusValue === PublishStatus::Published->value && $publishedAt === null) {
                $publishedAt = now();
            }

            $updateData = [
                'title'        => $title,
                'slug'         => $slug,
                'event_date'   => $data['event_date'],
                'content'      => $content,
                'status'       => $statusValue,
                'published_at' => $publishedAt,
                'updated_by'   => auth()->id(),
            ];

            if ($newCoverMediaId !== null) {
                if ($activity->cover_media_id) {
                    $oldMedia = Media::find($activity->cover_media_id);
                    if ($oldMedia) {
                        $oldMedia->forceDelete();
                    }
                }
                $updateData['cover_media_id'] = $newCoverMediaId;
            }

            $activity->update($updateData);

            $this->syncGalleries($activity, $galleryMediaIds, $removeIds);

            $activity->unsetRelation('galleries');
            if (! $activity->cover_media_id && $activity->galleries()->exists()) {
                $firstGallery = $activity->galleries()->with('media')->first();
                if ($firstGallery && $firstGallery->media) {
                    $activity->update(['cover_media_id' => $firstGallery->media_id]);
                    $firstGallery->delete();
                }
            }

            $this->updateExistingMediaAltText($activity, $title, $content);

            if (class_exists(ActivityLogger::class)) {
                app(ActivityLogger::class)->log(
                    action: 'update_activity',
                    description: "Memperbarui kegiatan: \"{$activity->title}\"",
                    subject: $activity
                );
            }
        });

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        $activityTitle = $activity->title;

        DB::transaction(function () use ($activity, $activityTitle) {
            if ($activity->cover) {
                $activity->cover->forceDelete();
            }

            $galleries = $activity->galleries()->with('media')->get();
            foreach ($galleries as $gallery) {
                if ($gallery->media) {
                    $gallery->media->forceDelete();
                }
                $gallery->delete();
            }

            $activity->forceDelete();

            if (class_exists(ActivityLogger::class)) {
                app(ActivityLogger::class)->log(
                    action: 'delete_activity',
                    description: "Menghapus kegiatan secara permanen: \"{$activityTitle}\""
                );
            }
        });

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil dihapus secara permanen beserta berkas medianya.');
    }

    protected function storeCoverIfPresent($cover, string $title, ?string $content = null): ?int
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
            'alt_text'      => $this->formatAltText($content, $title, 'Cover'),
            'created_by'    => auth()->id(),
        ]);
        return $media->id;
    }

    protected function storeGalleryImages(array $images, string $title, ?string $content = null): array
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
                'alt_text'      => $this->formatAltText($content, $title, "Galeri " . ($index + 1)),
                'created_by'    => auth()->id(),
            ]);
            $mediaIds[] = $media->id;
        }
        return $mediaIds;
    }

    protected function syncGalleries(Activity $activity, array $newMediaIds, array $removeGalleryIds): void
    {
        if (! empty($removeGalleryIds)) {
            $galleriesToRemove = $activity->galleries()->whereIn('id', $removeGalleryIds)->with('media')->get();
            foreach ($galleriesToRemove as $gallery) {
                if ($gallery->media) {
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

    protected function updateExistingMediaAltText(Activity $activity, string $title, ?string $content): void
    {
        $activity->load(['cover', 'galleries.media']);
        if ($activity->cover) {
            $activity->cover->update([
                'alt_text'   => $this->formatAltText($content, $title, 'Cover'),
                'updated_by' => auth()->id(),
            ]);
        }
        foreach ($activity->galleries as $index => $gallery) {
            if ($gallery->media) {
                $gallery->media->update([
                    'alt_text'   => $this->formatAltText($content, $title, "Galeri " . ($index + 1)),
                    'updated_by' => auth()->id(),
                ]);
            }
        }
    }

    protected function formatAltText(?string $content, string $title, string $suffix = ''): string
    {
        $cleanContent = $content ? trim(strip_tags($content)) : '';
        $baseText = $cleanContent !== '' ? $cleanContent : $title;
        if ($suffix !== '') $baseText .= " ({$suffix})";
        return Str::limit($baseText, 250);
    }

    protected function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $suffix = 1;

        while (
            Activity::withTrashed()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }
        return $slug;
    }
}