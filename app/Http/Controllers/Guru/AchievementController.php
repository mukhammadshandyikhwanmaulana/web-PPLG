<?php

namespace App\Http\Controllers\Guru;

use App\Enums\AchievementLevel;
use App\Enums\PublishStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAchievementRequest;
use App\Http\Requests\Admin\UpdateAchievementRequest;
use App\Models\Achievement;
use App\Models\Media;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AchievementController extends Controller
{
    public function index(Request $request): View
    {
        $userId = auth()->id();

        $achievements = Achievement::with('document')
            ->where('created_by', $userId)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('contributor_name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('level'), function ($query) use ($request) {
                $query->where('level', $request->input('level'));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->orderByDesc('achievement_date')
            ->paginate(10)
            ->withQueryString();

        return view('guru.prestasi.index', compact('achievements'));
    }

    public function create(): View
    {
        return view('guru.prestasi.create');
    }

    public function store(StoreAchievementRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $data = $request->validated();
            $mediaId = $this->storeDocumentIfPresent($request);

            $rawStatus = $data['status'] ?? '';
            $status = $rawStatus instanceof PublishStatus 
                ? $rawStatus 
                : (PublishStatus::tryFrom((string)$rawStatus) ?? PublishStatus::Draft);

            $rawLevel = $data['level'] ?? null;
            $level = $rawLevel instanceof AchievementLevel 
                ? $rawLevel 
                : ($rawLevel ? AchievementLevel::tryFrom((string)$rawLevel) : null);

            $achievement = Achievement::create([
                'title'            => $data['title'],
                'slug'             => $this->generateUniqueSlug($data['title']),
                'achievement_date' => $data['achievement_date'] ?? null,
                'level'            => $level,
                'contributor_name' => $data['contributor_name'] ?? null,
                'description'      => $data['description'] ?? null,
                'document_media_id' => $mediaId,
                'status'           => $status,
                'published_at'     => $status === PublishStatus::Published ? now() : null,
                'created_by'       => auth()->id(),
                'updated_by'       => auth()->id(),
            ]);

            if (class_exists(ActivityLogger::class)) {
                app(ActivityLogger::class)->log(
                    action: 'create_achievement_guru',
                    description: "Guru mencatat prestasi baru: \"{$achievement->title}\"",
                    subject: $achievement
                );
            }
        });

        return redirect()->route('guru.prestasi.index')->with('success', 'Data prestasi berhasil dicatat!');
    }

    public function edit(Achievement $achievement): View
    {
        $this->authorizeAccess($achievement);
        $achievement->loadMissing('document');

        return view('guru.prestasi.edit', compact('achievement'));
    }

    public function update(UpdateAchievementRequest $request, Achievement $achievement): RedirectResponse
    {
        $this->authorizeAccess($achievement);

        $oldMedia = null;

        DB::transaction(function () use ($request, $achievement, &$oldMedia) {
            $data = $request->validated();
            $newMediaId = $this->storeDocumentIfPresent($request);

            $rawStatus = $data['status'] ?? '';
            $status = $rawStatus instanceof PublishStatus 
                ? $rawStatus 
                : (PublishStatus::tryFrom((string)$rawStatus) ?? $achievement->status);

            $rawLevel = $data['level'] ?? null;
            $level = $rawLevel instanceof AchievementLevel 
                ? $rawLevel 
                : ($rawLevel ? AchievementLevel::tryFrom((string)$rawLevel) : $achievement->level);

            $updateData = [
                'title'            => $data['title'],
                'slug'             => $this->generateUniqueSlug($data['title'], $achievement->id),
                'achievement_date' => $data['achievement_date'] ?? null,
                'level'            => $level,
                'contributor_name' => $data['contributor_name'] ?? null,
                'description'      => $data['description'] ?? null,
                'status'           => $status,
                'updated_by'       => auth()->id(),
            ];

            if ($status === PublishStatus::Published && $achievement->published_at === null) {
                $updateData['published_at'] = now();
            }

            if ($newMediaId !== null) {
                if ($achievement->document_media_id) {
                    $oldMedia = Media::find($achievement->document_media_id);
                }
                $updateData['document_media_id'] = $newMediaId;
            } else {
                if ($achievement->document_media_id) {
                    $altText = $this->formatAltText($data['description'] ?? null, $data['title']);
                    Media::where('id', $achievement->document_media_id)->update([
                        'alt_text'   => $altText,
                        'updated_by' => auth()->id(),
                    ]);
                }
            }

            $achievement->update($updateData);

            if (class_exists(ActivityLogger::class)) {
                app(ActivityLogger::class)->log(
                    action: 'update_achievement_guru',
                    description: "Guru memperbarui prestasi: \"{$achievement->title}\"",
                    subject: $achievement
                );
            }
        });

        if ($oldMedia) {
            $isUsedElsewhere = Achievement::withTrashed()
                ->where('document_media_id', $oldMedia->id)
                ->where('id', '!=', $achievement->id)
                ->exists();

            if (! $isUsedElsewhere) {
                Storage::disk($oldMedia->disk ?? 'public')->delete($oldMedia->path);
                $oldMedia->forceDelete();
            }
        }

        return redirect()->route('guru.prestasi.index')->with('success', 'Data prestasi berhasil diperbarui!');
    }

    public function destroy(Achievement $achievement): RedirectResponse
    {
        $this->authorizeAccess($achievement);

        $oldMedia = $achievement->document;

        DB::transaction(function () use ($achievement) {
            $achievement->forceDelete();

            if (class_exists(ActivityLogger::class)) {
                app(ActivityLogger::class)->log(
                    action: 'delete_achievement_guru',
                    description: "Guru menghapus prestasi: \"{$achievement->title}\""
                );
            }
        });

        if ($oldMedia) {
            $isUsedElsewhere = Achievement::withTrashed()->where('document_media_id', $oldMedia->id)->exists();
            if (! $isUsedElsewhere) {
                Storage::disk($oldMedia->disk ?? 'public')->delete($oldMedia->path);
                $oldMedia->forceDelete();
            }
        }

        return redirect()->route('guru.prestasi.index')->with('success', 'Data prestasi berhasil dihapus!');
    }

    protected function authorizeAccess(Achievement $achievement): void
    {
        $user = auth()->user();
        $isAdmin = (method_exists($user, 'hasRole') && $user->hasRole('admin')) || (strtolower($user->role ?? '') === 'admin');

        if ($achievement->created_by !== $user->id && ! $isAdmin) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola data prestasi ini.');
        }
    }

    protected function storeDocumentIfPresent(Request $request): ?int
    {
        if (! $request->hasFile('document')) {
            return null;
        }

        $file = $request->file('document');
        $path = $file->store('achievements', 'public');
        $altText = $this->formatAltText($request->input('description'), $request->input('title'));

        $media = Media::create([
            'original_name' => $file->getClientOriginalName(),
            'file_name'     => basename($path),
            'disk'          => 'public',
            'path'          => $path,
            'mime_type'     => $file->getClientMimeType(),
            'size'          => $file->getSize(),
            'alt_text'      => $altText,
            'created_by'    => auth()->id(),
        ]);

        return $media->id;
    }

    protected function formatAltText(?string $description, string $title): string
    {
        $cleanDesc = $description ? trim(strip_tags($description)) : '';
        return $cleanDesc !== '' 
            ? Str::limit($cleanDesc, 250) 
            : Str::limit($title, 250);
    }

    protected function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $suffix = 1;

        while (
            Achievement::withTrashed()
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