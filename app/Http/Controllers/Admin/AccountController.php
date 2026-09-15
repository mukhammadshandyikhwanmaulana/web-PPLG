<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function edit(Request $request): View
    {
        return view('admin.account.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'avatar' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::defaults()->min(8)],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email sudah digunakan oleh pengguna lain.',
            'avatar.image' => 'File foto profil harus berupa gambar yang valid.',
            'avatar.mimes' => 'Foto profil harus berformat JPG, JPEG, PNG, atau WEBP.',
            'avatar.max' => 'Ukuran foto profil maksimal 2 MB.',
            'current_password.required_with' => 'Password saat ini wajib diisi untuk mengganti password baru.',
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password baru minimal 8 karakter.',
        ]);

        $passwordChanged = false;

        DB::transaction(function () use ($user, $validated, $request, &$passwordChanged) {
            $user->name = $validated['name'];
            $user->email = $validated['email'];

            if ($request->boolean('remove_avatar') && $user->avatar) {
                Storage::disk('public')->delete($user->avatar);
                $user->avatar = null;
            }

            if ($request->hasFile('avatar')) {
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $user->avatar = $request->file('avatar')->store('avatars', 'public');
            }

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
                $passwordChanged = true;
            }

            $user->save();

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'update',
                'description' => 'Memperbarui profil akun' . ($passwordChanged ? ' dan password' : ''),
                'subject_type' => get_class($user),
                'subject_id' => $user->id,
                'properties' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'has_avatar' => !empty($user->avatar),
                    'password_changed' => $passwordChanged,
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        });

        return redirect()
            ->route('admin.account.edit')
            ->with('success', 'Profil akun Anda berhasil diperbarui.')
            ->with('password_changed', $passwordChanged);
    }
}