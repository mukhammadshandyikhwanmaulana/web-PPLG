<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFacilityRequest;
use App\Http\Requests\Admin\UpdateFacilityRequest;
use App\Models\Facility;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FacilityController extends Controller
{
    public function index(Request $request): View
    {
        $facilities = Facility::with('photo')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.fasilitas.index', compact('facilities'));
    }

    public function create(): View 
    { 
        return view('admin.fasilitas.create'); 
    }

    public function store(StoreFacilityRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $data) {
            $mediaId = $this->storePhotoIfPresent($request);
            if (isset($data['sort_order']) && $data['sort_order'] > 0) {
                Facility::where('sort_order', '>=', $data['sort_order'])->lockForUpdate()->increment('sort_order');
                $sortOrder = (int) $data['sort_order'];
            } else {
                $sortOrder = (Facility::max('sort_order') ?? 0) + 1;
            }

            Facility::create([
                'name'           => $data['name'],
                'description'    => $data['description'] ?? null,
                'photo_media_id' => $mediaId,
                'sort_order'     => $sortOrder,
                'created_by'     => auth()->id(),
                'updated_by'     => auth()->id(),
            ]);
        });

        return redirect()->route('admin.fasilitas.index')->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    public function edit(Facility $facility): View
    {
        $facility->loadMissing('photo');
        return view('admin.fasilitas.edit', compact('facility'));
    }

    public function update(UpdateFacilityRequest $request, Facility $facility): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $facility, $data) {
            $mediaId = $this->storePhotoIfPresent($request);
            $oldSortOrder = (int) ($facility->sort_order ?? 0);
            $newSortOrder = (isset($data['sort_order']) && $data['sort_order'] > 0) ? (int) $data['sort_order'] : $oldSortOrder;

            if ($oldSortOrder > 0 && $newSortOrder !== $oldSortOrder && $newSortOrder > 0) {
                if ($newSortOrder < $oldSortOrder) {
                    Facility::where('id', '!=', $facility->id)->where('sort_order', '>=', $newSortOrder)->where('sort_order', '<', $oldSortOrder)->lockForUpdate()->increment('sort_order');
                } else {
                    Facility::where('id', '!=', $facility->id)->where('sort_order', '>', $oldSortOrder)->where('sort_order', '<=', $newSortOrder)->lockForUpdate()->decrement('sort_order');
                }
            } elseif ($oldSortOrder === 0 && $newSortOrder > 0) {
                Facility::where('id', '!=', $facility->id)->where('sort_order', '>=', $newSortOrder)->lockForUpdate()->increment('sort_order');
            }

            $updateData = [
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'sort_order'  => $newSortOrder > 0 ? $newSortOrder : ((Facility::max('sort_order') ?? 0) + 1),
                'updated_by'  => auth()->id(),
            ];

            if ($mediaId !== null) {
                if ($facility->photo_media_id) {
                    $oldPhoto = Media::find($facility->photo_media_id);
                    if ($oldPhoto) {
                        $oldPhoto->forceDelete();
                    }
                }
                $updateData['photo_media_id'] = $mediaId;
            } else {
                if ($facility->photo_media_id) {
                    $altText = $this->formatAltText($data['description'] ?? null, $data['name']);
                    Media::where('id', $facility->photo_media_id)->update([
                        'alt_text'   => $altText,
                        'updated_by' => auth()->id(),
                    ]);
                }
            }

            $facility->update($updateData);
        });

        return redirect()->route('admin.fasilitas.index')->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function destroy(Facility $facility): RedirectResponse
    {
        DB::transaction(function () use ($facility) {
            $deletedOrder = (int) ($facility->sort_order ?? 0);
            if ($facility->photo) {
                $facility->photo->forceDelete();
            }
            $facility->forceDelete();

            if ($deletedOrder > 0) {
                Facility::where('sort_order', '>', $deletedOrder)->decrement('sort_order');
            }
        });

        return redirect()->route('admin.fasilitas.index')->with('success', 'Fasilitas berhasil dihapus.');
    }

    protected function storePhotoIfPresent(Request $request): ?int
    {
        if (! $request->hasFile('photo')) return null;
        $file = $request->file('photo');
        $path = $file->store('facilities', 'public');
        $media = Media::create([
            'original_name' => $file->getClientOriginalName(),
            'file_name'     => basename($path),
            'disk'          => 'public',
            'path'          => $path,
            'mime_type'     => $file->getClientMimeType(),
            'size'          => $file->getSize(),
            'alt_text'      => $this->formatAltText($request->input('description'), $request->input('name')),
            'created_by'    => auth()->id(),
        ]);
        return $media->id;
    }

    protected function formatAltText(?string $description, string $name): string
    {
        $cleanDesc = $description ? trim(strip_tags($description)) : '';
        return $cleanDesc !== '' ? Str::limit($cleanDesc, 250) : Str::limit($name, 250);
    }
}