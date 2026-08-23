<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Jika routes/admin.php tidak didaftarkan otomatis di bootstrap/app.php (Laravel 11) 
// atau RouteServiceProvider (Laravel 10 Kebawah), Anda bisa memuatnya di sini:
require __DIR__.'/admin.php';