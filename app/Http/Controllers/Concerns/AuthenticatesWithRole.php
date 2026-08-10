<?php

namespace App\Http\Controllers\Concerns;

use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

trait AuthenticatesWithRole
{
    protected function attemptLogin(Request $request, UserRole $expectedRole): void
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Kredensial tidak valid.',
            ]);
        }

        $user = Auth::user();

        if (! $user->is_active || ! $user->hasRole($expectedRole->value)) {
            $this->performLogout($request);

            throw ValidationException::withMessages([
                'email' => 'Kredensial tidak valid.',
            ]);
        }

        $request->session()->regenerate();
    }

    protected function performLogout(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}