<?php

use App\Enums\UserRole;
use App\Http\Controllers\Guru\AccountController;
use App\Http\Controllers\Guru\AchievementController;
use App\Http\Controllers\Guru\ActivityController;
use App\Http\Controllers\Guru\DashboardController;
use App\Http\Controllers\Guru\StudentWorkController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Guru Protected (Role Guru & Admin)
|--------------------------------------------------------------------------
*/

// Menggunakan tanda pipa '|' (OR) untuk Spatie Permission
$guruRoles = UserRole::Guru->value . '|' . UserRole::Admin->value;

Route::middleware(['web', 'auth:web', 'role:' . $guruRoles . ',web'])
    ->prefix('guru')
    ->name('guru.')
    ->group(function () {

        // Redirect /guru ke /guru/dashboard
        Route::get('/', fn () => redirect()->route('guru.dashboard'));

        // Dashboard Utama Guru
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Pengelolaan Karya, Kegiatan, dan Prestasi Guru
        Route::resources([
            'karya-siswa' => StudentWorkController::class,
            'kegiatan'    => ActivityController::class,
            'prestasi'    => AchievementController::class,
        ], [
            'except'     => ['show'],
            'parameters' => [
                'karya-siswa' => 'studentWork',
                'kegiatan'    => 'activity',
                'prestasi'    => 'achievement',
            ],
        ]);

        // Pengaturan Akun Profil Guru
        Route::controller(AccountController::class)->group(function () {
            Route::get('account', 'edit')->name('account.edit');
            Route::put('account', 'update')->name('account.update');
        });
    });