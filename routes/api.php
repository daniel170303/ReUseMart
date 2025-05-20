<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\{
    BarangTitipanController,
    OrganisasiController,
    PegawaiController,
    PembeliController,
    PenitipController,
    RequestController,
    RewardPembeliController,
    RolePegawaiController,
    TransaksiController,
    GambarBarangTitipanController
};

// === AUTH ROUTES (TIDAK PERLU AUTENTIKASI) ===

// Satu endpoint login untuk semua role (deteksi otomatis)
Route::post('/login', [AuthController::class, 'apiLogin']);

// Register endpoint yang mendukung berbagai role
Route::post('/register', [AuthController::class, 'apiRegister']);

// Protected Routes - hanya bisa diakses jika sudah login
Route::middleware('auth:sanctum')->group(function () {
    
    // Logout & ambil user info aktif
    Route::post('/logout', [AuthController::class, 'apiLogout']);
    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return $request->user();
    });
    
    // Route untuk Pegawai
    Route::prefix('pegawai')->middleware('role:admin,owner,pegawai')->group(function () {
        Route::get('/', [PegawaiController::class, 'index']);
        Route::post('/', [PegawaiController::class, 'store']);
        Route::get('/{id}', [PegawaiController::class, 'show']);
        Route::put('/{id}', [PegawaiController::class, 'update']);
        Route::delete('/{id}', [PegawaiController::class, 'destroy']);
        Route::get('/search/{keyword}', [PegawaiController::class, 'search']);
    });

    // Route untuk Barang Titipan
    Route::prefix('barang-titipan')->group(function () {
        Route::get('/', [BarangTitipanController::class, 'index']);
        Route::post('/', [BarangTitipanController::class, 'store'])->middleware('role:admin,owner,penitip');
        Route::get('/{id}', [BarangTitipanController::class, 'show']);
        Route::put('/{id}', [BarangTitipanController::class, 'update'])->middleware('role:admin,owner,penitip');
        Route::delete('/{id}', [BarangTitipanController::class, 'destroy'])->middleware('role:admin,owner,penitip');
        Route::get('/search/{keyword}', [BarangTitipanController::class, 'search']);
    });

    // Route untuk Organisasi
    Route::prefix('organisasi')->middleware('role:admin,owner')->group(function () {
        Route::get('/', [OrganisasiController::class, 'index']);
        Route::post('/', [OrganisasiController::class, 'store']);
        Route::get('/{id}', [OrganisasiController::class, 'show']);
        Route::put('/{id}', [OrganisasiController::class, 'update']);
        Route::delete('/{id}', [OrganisasiController::class, 'destroy']);
        Route::get('/search/{keyword}', [OrganisasiController::class, 'search']);
    });

    // Route untuk Penitip
    Route::prefix('penitip')->group(function () {
        Route::get('/', [PenitipController::class, 'index'])->middleware('role:admin,owner,pegawai');
        Route::post('/', [PenitipController::class, 'store'])->middleware('role:admin,owner,pegawai');
        Route::get('/{id}', [PenitipController::class, 'show'])->middleware('role:admin,owner,pegawai,penitip');
        Route::put('/{id}', [PenitipController::class, 'update'])->middleware('role:admin,owner,pegawai,penitip');
        Route::delete('/{id}', [PenitipController::class, 'destroy'])->middleware('role:admin,owner');
        Route::get('/search/{keyword}', [PenitipController::class, 'search'])->middleware('role:admin,owner,pegawai');
        
        // Endpoint untuk data penitip yang login
        Route::get('/profile/me', [PenitipController::class, 'profile'])->middleware('role:penitip');
    });

    // Route untuk Pembeli
    Route::prefix('pembeli')->group(function () {
        Route::get('/', [PembeliController::class, 'index']);
        Route::post('/', [PembeliController::class, 'store']);
        Route::get('/{id}', [PembeliController::class, 'show']);
        Route::put('/{id}', [PembeliController::class, 'update']);
        Route::delete('/{id}', [PembeliController::class, 'destroy']);
        Route::get('/search/{keyword}', [PembeliController::class, 'search']);
        
        // Endpoint untuk data pembeli yang login
        Route::get('/profile/me', [PembeliController::class, 'profile'])->middleware('role:pembeli');
    });

    // Route untuk Request
    Route::prefix('request')->group(function () {
        Route::get('/', [RequestController::class, 'index']);
        Route::post('/', [RequestController::class, 'store'])->middleware('role:organisasi,admin,owner');
        Route::get('/{id}', [RequestController::class, 'show']);
        Route::put('/{id}', [RequestController::class, 'update'])->middleware('role:organisasi,admin,owner');
        Route::delete('/{id}', [RequestController::class, 'destroy'])->middleware('role:admin,owner');
    });

    // Route untuk Reward Pembeli
    Route::prefix('reward-pembeli')->group(function () {
        Route::get('/', [RewardPembeliController::class, 'index'])->middleware('role:admin,owner,pegawai');
        Route::post('/', [RewardPembeliController::class, 'store'])->middleware('role:admin,owner,pegawai');
        Route::get('/{id}', [RewardPembeliController::class, 'show'])->middleware('role:admin,owner,pegawai,pembeli');
        Route::put('/{id}', [RewardPembeliController::class, 'update'])->middleware('role:admin,owner,pegawai');
        Route::delete('/{id}', [RewardPembeliController::class, 'destroy'])->middleware('role:admin,owner,pegawai');
        
        // Endpoint untuk melihat reward pembeli sendiri
        Route::get('/my-rewards', [RewardPembeliController::class, 'myRewards'])->middleware('role:pembeli');
    });

    // Route untuk Role Pegawai
    Route::prefix('role-pegawai')->middleware('role:admin,owner')->group(function () {
        Route::get('/', [RolePegawaiController::class, 'index']);
        Route::post('/', [RolePegawaiController::class, 'store']);
        Route::get('/{id}', [RolePegawaiController::class, 'show']);
        Route::put('/{id}', [RolePegawaiController::class, 'update']);
        Route::delete('/{id}', [RolePegawaiController::class, 'destroy']);
    });

    // Route untuk Transaksi
    Route::prefix('transaksi')->group(function () {
        Route::get('/', [TransaksiController::class, 'index'])->middleware('role:admin,owner,pegawai');
        Route::post('/', [TransaksiController::class, 'store'])->middleware('role:admin,owner,pegawai,pembeli');
        Route::get('/{id}', [TransaksiController::class, 'show'])->middleware('role:admin,owner,pegawai,pembeli,penitip');
        Route::put('/{id}', [TransaksiController::class, 'update'])->middleware('role:admin,owner,pegawai');
        Route::delete('/{id}', [TransaksiController::class, 'destroy'])->middleware('role:admin,owner');
        Route::get('/search/{keyword}', [TransaksiController::class, 'search'])->middleware('role:admin,owner,pegawai');
        
        // Endpoint untuk melihat transaksi pembeli sendiri
        Route::get('/my-transactions', [TransaksiController::class, 'myTransactions'])->middleware('role:pembeli');
        
        // Endpoint untuk melihat transaksi penitip sendiri
        Route::get('/my-sales', [TransaksiController::class, 'mySales'])->middleware('role:penitip');
    });

    // Route untuk Gambar Barang Titipan
    Route::prefix('gambar-barang-titipan')->group(function () {
        Route::get('/{barangId}', [GambarBarangTitipanController::class, 'index']);
        Route::post('/', [GambarBarangTitipanController::class, 'store'])->middleware('role:admin,owner,penitip');
        Route::delete('/destroy/{id}', [GambarBarangTitipanController::class, 'destroy'])->middleware('role:admin,owner,penitip');
    });
});


// Protected Routes - hanya bisa diakses jika sudah login
Route::middleware('auth:sanctum')->group(function () {
    
    // Route untuk admin saja
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        // Route untuk admin
    });
    
    // Route untuk admin atau owner
    Route::prefix('management')->middleware('role:admin,owner')->group(function () {
        // Route untuk manajemen
    });
    
    // Route untuk penitip saja
    Route::prefix('penitip-area')->middleware('role:penitip')->group(function () {
        // Route untuk penitip
    });
    
    // Route untuk pembeli saja
    Route::prefix('buyer-area')->middleware('role:pembeli')->group(function () {
        // Route untuk pembeli
    });
    
    // Route yang bisa diakses beberapa role
    Route::get('/product/{id}', [ProductController::class, 'show'])
        ->middleware('role:admin,penitip,pembeli');
});