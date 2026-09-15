<?php

namespace App\Http\Controllers\Guru;

use App\Enums\PublishStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStudentWorkRequest;
use App\Http\Requests\Admin\UpdateStudentWorkRequest;
use App\Models\Gallery;
use App\Models\Media;
use App\Models\StaffMember;
use App\Models\StudentWork;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StudentWorkController extends Controller
{
    public function index(Request $request): View
    {
        $userId = auth()->id();

        $studentWorks = StudentWork::with(['cover', 'galleries.media', 'supervisor'])
            ->where('created_by', $userId)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('contributor_name', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('guru.karya-siswa.index', compact('studentWorks'));
    }

    public function create(): View
    {
        return view('guru.karya-siswa.create');
    }

    public function store(StoreStudentWorkRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $data) {
            $mediaId = $this->storeCoverIfPresent($request, $data['title']);

            $rawStatus = $data['status'] ?? '';
            $status = $rawStatus instanceof PublishStatus 
                ? $rawStatus 
                : (PublishStatus::tryFrom((string)$rawStatus) ?? PublishStatus::Draft);

            // OTOMATISISASI: Cari ID StaffMember milik Guru yang sedang login
            $staffMember = StaffMember::where('user_id', auth()->id())->first();
            $supervisorId = $data['supervisor_id'] ?? $staffMember?->id;

            $studentWork = StudentWork::create([
                'title'            => $data['title'],
                'slug'             => $this->generateUniqueSlug($data['title']),
                'description'      => $data['description'] ?? null,
                'contributor_name' => $data['contributor_name'] ?? null,
                'supervisor_id'    => $supervisorId, // Otomatis terisi ID Guru
                'demo_url'         => $data['demo_url'] ?? null,
                'is_featured'      => $request->boolean('is_featured'),
                'cover_media_id'   => $mediaId,
                'status'           => $status,
                'published_at'     => $status === PublishStatus::Published ? now() : null,
                'created_by'       => auth()->id(),
                'updated_by'       => auth()->id(),
            ]);

            if ($request->hasFile('images')) {
                $this->storeGalleries(array_slice($request->file('images'), 0, 5), $studentWork);
            }

            if (class_exists(ActivityLogger::class)) {
                app(ActivityLogger::class)->log(
                    action: 'create_student_work_guru',
                    description: "Guru mempublikasikan karya siswa baru: \"{$studentWork->title}\"",
                    subject: $studentWork
                );
            }
        });

        return redirect()->route('guru.karya-siswa.index')->with('success', 'Karya Siswa berhasil disimpan!');
    }

    public function edit(StudentWork $studentWork): View
    {
        $this->authorizeAccess($studentWork);
        $studentWork->loadMissing(['cover', 'galleries.media', 'supervisor']);

        return view('guru.karya-siswa.edit', compact('studentWork'));
    }

    public function update(UpdateStudentWorkRequest $request, StudentWork $studentWork): RedirectResponse
    {
        $this->authorizeAccess($studentWork);
        $data = $request->validated();

        $oldMedia = null;

        DB::transaction(function () use ($request, $data, $studentWork, &$oldMedia) {
            $newMediaId = $this->storeCoverIfPresent($request, $data['title']);

            $rawStatus = $data['status'] ?? '';
            $status = $rawStatus instanceof PublishStatus 
                ? $rawStatus 
                : (PublishStatus::tryFrom((string)$rawStatus) ?? $studentWork->status);

            // OTOMATISISASI: Jika supervisor_id masih kosong pada data lama, otomatis isi dengan ID Guru
            $staffMember = StaffMember::where('user_id', auth()->id())->first();
            $supervisorId = $data['supervisor_id'] ?? $studentWork->supervisor_id ?? $staffMember?->id;

            $updateData = [
                'title'            => $data['title'],
                'slug'             => $this->generateUniqueSlug($data['title'], $studentWork->id),
                'description'      => $data['description'] ?? null,
                'contributor_name' => $data['contributor_name'] ?? null,
                'supervisor_id'    => $supervisorId,
                'demo_url'         => $data['demo_url'] ?? null,
                'is_featured'      => $request->boolean('is_featured'),
                'status'           => $status,
                'updated_by'       => auth()->id(),
            ];

            if ($status === PublishStatus::Published && $studentWork->published_at === null) {
                $updateData['published_at'] = now();
            }

            if ($newMediaId !== null) {
                if ($studentWork->cover_media_id) {
                    $oldMedia = Media::find($studentWork->cover_media_id);
                }
                $updateData['cover_media_id'] = $newMediaId;
            }

            $studentWork->update($updateData);

            if (! empty($data['remove_gallery_ids'])) {
                $galleriesToDelete = $studentWork->galleries()->whereIn('id', $data['remove_gallery_ids'])->get();
                foreach ($galleriesToDelete as $gallery) {
                    if ($gallery->media) {
                        Storage::disk($gallery->media->disk ?? 'public')->delete($gallery->media->path);
                        $gallery->media->forceDelete();
                    }
                    $gallery->delete();
                }
            }

            if ($request->hasFile('images')) {
                $currentCount = $studentWork->galleries()->count();
                $allowedSlots = max(0, 5 - $currentCount);
                $filesToStore = array_slice($request->file('images'), 0, $allowedSlots);

                if (! empty($filesToStore)) {
                    $this->storeGalleries($filesToStore, $studentWork);
                }
            }

            if (class_exists(ActivityLogger::class)) {
                app(ActivityLogger::class)->log(
                    action: 'update_student_work_guru',
                    description: "Guru memperbarui karya siswa: \"{$studentWork->title}\"",
                    subject: $studentWork
                );
            }
        });

        if ($oldMedia) {
            $isUsedElsewhere = StudentWork::withTrashed()
                ->where('cover_media_id', $oldMedia->id)
                ->where('id', '!=', $studentWork->id)
                ->exists();

            if (! $isUsedElsewhere) {
                Storage::disk($oldMedia->disk ?? 'public')->delete($oldMedia->path);
                $oldMedia->forceDelete();
            }
        }

        return redirect()->route('guru.karya-siswa.index')->with('success', 'Karya Siswa berhasil diperbarui!');
    }

    public function destroy(StudentWork $studentWork): RedirectResponse
    {
        $this->authorizeAccess($studentWork);

        $oldMedia = $studentWork->cover;

        DB::transaction(function () use ($studentWork) {
            foreach ($studentWork->galleries as $gallery) {
                if ($gallery->media) {
                    Storage::disk($gallery->media->disk ?? 'public')->delete($gallery->media->path);
                    $gallery->media->forceDelete();
                }
                $gallery->delete();
            }

            $studentWork->forceDelete();

            if (class_exists(ActivityLogger::class)) {
                app(ActivityLogger::class)->log(
                    action: 'delete_student_work_guru',
                    description: "Guru menghapus karya siswa: \"{$studentWork->title}\""
                );
            }
        });

        if ($oldMedia) {
            $isUsedElsewhere = StudentWork::withTrashed()->where('cover_media_id', $oldMedia->id)->exists();
            if (! $isUsedElsewhere) {
                Storage::disk($oldMedia->disk ?? 'public')->delete($oldMedia->path);
                $oldMedia->forceDelete();
            }
        }

        return redirect()->route('guru.karya-siswa.index')->with('success', 'Karya Siswa berhasil dihapus!');
    }

    protected function authorizeAccess(StudentWork $studentWork): void
    {
        $user = auth()->user();
        $isAdmin = (method_exists($user, 'hasRole') && $user->hasRole('admin')) || (strtolower($user->role ?? '') === 'admin');

        if ($studentWork->created_by !== $user->id && ! $isAdmin) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola karya siswa ini.');
        }
    }

    protected function storeCoverIfPresent(Request $request, string $title): ?int
    {
        if (! $request->hasFile('cover')) {
            return null;
        }

        $file = $request->file('cover');
        $path = $file->store('student-works/covers', 'public');

        $media = Media::create([
            'original_name' => $file->getClientOriginalName(),
            'file_name'     => basename($path),
            'disk'          => 'public',
            'path'          => $path,
            'mime_type'     => $file->getClientMimeType(),
            'size'          => $file->getSize(),
            'alt_text'      => Str::limit($title, 250),
            'created_by'    => auth()->id(),
        ]);

        return $media->id;
    }

    protected function storeGalleries(array $files, StudentWork $studentWork): void
    {
        $nextOrder = (int) ($studentWork->galleries()->max('sort_order') ?? 0) + 1;

        foreach ($files as $file) {
            $path = $file->store('student-works/galleries', 'public');

            $media = Media::create([
                'original_name' => $file->getClientOriginalName(),
                'file_name'     => basename($path),
                'disk'          => 'public',
                'path'          => $path,
                'mime_type'     => $file->getClientMimeType(),
                'size'          => $file->getSize(),
                'alt_text'      => Str::limit($studentWork->title, 250),
                'created_by'    => auth()->id(),
            ]);

            Gallery::create([
                'media_id'         => $media->id,
                'galleryable_id'   => $studentWork->id,
                'galleryable_type' => StudentWork::class,
                'is_cover'         => false,
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
            StudentWork::withTrashed()
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