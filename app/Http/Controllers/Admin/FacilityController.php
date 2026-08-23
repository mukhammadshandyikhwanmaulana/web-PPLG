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
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FacilityController extends Controller
{
    public function index(Request $request): View
    {
        $facilities = Facility::with('photo')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->input('search').'%');
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

            Facility::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'photo_media_id' => $mediaId,
                'sort_order' => $data['sort_order'] ?? 0,
                'created_by' => auth()->id(),
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

            $updateData = [
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'sort_order' => $data['sort_order'] ?? $facility->sort_order,
                'updated_by' => auth()->id(),
            ];

            if ($mediaId !== null) {
                // Hapus media & berkas fisik lama
                if ($facility->photo) {
                    Storage::disk('public')->delete($facility->photo->file_path);
                    $facility->photo()->delete();
                }
                $updateData['photo_media_id'] = $mediaId;
            }

            $facility->update($updateData);
        });

        return redirect()->route('admin.fasilitas.index')->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function destroy(Facility $facility): RedirectResponse
    {
        $facility->delete();

        return redirect()->route('admin.fasilitas.index')->with('success', 'Fasilitas berhasil dihapus.');
    }

    protected function storePhotoIfPresent(StoreFacilityRequest|UpdateFacilityRequest $request): ?int
    {
        if (!$request->hasFile('photo')) {
            return null;
        }

        $file = $request->file('photo');
        $path = $file->store('facilities', 'public');

        $media = Media::create([
            'file_name' => basename($path),
            'file_path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
        ]);

        return $media->id;
    }
}