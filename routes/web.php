<?php

use App\Enums\UserRole;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AchievementController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\IndustryPartnerController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PrincipalWelcomeController;
use App\Http\Controllers\Admin\ProfileContentController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StudentWorkController;
use App\Http\Controllers\Admin\UnitUsahaController;
use App\Http\Controllers\Auth\UnifiedLoginController;
use App\Http\Controllers\Public\AchievementController as PublicAchievementController;
use App\Http\Controllers\Public\ActivityController as PublicActivityController;
use App\Http\Controllers\Public\FaqController as PublicFaqController;
use App\Http\Controllers\Public\GalleryController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ProfileController;
use App\Http\Controllers\Public\StaffMemberController;
use App\Http\Controllers\Public\StudentWorkController as PublicStudentWorkController;
use App\Http\Controllers\Public\UnitUsahaController as PublicUnitUsahaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Publik (Frontend Landing Page)
|--------------------------------------------------------------------------
*/

Route::get('/', HomeController::class)->name('home');
Route::get('/profil', ProfileController::class)->name('public.profile');
Route::get('/unit-usaha', PublicUnitUsahaController::class)->name('public.unit-usaha');

// Rute Guru & Staf Publik
Route::get('/guru-staf', StaffMemberController::class)->name('public.staff.index');

// Rute Kegiatan Publik
Route::get('/kegiatan', [PublicActivityController::class, 'index'])->name('public.activities.index');
Route::get('/kegiatan/{slug}', [PublicActivityController::class, 'show'])->name('public.activities.show');

// Rute Prestasi Publik
Route::get('/prestasi', [PublicAchievementController::class, 'index'])->name('public.achievements.index');
Route::get('/prestasi/{slug}', [PublicAchievementController::class, 'show'])->name('public.achievements.show');

// Rute Galeri Publik
Route::get('/galeri', GalleryController::class)->name('public.galleries.index');

// Rute Karya Siswa Publik
Route::get('/karya-siswa', [PublicStudentWorkController::class, 'index'])->name('public.student-works.index');
Route::get('/karya-siswa/{slug}', [PublicStudentWorkController::class, 'show'])->name('public.student-works.show');

// Rute FAQ Publik
Route::get('/faq', PublicFaqController::class)->name('public.faq.index');

/*
|--------------------------------------------------------------------------
| Rute Autentikasi Terpadu (Single Login Page)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [UnifiedLoginController::class, 'create'])->name('login');
    Route::post('/login', [UnifiedLoginController::class, 'store'])->name('login.store');
});

Route::post('/logout', [UnifiedLoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Rute Admin Protected (Khusus Role Admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:' . UserRole::Admin->value . ',web'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Redirect /admin ke /admin/dashboard
        Route::get('/', fn () => redirect()->route('admin.dashboard'));

        // Dashboard Admin
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Manajemen Notifikasi System / Activity Log
        Route::controller(NotificationController::class)->group(function () {
            Route::post('notifications/mark-all-as-read', 'markAllAsRead')->name('notifications.markAllAsRead');
            Route::post('notifications/{id}/mark-as-read', 'markAsRead')->name('notifications.markAsRead');
        });

        // Manajemen Profil Akun Admin Saya
        Route::controller(AccountController::class)->group(function () {
            Route::get('account', 'edit')->name('account.edit');
            Route::put('account', 'update')->name('account.update');
        });

        // Manajemen Guru & Staf
        Route::resource('guru', GuruController::class)->except(['show']);

        // Manajemen Konten Profil & Unit Usaha
        Route::controller(ProfileContentController::class)->group(function () {
            Route::get('profil', 'edit')->name('profil.edit');
            Route::put('profil', 'update')->name('profil.update');
        });

        Route::controller(UnitUsahaController::class)->group(function () {
            Route::get('unit-usaha', 'edit')->name('unit-usaha.edit');
            Route::put('unit-usaha', 'update')->name('unit-usaha.update');
        });

        // Manajemen Sambutan Kepala Jurusan (Kajur)
        Route::controller(PrincipalWelcomeController::class)->group(function () {
            Route::get('sambutan', 'edit')->name('sambutan.edit');
            Route::put('sambutan', 'update')->name('sambutan.update');
        });

        // Operasi Khusus Media Album (WAJIB didefinisikan sebelum Route::resources)
        Route::delete('media/destroy-group', [MediaController::class, 'destroyGroup'])->name('media.destroy-group');

        // CRUD Resource Management Terpusat
        Route::resources([
            'banner'      => BannerController::class,
            'fasilitas'   => FacilityController::class,
            'prestasi'    => AchievementController::class,
            'karya-siswa' => StudentWorkController::class,
            'kegiatan'    => ActivityController::class,
            'mitra'       => IndustryPartnerController::class,
            'media'       => MediaController::class,
            'faq'         => FaqController::class,
        ], [
            'except'     => ['show'],
            'parameters' => [
                'fasilitas'   => 'facility',
                'prestasi'    => 'achievement',
                'karya-siswa' => 'studentWork',
                'kegiatan'    => 'activity',
                'mitra'       => 'industryPartner',
            ],
        ]);

        // System Settings & Activity Logs
        Route::controller(SettingController::class)->group(function () {
            Route::get('pengaturan', 'edit')->name('pengaturan.edit');
            Route::put('pengaturan', 'update')->name('pengaturan.update');
        });

        Route::resource('activity-log', ActivityLogController::class)->only(['index', 'show']);
    });