<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\StaffMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AccountController extends Controller
{
    /**
     * Tampilkan form edit profil khusus Guru.
     */
    public function edit(): View
    {
        $user = auth()->user();

        $staff = StaffMember::firstOrCreate(
            ['user_id' => $user->id],
            ['name' => $user->name, 'is_active' => true]
        );

        return view('guru.account.edit', compact('user', 'staff'));
    }

    /**
     * Simpan perubahan profil, avatar, dan biodata staf.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $staff = StaffMember::firstOrCreate(
            ['user_id' => $user->id],
            ['name' => $user->name, 'is_active' => true]
        );

        $rules = [
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'string', 'email:filter', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'position'         => ['nullable', 'string', 'max:255'],
            'expertise'        => ['nullable', 'string', 'max:255'],
            'avatar'           => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=4000,max_height=4000'],
            'remove_avatar'    => ['nullable', 'boolean'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['required', 'confirmed', Password::min(8)];
        }

        $validated = $request->validate($rules, [
            'name.required'                     => 'Nama lengkap wajib diisi.',
            'email.required'                    => 'Alamat email wajib diisi.',
            'email.email'                       => 'Format email tidak valid.',
            'email.unique'                      => 'Email sudah digunakan oleh akun lain.',
            'avatar.image'                      => 'Foto profil harus berupa berkas gambar.',
            'avatar.mimes'                      => 'Foto profil harus berformat JPG, JPEG, PNG, atau WEBP.',
            'avatar.max'                        => 'Ukuran foto profil maksimal 2 MB.',
            'avatar.dimensions'                 => 'Resolusi foto profil maksimal 4000x4000 piksel.',
            'current_password.required_with'    => 'Password saat ini wajib diisi untuk mengubah password.',
            'current_password.current_password' => 'Password saat ini tidak cocok.',
            'password.confirmed'                => 'Konfirmasi password baru tidak cocok.',
            'password.min'                      => 'Password baru minimal 8 karakter.',
        ]);

        DB::transaction(function () use ($request, $validated, $user, $staff) {

            // 1. Update Tabel Users (Identitas Login Guru)
            $user->name = trim($validated['name']);
            $user->email = trim(strtolower($validated['email']));

            if (! empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            // Fitur Hapus Foto Wajah
            if ($request->boolean('remove_avatar')) {
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $user->avatar = null;

                if ($staff->photo_media_id) {
                    $oldMedia = Media::find($staff->photo_media_id);
                    if ($oldMedia) {
                        if ($oldMedia->path && Storage::disk($oldMedia->disk ?? 'public')->exists($oldMedia->path)) {
                            Storage::disk($oldMedia->disk ?? 'public')->delete($oldMedia->path);
                        }
                        $oldMedia->forceDelete();
                    }
                    $staff->photo_media_id = null;
                }
            }

            // Fitur Upload Foto Wajah Guru Baru
            if ($request->hasFile('avatar')) {
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $file = $request->file('avatar');
                $storedPath = $file->store('avatars/guru', 'public');
                $user->avatar = $storedPath;

                $media = Media::create([
                    'original_name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    'file_name'     => basename($storedPath),
                    'path'          => $storedPath,
                    'disk'          => 'public',
                    'mime_type'     => $file->getMimeType(),
                    'size'          => $file->getSize(),
                    'created_by'    => $user->id,
                ]);

                $staff->photo_media_id = $media->id;
            }

            $user->save();

            // 2. Update Tabel StaffMembers (Biodata Publik Guru)
            $staff->name = trim($validated['name']);
            if (isset($validated['position'])) {
                $staff->position = trim($validated['position']);
            }
            if (isset($validated['expertise'])) {
                $staff->expertise = trim($validated['expertise']);
            }
            $staff->updated_by = $user->id;
            $staff->save();
        });

        return redirect()->route('guru.account.edit')->with('success', 'Profil dan biodata Guru berhasil diperbarui!');
    }
}