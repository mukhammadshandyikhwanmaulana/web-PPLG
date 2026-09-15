<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $banners = Banner::orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.banner.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banner.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image'     => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'is_active' => 'nullable|boolean',
        ], [
            'image.required' => 'Gambar Hero wajib diunggah.',
            'image.image'    => 'Berkas harus berupa gambar.',
            'image.max'      => 'Ukuran gambar maksimal 5MB.',
        ]);

        DB::transaction(function () use ($request) {
            $lastOrder = Banner::max('order') ?? 0;
            $newOrder = $lastOrder + 1;

            $imagePath = $request->file('image')->store('banners', 'public');

            Banner::create([
                'title'       => 'Hero Image ' . date('YmdHis'),
                'subtitle'    => null,
                'image_path'  => $imagePath,
                'button_text' => null,
                'button_url'  => null,
                'order'       => $newOrder,
                'is_active'   => $request->boolean('is_active'),
            ]);

            $this->reorderAllBanners();
        });

        return redirect()->route('admin.banner.index')->with('success', 'Gambar Hero berhasil ditambahkan!');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banner.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'is_active' => 'nullable|boolean',
        ], [
            'image.image' => 'Berkas harus berupa gambar.',
            'image.max'   => 'Ukuran gambar maksimal 5MB.',
        ]);

        DB::transaction(function () use ($request, $banner) {
            $dataUpdate = [
                'is_active' => $request->boolean('is_active'),
            ];

            if ($request->hasFile('image')) {
                if ($banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
                    Storage::disk('public')->delete($banner->image_path);
                }

                $dataUpdate['image_path'] = $request->file('image')->store('banners', 'public');
            }

            $banner->update($dataUpdate);
        });

        return redirect()->route('admin.banner.index')->with('success', 'Gambar Hero berhasil diperbarui!');
    }

    public function destroy(Banner $banner)
    {
        DB::transaction(function () use ($banner) {
            if ($banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
                Storage::disk('public')->delete($banner->image_path);
            }

            $banner->delete();
            $this->reorderAllBanners();
        });

        return redirect()->route('admin.banner.index')->with('success', 'Gambar Hero berhasil dihapus!');
    }

    private function reorderAllBanners(): void
    {
        $banners = Banner::orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($banners as $index => $item) {
            $item->updateQuietly(['order' => $index + 1]);
        }
    }
}