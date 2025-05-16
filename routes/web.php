<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangTitipanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PenitipController;
use App\Models\BarangTitipan;

// Landing page, tampilkan 3 barang titipan
Route::get('/', function () {
    $barangTitipan = BarangTitipan::take(3)->get();
    return view('landingPage.landingPage', compact('barangTitipan'));
});

// Form login umum
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

// Proses login (satu route POST untuk semua role)
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

// Logout (bisa digunakan untuk semua role)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Halaman register umum (pilih daftar sebagai pembeli atau organisasi)
Route::get('/register', function () {
    return view('register.register'); // halaman pilihan register
})->name('register');

// Halaman register pembeli
Route::get('/register/pembeli', function () {
    return view('register.registerPembeli');
})->name('register.pembeli.form');

// Halaman register organisasi
Route::get('/register/organisasi', function () {
    return view('register.registerOrganisasi');
})->name('register.organisasi.form');

// Proses register pembeli
Route::post('/register/pembeli', [AuthController::class, 'registerPembeli'])->name('register.pembeli.submit');

// Proses register organisasi
Route::post('/register/organisasi', [AuthController::class, 'registerOrganisasi'])->name('register.organisasi.submit');

Route::middleware(['auth:pembeli'])->group(function () {
    Route::get('/dashboard', [PembeliController::class, 'dashboard'])->name('dashboard.pembeli');
    Route::get('/profile', [PembeliController::class, 'profile'])->name('pembeli.profile');
    Route::get('/history', [PembeliController::class, 'historyTransaksi'])->name('pembeli.history');
});

// Dashboard organisasi, pakai middleware auth dengan guard organisasi
Route::middleware(['auth:organisasi'])->group(function () {
    Route::get('/dashboard-organisasi', function () {
        return view('organisasi.dashboardOrganisasi');
    })->name('dashboard.organisasi');
});

// Dashboard admin/pegawai, pakai middleware auth dengan guard pegawai
Route::middleware(['auth:pegawai'])->group(function () {
    Route::get('/dashboard-admin', function () {
        return view('admin.dashboardAdmin');
    })->name('dashboard.admin');
});

// Detail barang titipan (bisa diakses tanpa login)
Route::get('/barang/{id}', [BarangTitipanController::class, 'showDetail'])->name('barang.show');

// Halaman Customer Service menampilkan list penitip dan form CRUD
Route::get('/customer-service', function () {
    $penitips = \App\Models\Penitip::all();
    return view('customerService.customerService', compact('penitips'));
})->name('customerService.index');

Route::resource('penitip', PenitipController::class);

