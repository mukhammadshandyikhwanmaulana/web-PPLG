<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PublishStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStudentWorkRequest;
use App\Http\Requests\Admin\UpdateStudentWorkRequest;
use App\Models\Gallery;
use App\Models\Media;
use App\Models\StaffMember;
use App\Models\StudentWork;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StudentWorkController extends Controller
{
    public function index(Request $request): View
    {
        $query = StudentWork::query()->with([
            'supervisor', 
            'cover',
            'creator',
            'galleries' => function ($q) {
                $q->where('is_cover', false)->with('media');
            }
        ]);

        if ($search = trim((string) $request->query('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('contributor_name', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($request->filled('featured')) {
            $query->where('is_featured', $request->boolean('featured'));
        }

        $supervisorId = $request->query('supervisor_id') ?? $request->query('pembimbing_id');
        if ($supervisorId) {
            $query->where('supervisor_id', $supervisorId);
        }

        $studentWorks = $query
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->orderBy('title')
            ->paginate(15)
            ->withQueryString();

        $supervisors = StaffMember::query()
            ->active()
            ->when($supervisorId, fn($q) => $q->orWhere('id', $supervisorId))
            ->orderBy('name')
            ->get();

        return view('admin.karya-siswa.index', compact('studentWorks', 'supervisors'));
    }

    public function create(): View
    {
        $supervisors = StaffMember::query()
            ->active()
            ->orderBy('name')
            ->get();

        $studentWork = new StudentWork(); 

        return view('admin.karya-siswa.create', compact('supervisors', 'studentWork'));
    }

    public function store(StoreStudentWorkRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $data) {
            $title = $data['title'];
            $description = $data['description'] ?? null;

            $coverMediaId = $this->storeSingleImage($request->file('cover'), $title, $description, 'Cover');
            $mediaIds = $this->storeImages($request->file('images', []), $title, $description);
            $slug = $this->generateUniqueSlug($title);

            if (! $coverMediaId && ! empty($mediaIds)) {
                $coverMediaId = array_shift($mediaIds);
            }

            $statusValue = $data['status'] instanceof PublishStatus 
                ? $data['status']->value 
                : ($data['status'] ?? PublishStatus::Draft->value);

            $publishedAt = ($statusValue === PublishStatus::Published->value) ? now() : null;

            $studentWork = StudentWork::create([
                'user_id'          => auth()->id(),
                'title'            => $title,
                'slug'             => $slug,
                'description'      => $description,
                'contributor_name' => $data['contributor_name'] ?? null,
                'supervisor_id'    => $data['supervisor_id'] ?? null,
                'demo_url'         => $data['demo_url'] ?? null,
                'is_featured'      => $request->boolean('is_featured'),
                'cover_media_id'   => $coverMediaId,
                'status'           => $statusValue,
                'published_at'     => $publishedAt,
                'created_by'       => auth()->id(),
                'updated_by'       => auth()->id(),
            ]);

            $mediaIds = array_slice($mediaIds, 0, 5);

            foreach ($mediaIds as $index => $mediaId) {
                Gallery::create([
                    'media_id'         => $mediaId,
                    'galleryable_id'   => $studentWork->id,
                    'galleryable_type' => StudentWork::class,
                    'is_cover'         => false,
                    'sort_order'       => $index + 1,
                ]);
            }
        });

        return redirect()->route('admin.karya-siswa.index')->with('success', 'Karya Siswa berhasil ditambahkan.');
    }

    public function edit(StudentWork $studentWork): View
    {
        $studentWork->load(['cover', 'galleries.media', 'creator']);
        $supervisors = StaffMember::query()
            ->active()
            ->when($studentWork->supervisor_id, fn($q) => $q->orWhere('id', $studentWork->supervisor_id))
            ->orderBy('name')
            ->get();

        return view('admin.karya-siswa.edit', compact('studentWork', 'supervisors'));
    }

    public function update(UpdateStudentWorkRequest $request, StudentWork $studentWork): RedirectResponse
    {
        $data = $request->validated();
        $removeIds = $data['remove_gallery_ids'] ?? [];

        DB::transaction(function () use ($request, $data, $removeIds, $studentWork) {
            $title = $data['title'];
            $description = $data['description'] ?? null;

            $newCoverMediaId = $this->storeSingleImage($request->file('cover'), $title, $description, 'Cover');
            $mediaIds = $this->storeImages($request->file('images', []), $title, $description);

            $slug = $studentWork->slug;
            if (Str::slug($title) !== Str::slug($studentWork->title)) {
                $slug = $this->generateUniqueSlug($title, $studentWork->id);
            }

            $statusValue = $data['status'] instanceof PublishStatus 
                ? $data['status']->value 
                : ($data['status'] ?? (is_object($studentWork->status) ? $studentWork->status->value : $studentWork->status));

            $publishedAt = $studentWork->published_at;
            if ($statusValue === PublishStatus::Published->value && $publishedAt === null) {
                $publishedAt = now();
            }

            $updateData = [
                'title'            => $title,
                'slug'             => $slug,
                'description'      => $description,
                'contributor_name' => $data['contributor_name'] ?? null,
                'supervisor_id'    => $data['supervisor_id'] ?? null,
                'demo_url'         => $data['demo_url'] ?? null,
                'is_featured'      => $request->boolean('is_featured'),
                'status'           => $statusValue,
                'published_at'     => $publishedAt,
                'updated_by'       => auth()->id(),
            ];

            if ($newCoverMediaId) {
                if ($studentWork->cover_media_id) {
                    $oldMedia = Media::find($studentWork->cover_media_id);
                    if ($oldMedia) {
                        $oldMedia->forceDelete();
                    }
                }
                $updateData['cover_media_id'] = $newCoverMediaId;
            }

            $studentWork->update($updateData);

            $this->syncGalleries($studentWork, $mediaIds, $removeIds);

            if (! $studentWork->cover_media_id && $studentWork->galleries()->exists()) {
                $firstGallery = $studentWork->galleries()->first();
                if ($firstGallery && $firstGallery->media_id) {
                    $studentWork->update(['cover_media_id' => $firstGallery->media_id]);
                    $firstGallery->delete();
                }
            }

            $this->updateExistingMediaAltText($studentWork, $title, $description);
        });

        return redirect()->route('admin.karya-siswa.index')->with('success', 'Karya Siswa berhasil diperbarui.');
    }

    public function destroy(StudentWork $studentWork): RedirectResponse
    {
        DB::transaction(function () use ($studentWork) {
            $studentWork->load(['galleries.media', 'cover']);

            if ($studentWork->cover) {
                $studentWork->cover->forceDelete();
            }

            foreach ($studentWork->galleries as $gallery) {
                if ($gallery->media) {
                    $gallery->media->forceDelete();
                }
                $gallery->delete();
            }

            $studentWork->forceDelete();
        });

        return redirect()->route('admin.karya-siswa.index')->with('success', 'Karya Siswa berhasil dihapus permanen.');
    }

    protected function storeSingleImage($image, string $title, ?string $description = null, string $suffix = ''): ?int
    {
        if (! $image) return null;
        $path = $image->store('student-works/covers', 'public');
        $media = Media::create([
            'original_name' => $image->getClientOriginalName(),
            'file_name'     => basename($path),
            'disk'          => 'public',
            'path'          => $path,
            'mime_type'     => $image->getClientMimeType(),
            'size'          => $image->getSize(),
            'alt_text'      => $this->formatAltText($description, $title, $suffix),
            'created_by'    => auth()->id(),
        ]);
        return $media->id;
    }

    protected function storeImages(array $images, string $title, ?string $description = null): array
    {
        $mediaIds = [];
        foreach (array_filter($images) as $index => $image) {
            $path = $image->store('student-works/galleries', 'public');
            $media = Media::create([
                'original_name' => $image->getClientOriginalName(),
                'file_name'     => basename($path),
                'disk'          => 'public',
                'path'          => $path,
                'mime_type'     => $image->getClientMimeType(),
                'size'          => $image->getSize(),
                'alt_text'      => $this->formatAltText($description, $title, "Galeri " . ($index + 1)),
                'created_by'    => auth()->id(),
            ]);
            $mediaIds[] = $media->id;
        }
        return $mediaIds;
    }

    protected function syncGalleries(StudentWork $studentWork, array $newMediaIds, array $removeGalleryIds): void
    {
        if (! empty($removeGalleryIds)) {
            $galleriesToRemove = $studentWork->galleries()
                ->whereIn('id', $removeGalleryIds)
                ->with('media')
                ->get();

            foreach ($galleriesToRemove as $gallery) {
                if ($gallery->media) {
                    $gallery->media->forceDelete();
                }
                $gallery->delete();
            }
        }

        $nextOrder = (int) ($studentWork->galleries()->max('sort_order') ?? 0);
        $nextOrder++;

        foreach ($newMediaIds as $mediaId) {
            Gallery::create([
                'media_id'         => $mediaId,
                'galleryable_id'   => $studentWork->id,
                'galleryable_type' => StudentWork::class,
                'is_cover'         => false,
                'sort_order'       => $nextOrder,
            ]);
            $nextOrder++;
        }

        $allGalleries = $studentWork->galleries()->orderBy('sort_order')->get();
        if ($allGalleries->count() > 5) {
            $excessGalleries = $allGalleries->slice(5);
            foreach ($excessGalleries as $extra) {
                if ($extra->media) {
                    $extra->media->forceDelete();
                }
                $extra->delete();
            }
        }
    }

    protected function updateExistingMediaAltText(StudentWork $studentWork, string $title, ?string $description): void
    {
        $studentWork->load(['cover', 'galleries.media']);
        if ($studentWork->cover) {
            $studentWork->cover->update([
                'alt_text'   => $this->formatAltText($description, $title, 'Cover'),
                'updated_by' => auth()->id(),
            ]);
        }
        foreach ($studentWork->galleries as $index => $gallery) {
            if ($gallery->media) {
                $gallery->media->update([
                    'alt_text'   => $this->formatAltText($description, $title, "Galeri " . ($index + 1)),
                    'updated_by' => auth()->id(),
                ]);
            }
        }
    }

    protected function formatAltText(?string $description, string $title, string $suffix = ''): string
    {
        $cleanDesc = $description ? trim(strip_tags($description)) : '';
        $baseText = $cleanDesc !== '' ? $cleanDesc : $title;
        if ($suffix !== '') $baseText .= " ({$suffix})";
        return Str::limit($baseText, 250);
    }

    protected function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $suffix = 1;
        while (
            StudentWork::withTrashed()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()
        ) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }
        return $slug;
    }
}