<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGuruRequest;
use App\Http\Requests\Admin\UpdateGuruRequest;
use App\Models\Media;
use App\Models\StaffMember;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GuruController extends Controller
{
    public function index(): View
    {
        $guru = User::role('guru')
            ->with(['roles', 'staffMember.photo'])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.guru.index', ['guru' => $guru]);
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
                'is_active' => $data['is_active'] ?? true,
            ]);

            $user->assignRole('guru');

            StaffMember::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'position' => $data['position'] ?? null,
                'expertise' => $data['expertise'] ?? null,
                'photo_media_id' => $mediaId,
                'is_active' => $data['staff_is_active'] ?? true,
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
        $mediaId = $this->storePhotoIfPresent($request);

        DB::transaction(function () use ($data, $mediaId, $guru) {
            $guru->fill([
                'name' => $data['name'],
                'email' => $data['email'],
                'is_active' => $data['is_active'] ?? $guru->is_active,
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
                'is_active' => $data['staff_is_active'] ?? ($existingProfile->is_active ?? true),
                'sort_order' => $data['sort_order'] ?? ($existingProfile->sort_order ?? 0),
                'updated_by' => auth()->id(),
            ];

            if ($mediaId !== null) {
                $profileData['photo_media_id'] = $mediaId;
            }

            if (! $existingProfile) {
                $profileData['created_by'] = auth()->id();
            }

            StaffMember::updateOrCreate(['user_id' => $guru->id], $profileData);
        });

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

    /**
     * Mencegah akun non-Guru (mis. Admin) diakses lewat controller ini —
     * proteksi langsung terhadap risiko "admin terhapus lewat CRUD guru" (F-005 §8).
     */
    protected function ensureIsGuru(User $user): void
    {
        abort_unless($user->hasRole('guru'), 404);
    }

    /**
     * Upload foto (jika ada) dan buat record Media baru.
     * Dilakukan DI LUAR DB::transaction() karena operasi filesystem
     * tidak transactional (F-008 Phase A §9/§12). Jika transaction
     * gagal setelah ini, file/Media jadi orphan sementara — risiko
     * kecil yang didokumentasikan, tidak di-auto-cleanup pada F-008.
     */
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