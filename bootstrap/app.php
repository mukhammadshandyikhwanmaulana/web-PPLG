<?php

use App\Enums\UserRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Pendaftaran modular otomatis untuk rute Guru
            if (file_exists(base_path('routes/guru.php'))) {
                Route::middleware('web')->group(base_path('routes/guru.php'));
            }

            // Pendaftaran modular otomatis untuk rute Admin
            if (file_exists(base_path('routes/admin.php'))) {
                Route::middleware('web')->group(base_path('routes/admin.php'));
            }
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias Middleware Spatie Permission
        $middleware->alias([
            'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        // Pengalihan jika GUEST mencoba mengakses halaman terproteksi -> Satu Pintu Login
        $middleware->redirectGuestsTo(fn () => route('login'));

        // Pengalihan pintar jika user yang SUDAH LOGIN membuka halaman guest/login
        $middleware->redirectUsersTo(function () {
            $user = auth()->user();
            if (! $user) {
                return route('login');
            }

            $isAdmin = method_exists($user, 'hasRole') 
                ? $user->hasRole(UserRole::Admin->value) 
                : (($user->role ?? '') === UserRole::Admin->value);

            return $isAdmin 
                ? route('admin.dashboard') 
                : route('guru.dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();