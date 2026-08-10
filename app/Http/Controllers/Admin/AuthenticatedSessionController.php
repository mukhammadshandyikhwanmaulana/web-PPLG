<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Concerns\AuthenticatesWithRole;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AuthenticatedSessionController extends Controller
{
    use AuthenticatesWithRole;

    public function create(): string
    {
        return 'Admin login form placeholder — F-004 foundation only.';
    }

    public function store(Request $request): RedirectResponse
    {
        $this->attemptLogin($request, UserRole::Admin);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->performLogout($request);

        return redirect()->route('admin.login');
    }
}