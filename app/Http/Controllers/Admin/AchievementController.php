<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AchievementLevel;
use App\Enums\PublishStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAchievementRequest;
use App\Http\Requests\Admin\UpdateAchievementRequest;
use App\Models\Achievement;
use App\Models\Media;
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
        $achievements = Achievement::with('document')
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
            ->paginate(15)
            ->withQueryString();

        return view('admin.prestasi.index', compact('achievements'));
    }

    public function create(): View
    {
        return view('admin.prestasi.create');
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

            Achievement::create([
                'title'             => $data['title'],
                'slug'              => $this->generateUniqueSlug($data['title']),
                'achievement_date'  => $data['achievement_date'] ?? null,
                'level'             => $level,
                'contributor_name'  => $data['contributor_name'] ?? null,
                'description'       => $data['description'] ?? null,
                'document_media_id' => $mediaId,
                'status'            => $status,
                'published_at'      => $status === PublishStatus::Published ? now() : null,
                'created_by'        => auth()->id(),
                'updated_by'        => auth()->id(),
            ]);
        });

        return redirect()->route('admin.prestasi.index')->with('success', 'Prestasi berhasil ditambahkan.');
    }

    public function edit(Achievement $achievement): View
    {
        $achievement->loadMissing('document');
        return view('admin.prestasi.edit', compact('achievement'));
    }

    public function update(UpdateAchievementRequest $request, Achievement $achievement): RedirectResponse
    {
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

        return redirect()->route('admin.prestasi.index')->with('success', 'Prestasi berhasil diperbarui.');
    }

    public function destroy(Achievement $achievement): RedirectResponse
    {
        $oldMedia = $achievement->document;

        DB::transaction(function () use ($achievement) {
            $achievement->forceDelete();
        });

        if ($oldMedia) {
            $isUsedElsewhere = Achievement::withTrashed()->where('document_media_id', $oldMedia->id)->exists();
            if (! $isUsedElsewhere) {
                Storage::disk($oldMedia->disk ?? 'public')->delete($oldMedia->path);
                $oldMedia->forceDelete();
            }
        }

        return redirect()->route('admin.prestasi.index')->with('success', 'Prestasi berhasil dihapus permanen.');
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