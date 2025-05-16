<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrganisasiController;
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\PenitipController;
use App\Http\Controllers\DonasiController;
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
    TransaksiController
};

// Auth Routes (login & register) - Tidak perlu autentikasi
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Hanya bisa diakses kalau sudah login (pakai token)
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Gunakan Sanctum Auth middleware untuk route yang memerlukan autentikasi
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/// Route untuk resource organisasi
Route::prefix('organisasi')->group(function () {
    Route::get('/search/{keyword}', [OrganisasiController::class, 'search']); // cari organisasi
    Route::get('/', [OrganisasiController::class, 'index']);                   // tampilkan semua organisasi
    Route::post('/', [OrganisasiController::class, 'store']);                  // tambah organisasi baru
    Route::get('/{id}', [OrganisasiController::class, 'show']);                // tampilkan organisasi tertentu
    Route::put('/{id}', [OrganisasiController::class, 'update']);              // perbarui organisasi
    Route::delete('/{id}', [OrganisasiController::class, 'destroy']);          // hapus organisasi
});

// Route untuk resource pembeli
Route::prefix('pembeli')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [PembeliController::class, 'index']);           
    Route::post('/', [PembeliController::class, 'store']);          
    Route::get('/{pembeli}', [PembeliController::class, 'show']);   
    Route::put('/{pembeli}', [PembeliController::class, 'update']); 
    Route::delete('/{pembeli}', [PembeliController::class, 'destroy']);
});

// Route untuk penitip
Route::prefix('penitip')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [PenitipController::class, 'index']);              
    Route::post('/', [PenitipController::class, 'store']);             
    Route::get('/{penitip}', [PenitipController::class, 'show']);      
    Route::put('/{penitip}', [PenitipController::class, 'update']);    
    Route::delete('/{penitip}', [PenitipController::class, 'destroy']);
});

// Route untuk donasi
Route::prefix('donasi')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [DonasiController::class, 'index']);            
    Route::post('/', [DonasiController::class, 'store']);           
    Route::get('/{id}', [DonasiController::class, 'show']);         
    Route::put('/{id}', [DonasiController::class, 'update']);       
    Route::delete('/{id}', [DonasiController::class, 'destroy']);   
});

    // Route untuk Reward Pembeli
    Route::prefix('reward-pembeli')->group(function () {
        Route::get('/', [RewardPembeliController::class, 'index']);       // Tampilkan semua reward pembeli
        Route::post('/', [RewardPembeliController::class, 'store']);      // Tambah reward pembeli baru
        Route::get('/{id}', [RewardPembeliController::class, 'show']);    // Tampilkan reward pembeli tertentu
        Route::put('/{id}', [RewardPembeliController::class, 'update']);  // Perbarui reward pembeli
        Route::delete('/{id}', [RewardPembeliController::class, 'destroy']); // Hapus reward pembeli
    });

    // Route untuk Role Pegawai
    Route::prefix('role-pegawai')->group(function () {
        Route::get('/', [RolePegawaiController::class, 'index']);         // Tampilkan semua role pegawai
        Route::post('/', [RolePegawaiController::class, 'store']);        // Tambah role pegawai baru
        Route::get('/{id}', [RolePegawaiController::class, 'show']);      // Tampilkan role pegawai tertentu
        Route::put('/{id}', [RolePegawaiController::class, 'update']);    // Perbarui role pegawai
        Route::delete('/{id}', [RolePegawaiController::class, 'destroy']); // Hapus role pegawai
    });

    // Route untuk Transaksi
    Route::prefix('transaksi')->group(function () {
        Route::get('/', [TransaksiController::class, 'index']);           // Tampilkan semua transaksi
        Route::post('/', [TransaksiController::class, 'store']);          // Tambah transaksi baru
        Route::get('/{id}', [TransaksiController::class, 'show']);        // Tampilkan transaksi tertentu
        Route::put('/{id}', [TransaksiController::class, 'update']);      // Perbarui transaksi
        Route::delete('/{id}', [TransaksiController::class, 'destroy']);  // Hapus transaksi
        Route::get('/search/{keyword}', [TransaksiController::class, 'search']);  // Cari transaksi
    });

    Route::prefix('gambar-barang-titipan')->group(function () {
        Route::get('/{barangId}', [GambarBarangTitipanController::class, 'index']); // List gambar per barang titipan
        Route::post('/', [GambarBarangTitipanController::class, 'store']);          // Upload banyak gambar sekaligus
        Route::delete('/{id}', [GambarBarangTitipanController::class, 'destroy']);  // Hapus gambar
    });

    // Logout & ambil user info aktif
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return $request->user();
    });
// });
