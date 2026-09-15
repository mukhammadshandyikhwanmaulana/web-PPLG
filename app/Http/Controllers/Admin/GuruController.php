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
        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status');

        $guru = User::query()
            ->select('users.*')
            ->where(function ($q) {
                if (method_exists(User::class, 'scopeRole')) {
                    $q->role('guru');
                } else {
                    $q->whereHas('roles', fn ($r) => $r->where('name', 'guru'))
                      ->orWhere('role', 'guru');
                }
            })
            ->leftJoin('staff_members', 'staff_members.user_id', '=', 'users.id')
            ->with(['staffMember.photo'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('users.name', 'like', "%{$search}%")
                      ->orWhere('users.email', 'like', "%{$search}%")
                      ->orWhereHas('staffMember', function ($qStaff) use ($search) {
                          $qStaff->where('position', 'like', "%{$search}%")
                                 ->orWhere('expertise', 'like', "%{$search}%");
                      });
                });
            })
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->where('users.is_active', $status === 'active');
            })
            ->orderByRaw("
                CASE 
                    WHEN LOWER(COALESCE(staff_members.position, '')) LIKE '%ketua kompetensi keahlian%' 
                      OR LOWER(COALESCE(staff_members.position, '')) LIKE '%kepala jurusan%' 
                      OR LOWER(COALESCE(staff_members.position, '')) LIKE '%kaprog%' 
                      OR LOWER(COALESCE(staff_members.position, '')) LIKE '%kakomli%' 
                      OR LOWER(COALESCE(staff_members.position, '')) LIKE '%kajur%' 
                      OR LOWER(COALESCE(staff_members.position, '')) LIKE '%kepala program%' THEN 1
                    WHEN LOWER(COALESCE(staff_members.position, '')) LIKE '%guru%' THEN 2
                    ELSE 3
                END ASC
            ")
            ->orderBy('users.name', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.guru.index', compact('guru', 'search', 'status'));
    }

    public function create(): View
    {
        $isKajurExist = StaffMember::where(function ($query) {
            $query->where('position', 'like', '%Ketua Kompetensi Keahlian%')
                  ->orWhere('position', 'like', '%Kepala Jurusan%');
        })->exists();

        return view('admin.guru.create', compact('isKajurExist'));
    }

    public function store(StoreGuruRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $data) {
            $mediaData = $this->storePhotoIfPresent($request);

            $user = User::create([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'password'  => bcrypt($data['password']),
                'avatar'    => $mediaData['path'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            if (method_exists($user, 'assignRole')) {
                $user->assignRole('guru');
            } else {
                $user->update(['role' => 'guru']);
            }

            $positions = $request->input('positions', []);
            $positionString = !empty($positions) ? implode(', ', $positions) : null;

            StaffMember::create([
                'user_id'        => $user->id,
                'name'           => $data['name'],
                'position'       => $positionString,
                'expertise'      => $data['expertise'] ?? null,
                'photo_media_id' => $mediaData['id'] ?? null,
                'is_active'      => $data['staff_is_active'] ?? $data['is_active'] ?? true,
                'sort_order'     => 0,
                'created_by'     => auth()->id(),
                'updated_by'     => auth()->id(),
            ]);
        });

        return redirect()->route('admin.guru.index')->with('success', 'Akun Guru berhasil dibuat.');
    }

    public function edit(User $guru): View
    {
        $this->ensureIsGuru($guru);
        $guru->loadMissing('staffMember.photo');

        $rawPosition = $guru->staffMember?->position ?? '';
        $selectedPositions = array_filter(array_map('trim', explode(',', $rawPosition)));

        $isKajurExist = StaffMember::where(function ($query) {
            $query->where('position', 'like', '%Ketua Kompetensi Keahlian%')
                  ->orWhere('position', 'like', '%Kepala Jurusan%');
        })
        ->where('user_id', '!=', $guru->id)
        ->exists();

        return view('admin.guru.edit', compact('guru', 'selectedPositions', 'isKajurExist'));
    }

    public function update(UpdateGuruRequest $request, User $guru): RedirectResponse
    {
        $this->ensureIsGuru($guru);
        $data = $request->validated();
        $oldMediaToDelete = null;

        DB::transaction(function () use ($request, $data, $guru, &$oldMediaToDelete) {
            $newMediaData = $this->storePhotoIfPresent($request);
            
            $guruData = [
                'name'      => $data['name'],
                'email'     => $data['email'],
                'is_active' => $data['is_active'] ?? true,
            ];

            if ($newMediaData !== null) {
                $guruData['avatar'] = $newMediaData['path'];
            }

            if (!empty($data['password'])) {
                $guruData['password'] = bcrypt($data['password']);
            }

            $guru->update($guruData);

            $existingProfile = $guru->staffMember;
            $positions       = $request->input('positions', []);
            $positionString  = !empty($positions) ? implode(', ', $positions) : null;

            $profileData = [
                'name'       => $data['name'],
                'position'   => $positionString,
                'expertise'  => $data['expertise'] ?? null,
                'is_active'  => $data['staff_is_active'] ?? $data['is_active'] ?? true,
                'sort_order' => $existingProfile->sort_order ?? 0,
                'updated_by' => auth()->id(),
            ];

            if ($newMediaData !== null) {
                if ($existingProfile?->photo_media_id) {
                    $oldMediaToDelete = Media::find($existingProfile->photo_media_id);
                }
                $profileData['photo_media_id'] = $newMediaData['id'];
            }

            StaffMember::updateOrCreate(['user_id' => $guru->id], $profileData);
        });

        if ($oldMediaToDelete) {
            $isUsedElsewhere = StaffMember::where('photo_media_id', $oldMediaToDelete->id)
                ->where('user_id', '!=', $guru->id)
                ->exists();

            if (! $isUsedElsewhere) {
                Storage::disk($oldMediaToDelete->disk ?? 'public')->delete($oldMediaToDelete->path);
                $oldMediaToDelete->forceDelete();
            }
        }

        return redirect()->route('admin.guru.index')->with('success', 'Akun Guru berhasil diperbarui.');
    }

    public function destroy(User $guru): RedirectResponse
    {
        $this->ensureIsGuru($guru);

        $newStatus = ! $guru->is_active;

        DB::transaction(function () use ($guru, $newStatus) {
            $guru->update(['is_active' => $newStatus]);

            if ($guru->staffMember) {
                $guru->staffMember->update([
                    'is_active'  => $newStatus,
                    'updated_by' => auth()->id(),
                ]);
            }
        });

        $message = $newStatus ? 'Akun Guru berhasil diaktifkan kembali.' : 'Akun Guru berhasil dinonaktifkan.';

        return redirect()->route('admin.guru.index')->with('success', $message);
    }

    protected function ensureIsGuru(User $user): void
    {
        $isGuru = method_exists($user, 'hasRole') 
            ? $user->hasRole('guru') 
            : (($user->role ?? '') === 'guru');

        abort_unless($isGuru, 404);
    }

    protected function storePhotoIfPresent(Request $request): ?array
    {
        if (! $request->hasFile('photo')) {
            return null;
        }

        $file = $request->file('photo');
        $path = $file->store('staff', 'public');

        $media = Media::create([
            'original_name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'file_name'     => $file->hashName(),
            'path'          => $path,
            'disk'          => 'public',
            'mime_type'     => $file->getMimeType(),
            'size'          => $file->getSize(),
            'created_by'    => auth()->id(),
        ]);

        return [
            'id'   => $media->id,
            'path' => $path,
        ];
    }
}