<?php

use App\Http\Controllers\Admin\AchievementController;
use App\Http\Controllers\Admin\ProfileContentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route Admin Terproteksi Autentikasi
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Manajemen Profil
    Route::get('/profil', [ProfileContentController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [ProfileContentController::class, 'update'])->name('profil.update');

    // Manajemen Prestasi
    Route::resource('prestasi', AchievementController::class)->parameters([
        'prestasi' => 'achievement',
    ]);
});