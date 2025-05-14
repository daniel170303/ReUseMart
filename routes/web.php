<?php

use Illuminate\Support\Facades\Route;
use App\Models\BarangTitipan;
use App\Http\Controllers\AuthController;

// Halaman Utama (Landing Page)
Route::get('/', function () {
    // Ambil 3 barang titipan terbaru
    $barangTitipan = BarangTitipan::take(3)->get();

    // Kirim data barangTitipan ke view
    return view('landingPage.landingPage', compact('barangTitipan'));
});

// Halaman Login (GET request)
Route::get('/login', function () {
    return view('login.login');
})->name('login');

// Halaman Login (POST request) untuk autentikasi
Route::post('/login', [AuthController::class, 'login']);

// Halaman Registrasi (GET request)
Route::get('/register', function () {
    return view('auth.register');
});

// Halaman Logout (GET request)
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
