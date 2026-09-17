<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\AuthenticatesWithRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UnifiedLoginController extends Controller
{
    use AuthenticatesWithRole;

    public function create(): View|RedirectResponse
    {
        // 1. Jika user sudah login, arahkan ke dashboard sesuai role masing-masing
        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user->role === UserRole::Guru->value || (is_object($user->role) && $user->role === UserRole::Guru)) {
                return redirect()->route('guru.dashboard');
            }

            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi Input Role
        $request->validate([
            'email'    => ['required', 'string', 'email:filter'],
            'password' => ['required', 'string'],
            'role'     => ['required', Rule::enum(UserRole::class)],
        ], [
            'email.required'    => 'Alamat email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'role.required'     => 'Peran akun wajib dipilih.',
            'role.enum'         => 'Peran akun yang dipilih tidak valid.',
        ]);

        $roleInput = (string) $request->input('role');
        $selectedRole = UserRole::tryFrom($roleInput);

        if (! $selectedRole) {
            throw ValidationException::withMessages([
                'role' => 'Peran akun yang dipilih tidak valid.',
            ]);
        }

        // 2. Invalidate sesi lama secara mutlak sebelum memproses login baru
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // 3. Eksekusi Attempt Login + Throttle + Logging + Validasi Peran via Trait
        $this->attemptLogin($request, $selectedRole);

        // 4. Pengalihan Dinamis Sesuai Role Pengguna
        $user = Auth::user();
        
        $targetRoute = ($selectedRole === UserRole::Guru || $user?->role === UserRole::Guru->value) 
            ? 'guru.dashboard' 
            : 'admin.dashboard';

        return redirect()->intended(route($targetRoute));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->performLogout($request);

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}