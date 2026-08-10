<?php

use App\Http\Controllers\Guru\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->prefix('guru')->name('guru.')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('authenticate');
});

Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/', fn () => 'Guru area placeholder — F-004 foundation only.')->name('dashboard');
});