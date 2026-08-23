<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProfileContentController;

Route::get('/', function () {
    return view('welcome');
});

// Route Admin Terproteksi Autentikasi
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/profil', [ProfileContentController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [ProfileContentController::class, 'update'])->name('profil.update');
});