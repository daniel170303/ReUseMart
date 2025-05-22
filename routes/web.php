<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangTitipanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PenitipController;
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\TransaksiController;
use App\Models\BarangTitipan;

// Landing page, tampilkan 3 barang titipan
Route::get('/', function () {
    $barangTitipan = BarangTitipan::take(3)->get();
    return view('landingPage.landingPage', compact('barangTitipan'));
});

// Route untuk menampilkan form
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/register', function () {
    return view('register.register');
})->name('register');
Route::get('/register/pembeli', function () {
    return view('register.registerPembeli');
})->name('register.pembeli.form');
Route::get('/register/organisasi', function () {
    return view('register.registerOrganisasi');
})->name('register.organisasi.form');

// Route untuk proses registrasi
Route::post('/register/pembeli', [AuthController::class, 'registerPembeli'])->name('register.pembeli.submit');
Route::post('/register/organisasi', [AuthController::class, 'registerOrganisasi'])->name('register.organisasi.submit');

// Route untuk proses login
Route::post('/login/penitip', [AuthController::class, 'loginPenitip'])->name('login.penitip');
Route::post('/login/pembeli', [AuthController::class, 'loginPembeli'])->name('login.pembeli');
Route::post('/login/organisasi', [AuthController::class, 'loginOrganisasi'])->name('login.organisasi');
Route::post('/login/pegawai', [AuthController::class, 'loginPegawai'])->name('login.pegawai');

// Route untuk logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard routes (contoh, sesuaikan dengan aplikasi Anda)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard/penitip', function () {
        return view('dashboard.penitip');
    })->name('dashboardPenitip');
    
    Route::get('/dashboard/pembeli', function () {
        return view('dashboard.pembeli');
    })->name('pembeli.dashboardPembeli');
    
    Route::get('/dashboard/organisasi', function () {
        return view('dashboard.organisasi');
    })->name('dashboardOrganisasi');
    
    Route::get('/dashboard/admin', function () {
        return view('dashboard.admin');
    })->name('dashboardAdmin');
    
    Route::get('/dashboard/owner', function () {
        return view('dashboard.owner');
    })->name('dashboardOwner');
    
    Route::get('/dashboard/cs', function () {
        return view('dashboard.cs');
    })->name('dashboardCs');
    
    Route::get('/dashboard/gudang', function () {
        return view('dashboard.gudang');
    })->name('dashboardGudang');
});