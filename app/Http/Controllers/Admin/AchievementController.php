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
use Illuminate\Support\Str;
use Illuminate\View\View;

class AchievementController extends Controller
{
    /**
     * Memeriksa otorisasi akses admin.
     */
    protected function authorizeAdmin(): void
    {
        $user = auth()->user();

        // Tolak akses jika user tidak login atau memiliki role guru
        if (! $user || $user->hasRole('guru')) {
            abort(403, 'THIS ACTION IS UNAUTHORIZED');
        }
    }

    public function index(Request $request): View
    {
        $this->authorizeAdmin();

        $achievements = Achievement::with('document')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%'.$search.'%')
                        ->orWhere('contributor_name', 'like', '%'.$search.'%');
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

        return view('admin.prestasi.index', ['achievements' => $achievements]);
    }

    public function create(): View
    {
        $this->authorizeAdmin();

        return view('admin.prestasi.create');
    }

    public function store(StoreAchievementRequest $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validated();
        $mediaId = $this->storeDocumentIfPresent($request);

        $status = $data['status'] instanceof PublishStatus 
            ? $data['status'] 
            : PublishStatus::from($data['status']);

        $level = $data['level'] instanceof AchievementLevel 
            ? $data['level'] 
            : AchievementLevel::from($data['level']);

        Achievement::create([
            'title' => $data['title'],
            'slug' => $this->generateUniqueSlug($data['title']),
            'achievement_date' => $data['achievement_date'] ?? null,
            'level' => $level,
            'contributor_name' => $data['contributor_name'] ?? null,
            'description' => $data['description'] ?? null,
            'document_media_id' => $mediaId,
            'status' => $status,
            'published_at' => $status === PublishStatus::Published ? now() : null,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.prestasi.index')->with('success', 'Prestasi berhasil ditambahkan.');
    }

    public function edit(Achievement $achievement): View
    {
        $this->authorizeAdmin();

        $achievement->loadMissing('document');

        return view('admin.prestasi.edit', ['achievement' => $achievement]);
    }

    public function update(UpdateAchievementRequest $request, Achievement $achievement): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validated();
        $mediaId = $this->storeDocumentIfPresent($request);

        $status = $data['status'] instanceof PublishStatus 
            ? $data['status'] 
            : PublishStatus::from($data['status']);

        $level = $data['level'] instanceof AchievementLevel 
            ? $data['level'] 
            : AchievementLevel::from($data['level']);

        $updateData = [
            'title' => $data['title'],
            'slug' => $this->generateUniqueSlug($data['title'], $achievement->id, $achievement->slug),
            'achievement_date' => array_key_exists('achievement_date', $data) ? $data['achievement_date'] : $achievement->achievement_date,
            'level' => $level,
            'contributor_name' => array_key_exists('contributor_name', $data) ? $data['contributor_name'] : $achievement->contributor_name,
            'description' => array_key_exists('description', $data) ? $data['description'] : $achievement->description,
            'status' => $status,
            'updated_by' => auth()->id(),
        ];

        if ($status === PublishStatus::Published && $achievement->published_at === null) {
            $updateData['published_at'] = now();
        }

        if ($mediaId !== null) {
            $updateData['document_media_id'] = $mediaId;
        }

        $achievement->update($updateData);

        return redirect()->route('admin.prestasi.index')->with('success', 'Prestasi berhasil diperbarui.');
    }

    public function destroy(Achievement $achievement): RedirectResponse
    {
        $this->authorizeAdmin();

        $achievement->delete();

        return redirect()->route('admin.prestasi.index')->with('success', 'Prestasi berhasil dihapus.');
    }

    protected function storeDocumentIfPresent(Request $request): ?int
    {
        if (! $request->hasFile('document')) {
            return null;
        }

        $file = $request->file('document');
        $path = $file->store('achievements', 'public');

        $media = Media::create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
        ]);

        return $media->id;
    }

    protected function generateUniqueSlug(string $title, ?int $ignoreId = null, ?string $currentSlug = null): string
    {
        $base = Str::slug($title);

        if ($currentSlug !== null && Str::slug($currentSlug) === $base) {
            return $currentSlug;
        }

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