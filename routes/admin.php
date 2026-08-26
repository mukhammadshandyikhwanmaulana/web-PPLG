<?php

use App\Http\Controllers\Admin\AchievementController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\AuthenticatedSessionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\IndustryPartnerController;
use App\Http\Controllers\Admin\ProfileContentController;
use App\Http\Controllers\Admin\StudentWorkController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Guest Routes (Belum Login)
    |--------------------------------------------------------------------------
    */
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');

        Route::post('login', [AuthenticatedSessionController::class, 'store'])
            ->middleware('throttle:5,1')
            ->name('authenticate');
    });

    /*
    |--------------------------------------------------------------------------
    | Authenticated Admin Routes (Sudah Login & Role Admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        // Manajemen Akun Guru
        Route::resource('guru', GuruController::class)->except(['show']);

        // Kelola Konten Profil (Singleton: Edit & Update)
        Route::get('profil', [ProfileContentController::class, 'edit'])->name('profil.edit');
        Route::put('profil', [ProfileContentController::class, 'update'])->name('profil.update');

        // Manajemen Fasilitas
        Route::resource('fasilitas', FacilityController::class)
            ->parameters(['fasilitas' => 'facility'])
            ->except(['show']);

        // Manajemen Prestasi
        Route::resource('prestasi', AchievementController::class)
            ->parameters(['prestasi' => 'achievement'])
            ->except(['show']);

        // Manajemen Karya Siswa
        Route::resource('karya-siswa', StudentWorkController::class)
            ->parameters(['karya-siswa' => 'student_work'])
            ->except(['show']);

        // Manajemen Kegiatan
        Route::resource('kegiatan', ActivityController::class)
            ->parameters(['kegiatan' => 'activity'])
            ->except(['show']);

        // Manajemen Mitra Industri
        Route::resource('mitra', IndustryPartnerController::class)
            ->parameters(['mitra' => 'industry_partner'])
            ->except(['show']);
    });

});