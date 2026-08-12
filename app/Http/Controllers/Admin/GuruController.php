<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGuruRequest;
use App\Http\Requests\Admin\UpdateGuruRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GuruController extends Controller
{
    public function index(): View
    {
        $guru = User::role('guru')
            ->with('roles')
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

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_active' => $data['is_active'] ?? true,
        ]);

        $user->assignRole('guru');

        return redirect()->route('admin.guru.index')->with('success', 'Akun Guru berhasil dibuat.');
    }

    public function edit(User $guru): View
    {
        $this->ensureIsGuru($guru);

        return view('admin.guru.edit', ['guru' => $guru]);
    }

    public function update(UpdateGuruRequest $request, User $guru): RedirectResponse
    {
        $this->ensureIsGuru($guru);

        $data = $request->validated();

        $guru->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'is_active' => $data['is_active'] ?? $guru->is_active,
        ]);

        if (! empty($data['password'])) {
            $guru->password = $data['password'];
        }

        $guru->save();

        return redirect()->route('admin.guru.index')->with('success', 'Akun Guru berhasil diperbarui.');
    }

    public function destroy(User $guru): RedirectResponse
    {
        $this->ensureIsGuru($guru);

        $guru->update(['is_active' => false]);

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
}