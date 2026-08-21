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
    protected function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->hasRole('admin'), 403);
    }

    public function index(Request $request): View
    {
        $this->authorizeAdmin();

        $query = StudentWork::query()
            ->with(['supervisor', 'galleries.media']);

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

        $supervisors = StaffMember::where(function ($q) use ($supervisorId) {
                $q->where('is_active', true);
                if ($supervisorId) {
                    $q->orWhere('id', $supervisorId);
                }
            })
            ->orderBy('name')
            ->get();

        return view('admin.karya-siswa.index', compact('studentWorks', 'supervisors'));
    }

    public function create(): View
    {
        $this->authorizeAdmin();

        $supervisors = StaffMember::where('is_active', true)->orderBy('name')->get();

        return view('admin.karya-siswa.create', compact('supervisors'));
    }

    public function store(StoreStudentWorkRequest $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validated();
        $mediaIds = $this->storeImages($request->file('images', []));

        DB::transaction(function () use ($data, $mediaIds) {
            $slug = $this->generateUniqueSlug($data['title']);

            $publishedAt = null;
            if ($data['status'] === PublishStatus::Published->value) {
                $publishedAt = now();
            }

            $studentWork = StudentWork::create([
                'title' => $data['title'],
                'slug' => $slug,
                'description' => $data['description'] ?? null,
                'contributor_name' => $data['contributor_name'] ?? null,
                'supervisor_id' => $data['supervisor_id'] ?? null,
                'demo_url' => $data['demo_url'] ?? null,
                'is_featured' => $data['is_featured'] ?? false,
                'status' => $data['status'],
                'published_at' => $publishedAt,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            foreach ($mediaIds as $index => $mediaId) {
                Gallery::create([
                    'media_id' => $mediaId,
                    'galleryable_id' => $studentWork->id,
                    'galleryable_type' => StudentWork::class,
                    'sort_order' => $index,
                ]);
            }
        });

        return redirect()
            ->route('admin.karya-siswa.index')
            ->with('success', 'Karya Siswa berhasil ditambahkan.');
    }

    public function edit(StudentWork $student_work): View
    {
        $this->authorizeAdmin();

        $student_work->load(['galleries.media']);

        $supervisors = StaffMember::where(function ($q) use ($student_work) {
                $q->where('is_active', true);
                if ($student_work->supervisor_id) {
                    $q->orWhere('id', $student_work->supervisor_id);
                }
            })
            ->orderBy('name')
            ->get();

        return view('admin.karya-siswa.edit', [
            'studentWork' => $student_work,
            'supervisors' => $supervisors,
        ]);
    }

    public function update(UpdateStudentWorkRequest $request, StudentWork $student_work): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validated();
        $mediaIds = $this->storeImages($request->file('images', []));
        $removeIds = $data['remove_gallery_ids'] ?? [];

        DB::transaction(function () use ($data, $mediaIds, $removeIds, $student_work) {
            $slug = $student_work->slug;
            if (Str::slug($data['title']) !== Str::slug($student_work->title)) {
                $slug = $this->generateUniqueSlug($data['title'], $student_work->id);
            }

            $publishedAt = $student_work->published_at;
            if ($data['status'] === PublishStatus::Published->value && $publishedAt === null) {
                $publishedAt = now();
            }

            $student_work->update([
                'title' => $data['title'],
                'slug' => $slug,
                'description' => $data['description'] ?? null,
                'contributor_name' => $data['contributor_name'] ?? null,
                'supervisor_id' => $data['supervisor_id'] ?? null,
                'demo_url' => $data['demo_url'] ?? null,
                'is_featured' => $data['is_featured'] ?? false,
                'status' => $data['status'],
                'published_at' => $publishedAt,
                'updated_by' => auth()->id(),
            ]);

            $this->syncGalleries($student_work, $mediaIds, $removeIds);
        });

        return redirect()
            ->route('admin.karya-siswa.index')
            ->with('success', 'Karya Siswa berhasil diperbarui.');
    }

    public function destroy(StudentWork $student_work): RedirectResponse
    {
        $this->authorizeAdmin();

        $student_work->delete();

        return redirect()
            ->route('admin.karya-siswa.index')
            ->with('success', 'Karya Siswa berhasil dihapus.');
    }

    protected function storeImages(array $images): array
    {
        $mediaIds = [];

        foreach (array_filter($images) as $image) {
            $path = $image->store('student-works', 'public');

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

    protected function syncGalleries(StudentWork $studentWork, array $newMediaIds, array $removeIds): void
    {
        if (! empty($removeIds)) {
            $studentWork->galleries()
                ->whereIn('id', $removeIds)
                ->delete();
        }

        $nextOrder = $studentWork->galleries()->max('sort_order');
        $nextOrder = $nextOrder === null ? 0 : $nextOrder + 1;

        foreach ($newMediaIds as $mediaId) {
            Gallery::create([
                'media_id' => $mediaId,
                'galleryable_id' => $studentWork->id,
                'galleryable_type' => StudentWork::class,
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
            StudentWork::withTrashed()
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