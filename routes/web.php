<?php

use Illuminate\Support\Facades\Route;
use App\Models\BarangTitipan;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AuthController;

// Halaman Utama (Landing Page)
Route::get('/', function () {
    // Ambil 3 barang titipan terbaru
    $barangTitipan = BarangTitipan::take(3)->get();

    // Kirim data barangTitipan ke view
    return view('landingPage.landingPage', compact('barangTitipan'));
});

// Halaman Login (GET request)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

// Halaman Login (POST request) untuk autentikasi
Route::post('/login', [LoginController::class, 'login'])->name('login');

// Halaman Registrasi (GET request)
Route::get('/register', function () {
    return view('auth.register');
});

// Halaman Logout (GET request)
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Dashboard routes - tambahkan route untuk dashboard sesuai role
Route::middleware('auth')->group(function() {
    Route::get('/admin/dashboard', function() {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    Route::get('/pegawai/dashboard', function() {
        return view('pegawai.dashboard');
    })->name('pegawai.dashboard');
    
    Route::get('/owner/dashboard', function() {
        return view('owner.dashboard');
    })->name('owner.dashboard');
    
    Route::get('/gudang/dashboard', function() {
        return view('gudang.dashboard');
    })->name('gudang.dashboard');
    
    Route::get('/cs/dashboard', function() {
        return view('cs.dashboard');
    })->name('cs.dashboard');
    
    Route::get('/penitip/dashboard', function() {
        return view('penitip.dashboard');
    })->name('penitip.dashboard');
    
    Route::get('/pembeli/dashboard', function() {
        return view('pembeli.dashboard');
    })->name('pembeli.dashboard');
    
    Route::get('/organisasi/dashboard', function() {
        return view('organisasi.dashboard');
    })->name('organisasi.dashboard');
});