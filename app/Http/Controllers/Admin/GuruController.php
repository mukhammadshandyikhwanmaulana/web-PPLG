<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGuruRequest;
use App\Http\Requests\Admin\UpdateGuruRequest;
use App\Models\Media;
use App\Models\StaffMember;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GuruController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $guru = User::role('guru')
            ->with(['roles', 'staffMember.photo'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhereHas('staffMember', function ($sq) use ($search) {
                          $sq->where('position', 'like', "%{$search}%")
                            ->orWhere('expertise', 'like', "%{$search}%");
                      });
                });
            })
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->where('is_active', $status === 'active');
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.guru.index', [
            'guru' => $guru,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('admin.guru.create');
    }

    public function store(StoreGuruRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $mediaId = $this->storePhotoIfPresent($request);

        DB::transaction(function () use ($data, $mediaId) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'is_active' => $data['is_active'],
            ]);

            $user->assignRole('guru');

            StaffMember::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'position' => $data['position'] ?? null,
                'expertise' => $data['expertise'] ?? null,
                'photo_media_id' => $mediaId,
                'is_active' => $data['staff_is_active'],
                'sort_order' => $data['sort_order'] ?? 0,
                'created_by' => auth()->id(),
            ]);
        });

        return redirect()->route('admin.guru.index')->with('success', 'Akun Guru berhasil dibuat.');
    }

    public function edit(User $guru): View
    {
        $this->ensureIsGuru($guru);
        $guru->loadMissing('staffMember.photo');

        return view('admin.guru.edit', ['guru' => $guru]);
    }

    public function update(UpdateGuruRequest $request, User $guru): RedirectResponse
    {
        $this->ensureIsGuru($guru);

        $data = $request->validated();
        $oldMediaToDelete = null;

        $newMediaId = $this->storePhotoIfPresent($request);

        DB::transaction(function () use ($data, $newMediaId, $guru, &$oldMediaToDelete) {
            $guru->fill([
                'name' => $data['name'],
                'email' => $data['email'],
                'is_active' => $data['is_active'],
            ]);

            if (! empty($data['password'])) {
                $guru->password = $data['password'];
            }

            $guru->save();

            $existingProfile = $guru->staffMember;

            $profileData = [
                'name' => $data['name'],
                'position' => $data['position'] ?? null,
                'expertise' => $data['expertise'] ?? null,
                'is_active' => $data['staff_is_active'],
                'sort_order' => $data['sort_order'] ?? ($existingProfile->sort_order ?? 0),
                'updated_by' => auth()->id(),
            ];

            if ($newMediaId !== null) {
                if ($existingProfile?->photo) {
                    $oldMediaToDelete = $existingProfile->photo;
                }
                $profileData['photo_media_id'] = $newMediaId;
            }

            if (! $existingProfile) {
                $profileData['created_by'] = auth()->id();
            }

            StaffMember::updateOrCreate(['user_id' => $guru->id], $profileData);
        });

        // Hapus foto & media lama jika ada unggahan foto baru
        if ($oldMediaToDelete) {
            Storage::disk('public')->delete($oldMediaToDelete->file_path);
            $oldMediaToDelete->delete();
        }

        return redirect()->route('admin.guru.index')->with('success', 'Akun Guru berhasil diperbarui.');
    }

    public function destroy(User $guru): RedirectResponse
    {
        $this->ensureIsGuru($guru);

        DB::transaction(function () use ($guru) {
            $guru->update(['is_active' => false]);

            $guru->staffMember?->update([
                'is_active' => false,
                'updated_by' => auth()->id(),
            ]);
        });

        return redirect()->route('admin.guru.index')->with('success', 'Akun Guru berhasil dinonaktifkan.');
    }

    protected function ensureIsGuru(User $user): void
    {
        abort_unless($user->hasRole('guru'), 404);
    }

    protected function storePhotoIfPresent(StoreGuruRequest|UpdateGuruRequest $request): ?int
    {
        if (! $request->hasFile('photo')) {
            return null;
        }

        $file = $request->file('photo');
        $path = $file->store('staff', 'public');

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