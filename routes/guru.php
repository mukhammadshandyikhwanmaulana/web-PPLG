<?php

use App\Http\Controllers\Guru\AuthenticatedSessionController;
use App\Http\Controllers\Guru\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->prefix('guru')->name('guru.')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('authenticate');
});

// role:guru|admin — Admin boleh mengakses area Guru untuk pengawasan (sesuai persetujuan Anda)
Route::middleware(['auth', 'role:guru|admin'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});