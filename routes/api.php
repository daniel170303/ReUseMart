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

/// === LOGIN ROUTES ===
// Pegawai (admin, owner, cs, gudang) login
Route::post('/pegawai/login', [AuthController::class, 'loginPegawai']);

// Penitip login
Route::post('/penitip/login', [AuthController::class, 'loginPenitip']);

// Pembeli register + login
Route::post('/pembeli/register', [AuthController::class, 'registerPembeli']);
Route::post('/pembeli/login', [AuthController::class, 'loginPembeli']);

// Organisasi register + login
Route::post('/organisasi/register', [AuthController::class, 'registerOrganisasi']);
Route::post('/organisasi/login', [AuthController::class, 'loginOrganisasi']);


// === ROUTES WITH AUTHENTICATION (sanctum) ===
Route::middleware('auth:sanctum')->group(function () {
    
    // Logout (all user types)
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Penitip routes
    Route::prefix('penitip')->group(function () {
        Route::get('/', [PenitipController::class, 'index']);
        Route::post('/', [PenitipController::class, 'store']);
        Route::get('/{id}', [PenitipController::class, 'show']);
        Route::put('/{id}', [PenitipController::class, 'update']);
        Route::delete('/{id}', [PenitipController::class, 'destroy']);
        Route::get('/search/{keyword}', [PenitipController::class, 'search']);
    });

    // Pembeli routes
    Route::prefix('pembeli')->group(function () {
        Route::get('/', [PembeliController::class, 'index']);
        Route::post('/', [PembeliController::class, 'store']);
        Route::get('/{id}', [PembeliController::class, 'show']);
        Route::put('/{id}', [PembeliController::class, 'update']);
        Route::delete('/{id}', [PembeliController::class, 'destroy']);
        Route::get('/search/{keyword}', [PembeliController::class, 'search']);
    });

    // Organisasi routes
    Route::prefix('organisasi')->group(function () {
        Route::get('/', [OrganisasiController::class, 'index']);
        Route::post('/', [OrganisasiController::class, 'store']);
        Route::get('/{id}', [OrganisasiController::class, 'show']);
        Route::put('/{id}', [OrganisasiController::class, 'update']);
        Route::delete('/{id}', [OrganisasiController::class, 'destroy']);
        Route::get('/search/{keyword}', [OrganisasiController::class, 'search']);
    });

    // Pegawai routes
    Route::prefix('pegawai')->group(function () {
        Route::get('/', [PegawaiController::class, 'index']);
        Route::post('/', [PegawaiController::class, 'store']);
        Route::get('/{id}', [PegawaiController::class, 'show']);
        Route::put('/{id}', [PegawaiController::class, 'update']);
        Route::delete('/{id}', [PegawaiController::class, 'destroy']);
        Route::get('/search/{keyword}', [PegawaiController::class, 'search']);
    });

    // Barang Titipan routes
    Route::prefix('barang-titipan')->group(function () {
        Route::get('/', [BarangTitipanController::class, 'index']);             // Tampilkan semua barang titipan
        Route::post('/', [BarangTitipanController::class, 'store']);            // Tambah barang titipan baru
        Route::get('/{id}', [BarangTitipanController::class, 'show']);          // Tampilkan detail barang titipan
        Route::put('/{id}', [BarangTitipanController::class, 'update']);        // Perbarui barang titipan
        Route::delete('/{id}', [BarangTitipanController::class, 'destroy']);    // Hapus barang titipan
        Route::get('/search/{keyword}', [BarangTitipanController::class, 'search']);  // Cari barang titipan
    });

    // Request Routes
    Route::prefix('request')->group(function () {
        Route::get('/', [RequestController::class, 'index']);             // Tampilkan semua request
        Route::post('/', [RequestController::class, 'store']);            // Tambah request baru
        Route::get('/{id}', [RequestController::class, 'show']);          // Tampilkan request tertentu
        Route::put('/{id}', [RequestController::class, 'update']);        // Perbarui request
        Route::delete('/{id}', [RequestController::class, 'destroy']);    // Hapus request
    });

    // Reward Pembeli Routes
    Route::prefix('reward-pembeli')->group(function () {
        Route::get('/', [RewardPembeliController::class, 'index']);       // Tampilkan semua reward pembeli
        Route::post('/', [RewardPembeliController::class, 'store']);      // Tambah reward pembeli baru
        Route::get('/{id}', [RewardPembeliController::class, 'show']);    // Tampilkan reward pembeli tertentu
        Route::put('/{id}', [RewardPembeliController::class, 'update']);  // Perbarui reward pembeli
        Route::delete('/{id}', [RewardPembeliController::class, 'destroy']); // Hapus reward pembeli
    });

    // Transaksi Routes
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
        Route::delete('/destroy/{id}', [GambarBarangTitipanController::class, 'destroy']);  // Hapus gambar
    });

    // // Logout & ambil user info aktif
    // Route::post('/logout', [AuthController::class, 'logout']);
    // Route::get('/user', function (\Illuminate\Http\Request $request) {
    //     return $request->user();
    // });
});