<?php

use App\Enums\UserRole;
// Import Controller Auth
use App\Http\Controllers\Auth\UnifiedLoginController;

// Import Controller Admin
use App\Http\Controllers\Admin\AccountController as AdminAccountController;
use App\Http\Controllers\Admin\AchievementController as AdminAchievementController;
use App\Http\Controllers\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\IndustryPartnerController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PrincipalWelcomeController;
use App\Http\Controllers\Admin\ProfileContentController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StudentWorkController as AdminStudentWorkController;
use App\Http\Controllers\Admin\UnitUsahaController;

// Import Controller Guru
use App\Http\Controllers\Guru\AccountController as GuruAccountController;
use App\Http\Controllers\Guru\AchievementController as GuruAchievementController;
use App\Http\Controllers\Guru\ActivityController as GuruActivityController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Guru\StudentWorkController as GuruStudentWorkController;

// Import Controller Public
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
| 1. Rute Publik (Frontend Landing Page)
|--------------------------------------------------------------------------
*/

Route::get('/', HomeController::class)->name('home');
Route::get('/profil', ProfileController::class)->name('public.profile');
Route::get('/unit-usaha', PublicUnitUsahaController::class)->name('public.unit-usaha');
Route::get('/guru-staf', StaffMemberController::class)->name('public.staff.index');

// Kegiatan Publik
Route::get('/kegiatan', [PublicActivityController::class, 'index'])->name('public.activities.index');
Route::get('/kegiatan/{slug}', [PublicActivityController::class, 'show'])->name('public.activities.show');

// Prestasi Publik
Route::get('/prestasi', [PublicAchievementController::class, 'index'])->name('public.achievements.index');
Route::get('/prestasi/{slug}', [PublicAchievementController::class, 'show'])->name('public.achievements.show');

// Galeri Publik
Route::get('/galeri', GalleryController::class)->name('public.galleries.index');

// Karya Siswa Publik
Route::get('/karya-siswa', [PublicStudentWorkController::class, 'index'])->name('public.student-works.index');
Route::get('/karya-siswa/{slug}', [PublicStudentWorkController::class, 'show'])->name('public.student-works.show');

// FAQ Publik
Route::get('/faq', PublicFaqController::class)->name('public.faq.index');

/*
|--------------------------------------------------------------------------
| 2. Rute Autentikasi Terpadu (Single Login Page)
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
| 3. Rute Admin Protected (Khusus Role Admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:' . UserRole::Admin->value])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', fn () => redirect()->route('admin.dashboard'));
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Notifikasi System
        Route::controller(NotificationController::class)->group(function () {
            Route::post('notifications/mark-all-as-read', 'markAllAsRead')->name('notifications.markAllAsRead');
            Route::post('notifications/{id}/mark-as-read', 'markAsRead')->name('notifications.markAsRead');
        });

        // Akun Admin
        Route::controller(AdminAccountController::class)->group(function () {
            Route::get('account', 'edit')->name('account.edit');
            Route::put('account', 'update')->name('account.update');
        });

        // Master Data & Modul Khusus Admin
        Route::resource('guru', GuruController::class)->except(['show']);

        Route::controller(ProfileContentController::class)->group(function () {
            Route::get('profil', 'edit')->name('profil.edit');
            Route::put('profil', 'update')->name('profil.update');
        });

        Route::controller(UnitUsahaController::class)->group(function () {
            Route::get('unit-usaha', 'edit')->name('unit-usaha.edit');
            Route::put('unit-usaha', 'update')->name('unit-usaha.update');
        });

        Route::controller(PrincipalWelcomeController::class)->group(function () {
            Route::get('sambutan', 'edit')->name('sambutan.edit');
            Route::put('sambutan', 'update')->name('sambutan.update');
        });

        Route::delete('media/destroy-group', [MediaController::class, 'destroyGroup'])->name('media.destroy-group');

        // CRUD Resource Management Utama
        Route::resources([
            'banner'      => BannerController::class,
            'fasilitas'   => FacilityController::class,
            'prestasi'    => AdminAchievementController::class,
            'karya-siswa' => AdminStudentWorkController::class,
            'kegiatan'    => AdminActivityController::class,
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

        // Pengaturan Sistem & Log Aktivitas
        Route::controller(SettingController::class)->group(function () {
            Route::get('pengaturan', 'edit')->name('pengaturan.edit');
            Route::put('pengaturan', 'update')->name('pengaturan.update');
        });

        Route::resource('activity-log', ActivityLogController::class)->only(['index', 'show']);
    });

/*
|--------------------------------------------------------------------------
| 4. Rute Guru Protected (Khusus Role Guru & Admin)
|--------------------------------------------------------------------------
*/

$guruRoles = UserRole::Guru->value . '|' . UserRole::Admin->value;

Route::middleware(['auth', 'role:' . $guruRoles])
    ->prefix('guru')
    ->name('guru.')
    ->group(function () {

        Route::get('/', fn () => redirect()->route('guru.dashboard'));
        Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');

        // Management Konten yang dibuat Guru sendiri
        Route::resources([
            'karya-siswa' => GuruStudentWorkController::class,
            'kegiatan'    => GuruActivityController::class,
            'prestasi'    => GuruAchievementController::class,
        ], [
            'except'     => ['show'],
            'parameters' => [
                'karya-siswa' => 'studentWork',
                'kegiatan'    => 'activity',
                'prestasi'    => 'achievement',
            ],
        ]);

        // Akun Profil Guru
        Route::controller(GuruAccountController::class)->group(function () {
            Route::get('account', 'edit')->name('account.edit');
            Route::put('account', 'update')->name('account.update');
        });
    });