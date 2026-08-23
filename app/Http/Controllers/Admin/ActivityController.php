<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PublishStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreActivityRequest;
use App\Http\Requests\Admin\UpdateActivityRequest;
use App\Models\Activity;
use App\Models\Gallery;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ActivityController extends Controller
{
    protected function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->hasRole('admin'), 403);
    }

    public function index(Request $request): View
    {
        $this->authorizeAdmin();

        $query = Activity::query()->with(['cover', 'galleries.media']);

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
        $this->authorizeAdmin();

        return view('admin.kegiatan.create');
    }

    public function store(StoreActivityRequest $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validated();
        $coverMediaId = $this->storeCoverIfPresent($request->file('cover'));
        $galleryMediaIds = $this->storeGalleryImages($request->file('images', []));

        DB::transaction(function () use ($data, $coverMediaId, $galleryMediaIds) {
            $slug = $this->generateUniqueSlug($data['title']);

            $publishedAt = null;
            if ($data['status'] === PublishStatus::Published->value) {
                $publishedAt = now();
            }

            $activity = Activity::create([
                'title' => $data['title'],
                'slug' => $slug,
                'event_date' => $data['event_date'],
                'content' => $data['content'] ?? null,
                'cover_media_id' => $coverMediaId,
                'status' => $data['status'],
                'published_at' => $publishedAt,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            foreach ($galleryMediaIds as $index => $mediaId) {
                Gallery::create([
                    'media_id' => $mediaId,
                    'galleryable_id' => $activity->id,
                    'galleryable_type' => Activity::class,
                    'sort_order' => $index,
                ]);
            }
        });

        return redirect()
            ->route('admin.kegiatan.index')
            ->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function edit(Activity $activity): View
    {
        $this->authorizeAdmin();

        $activity->load(['cover', 'galleries.media']);

        return view('admin.kegiatan.edit', compact('activity'));
    }

    public function update(UpdateActivityRequest $request, Activity $activity): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validated();
        $newCoverMediaId = $this->storeCoverIfPresent($request->file('cover'));
        $galleryMediaIds = $this->storeGalleryImages($request->file('images', []));
        $removeIds = $data['remove_gallery_ids'] ?? [];

        DB::transaction(function () use ($data, $newCoverMediaId, $galleryMediaIds, $removeIds, $activity) {
            $slug = $activity->slug;
            if (Str::slug($data['title']) !== Str::slug($activity->title)) {
                $slug = $this->generateUniqueSlug($data['title'], $activity->id);
            }

            $publishedAt = $activity->published_at;
            if ($data['status'] === PublishStatus::Published->value && $publishedAt === null) {
                $publishedAt = now();
            }

            $updateData = [
                'title' => $data['title'],
                'slug' => $slug,
                'event_date' => $data['event_date'],
                'content' => $data['content'] ?? null,
                'status' => $data['status'],
                'published_at' => $publishedAt,
                'updated_by' => auth()->id(),
            ];

            // Cover hanya diganti jika ada file baru diunggah
            if ($newCoverMediaId !== null) {
                $updateData['cover_media_id'] = $newCoverMediaId;
            }

            $activity->update($updateData);

            $this->syncGalleries($activity, $galleryMediaIds, $removeIds);
        });

        // LOGIKA DINAMIS REDIRECT:
        if (!empty($removeIds)) {
            // Jika user menghapus gambar, kembalikan ke halaman edit agar terlihat efek hapusnya
            return redirect()
                ->route('admin.kegiatan.edit', $activity)
                ->with('success', 'Gambar galeri berhasil dihapus dan perubahan disimpan.');
        }

        // Jika tidak ada gambar yang dihapus, langsung kembali ke halaman daftar kegiatan
        return redirect()
            ->route('admin.kegiatan.index')
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        $this->authorizeAdmin();

        $activity->delete();

        return redirect()
            ->route('admin.kegiatan.index')
            ->with('success', 'Kegiatan berhasil dihapus.');
    }

    protected function storeCoverIfPresent($cover): ?int
    {
        if (! $cover) {
            return null;
        }

        $path = $cover->store('activities/cover', 'public');

        $media = Media::create([
            'file_name' => $cover->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $cover->getClientMimeType(),
            'size' => $cover->getSize(),
            'uploaded_by' => auth()->id(),
        ]);

        return $media->id;
    }

    protected function storeGalleryImages(array $images): array
    {
        $mediaIds = [];

        foreach (array_filter($images) as $image) {
            $path = $image->store('activities/gallery', 'public');

            $media = Media::create([
                'file_name' => $image->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $image->getClientMimeType(),
                'size' => $image->getSize(),
                'uploaded_by' => auth()->id(),
            ]);

            $mediaIds[] = $media->id;
        }

        return $mediaIds;
    }

    protected function syncGalleries(Activity $activity, array $newMediaIds, array $removeIds): void
    {
        if (! empty($removeIds)) {
            $activity->galleries()
                ->whereIn('id', $removeIds)
                ->delete();
        }

        $nextOrder = $activity->galleries()->max('sort_order');
        $nextOrder = $nextOrder === null ? 0 : $nextOrder + 1;

        foreach ($newMediaIds as $mediaId) {
            Gallery::create([
                'media_id' => $mediaId,
                'galleryable_id' => $activity->id,
                'galleryable_type' => Activity::class,
                'sort_order' => $nextOrder,
            ]);
            $nextOrder++;
        }
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