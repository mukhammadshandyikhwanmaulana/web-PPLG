<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PublishStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreIndustryPartnerRequest;
use App\Http\Requests\Admin\UpdateIndustryPartnerRequest;
use App\Models\IndustryPartner;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class IndustryPartnerController extends Controller
{
    public function index(Request $request): View
    {
        $partners = IndustryPartner::query()
            ->with('logo')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.mitra.index', compact('partners'));
    }

    public function create(): View 
    { 
        return view('admin.mitra.create'); 
    }

    public function store(StoreIndustryPartnerRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $data) {
            $mediaId = $this->storeLogoIfPresent($request);
            $status = $data['status'] ?? PublishStatus::Draft;
            $sortOrder = (isset($data['sort_order']) && $data['sort_order'] !== null && $data['sort_order'] !== '') 
                ? (int) $data['sort_order'] 
                : (IndustryPartner::max('sort_order') ?? 0) + 1;

            IndustryPartner::create([
                'name'          => $data['name'],
                'website_url'   => $data['website_url'] ?? null,
                'logo_media_id' => $mediaId,
                'sort_order'    => $sortOrder,
                'status'        => $status,
                'published_at'  => ($status === PublishStatus::Published || $status === PublishStatus::Published->value) ? now() : null,
                'created_by'    => auth()->id(),
                'updated_by'    => auth()->id(),
            ]);
        });

        $message = 'Mitra berhasil ditambahkan.';
        if (empty($data['website_url'])) {
            $message .= ' (Catatan: Disarankan melengkapi link website agar pengunjung web sekolah bisa langsung menuju profil resmi mitra).';
        }

        return redirect()->route('admin.mitra.index')->with('success', $message);
    }

    public function edit(IndustryPartner $industryPartner): View
    {
        $industryPartner->loadMissing('logo');
        return view('admin.mitra.edit', ['partner' => $industryPartner]);
    }

    public function update(UpdateIndustryPartnerRequest $request, IndustryPartner $industryPartner): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $industryPartner, $data) {
            $newMediaId = $this->storeLogoIfPresent($request);
            $status = $data['status'] ?? $industryPartner->status;
            $sortOrder = (isset($data['sort_order']) && $data['sort_order'] !== null && $data['sort_order'] !== '') 
                ? (int) $data['sort_order'] 
                : $industryPartner->sort_order;

            $updateData = [
                'name'        => $data['name'],
                'website_url' => $data['website_url'] ?? null,
                'sort_order'  => $sortOrder,
                'status'      => $status,
                'updated_by'  => auth()->id(),
            ];

            if (($status === PublishStatus::Published || $status === PublishStatus::Published->value) && $industryPartner->published_at === null) {
                $updateData['published_at'] = now();
            }

            if ($newMediaId !== null) {
                if ($industryPartner->logo_media_id) {
                    $oldMedia = Media::find($industryPartner->logo_media_id);
                    if ($oldMedia) {
                        $oldMedia->forceDelete();
                    }
                }
                $updateData['logo_media_id'] = $newMediaId;
            }

            $industryPartner->update($updateData);
        });

        $message = 'Mitra berhasil diperbarui.';
        if (empty($data['website_url'])) {
            $message .= ' (Catatan: Disarankan melengkapi link website agar pengunjung web sekolah bisa langsung menuju profil resmi mitra).';
        }

        return redirect()->route('admin.mitra.index')->with('success', $message);
    }

    public function destroy(IndustryPartner $industryPartner): RedirectResponse
    {
        DB::transaction(function () use ($industryPartner) {
            if ($industryPartner->logo) {
                $industryPartner->logo->forceDelete();
            }
            $industryPartner->forceDelete();
        });

        return redirect()->route('admin.mitra.index')->with('success', 'Mitra berhasil dihapus beserta logonya.');
    }

    protected function storeLogoIfPresent(Request $request): ?int
    {
        if (! $request->hasFile('logo')) return null;
        $file = $request->file('logo');
        $path = $file->store('mitra', 'public');
        
        $media = Media::create([
            'original_name' => $file->getClientOriginalName(),
            'file_name'     => $file->hashName(),
            'path'          => $path,
            'disk'          => 'public',
            'mime_type'     => $file->getClientMimeType(),
            'size'          => $file->getSize(),
            'created_by'    => auth()->id(),
        ]);
        
        return $media->id;
    }
}