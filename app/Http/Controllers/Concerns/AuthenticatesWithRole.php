<?php

namespace App\Http\Controllers\Concerns;

use App\Enums\UserRole;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait AuthenticatesWithRole
{
    protected function attemptLogin(Request $request, UserRole $role): void
    {
        $this->ensureIsNotRateLimited($request);

        $credentials = [
            'email'    => trim((string) $request->input('email')),
            'password' => (string) $request->input('password'),
        ];

        $remember = $request->boolean('remember');
        $logger = app(ActivityLogger::class);

        // 1. Percobaan Autentikasi Email & Password
        if (! Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($this->throttleKey($request));

            $logger->log(
                action: 'login_failed',
                description: 'Percobaan login gagal untuk email: ' . $credentials['email'],
                properties: [
                    'email'          => $credentials['email'],
                    'role_attempted' => $role->value,
                ]
            );

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 2. Cek Status Aktif Akun
        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $logger->log(
                action: 'login_blocked_inactive',
                description: "User {$user->email} mencoba login tetapi status akun non-aktif.",
                user: $user
            );

            throw ValidationException::withMessages([
                'email' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi Administrator.',
            ]);
        }

        // 3. Validasi Hak Akses Role
        $userRoleValue = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;

        $isAdmin = method_exists($user, 'hasRole') 
            ? ($user->hasRole(UserRole::Admin->value) || $user->hasRole('admin') || $user->hasRole('super-admin'))
            : ($userRoleValue === UserRole::Admin->value);

        $isGuru = method_exists($user, 'hasRole')
            ? $user->hasRole(UserRole::Guru->value)
            : ($userRoleValue === UserRole::Guru->value);

        // ATURAN HAK AKSES:
        // - Jika mencoba login sebagai ADMIN: User WAJIB memiliki role Admin. Guru yang mencoba login Admin akan ditolak.
        // - Jika mencoba login sebagai GURU: User role Admin maupun Guru diizinkan.
        if ($role === UserRole::Admin && ! $isAdmin) {
            $logger->log(
                action: 'unauthorized_access',
                description: "User {$user->email} (Guru) mencoba login sebagai Administrator tanpa hak akses.",
                user: $user
            );

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'role' => 'Akun ini tidak memiliki hak akses sebagai Administrator.',
            ]);
        }

        if ($role === UserRole::Guru && ! $isGuru && ! $isAdmin) {
            $logger->log(
                action: 'unauthorized_access',
                description: "User {$user->email} mencoba login sebagai Guru tanpa hak akses.",
                user: $user
            );

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'role' => 'Akun ini tidak memiliki hak akses sebagai Guru.',
            ]);
        }

        // 4. Sinkronisasi Role Spatie secara otomatis jika belum ada
        if (method_exists($user, 'syncRoles')) {
            $roleToSync = $isAdmin ? UserRole::Admin->value : UserRole::Guru->value;
            if (! $user->hasRole($roleToSync)) {
                $user->syncRoles([$roleToSync]);
            }
        }

        // 5. Bersihkan Rate Limiter & Regenerasi Sesi
        RateLimiter::clear($this->throttleKey($request));
        $request->session()->regenerate();
    }

    protected function performLogout(Request $request): void
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => (int) ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower((string) $request->input('email')) . '|' . $request->ip());
    }
}