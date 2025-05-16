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

// Auth Routes (login & register) - Tidak perlu autentikasi
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected Routes - hanya bisa diakses jika sudah login
// Route::middleware('auth:sanctum')->group(function () {
    // Route untuk Pegawai
    Route::prefix('pegawai')->group(function () {
        Route::get('/', [PegawaiController::class, 'index']);             // Tampilkan semua pegawai
        Route::post('/', [PegawaiController::class, 'store']);            // Tambah pegawai baru
        Route::get('/{id}', [PegawaiController::class, 'show']);          // Tampilkan pegawai tertentu
        Route::put('/{id}', [PegawaiController::class, 'update']);        // Perbarui pegawai
        Route::delete('/{id}', [PegawaiController::class, 'destroy']);    // Hapus pegawai
        Route::get('/search/{keyword}', [PegawaiController::class, 'search']); // Cari pegawai berdasarkan kata kunci
    });

    // Route untuk Barang Titipan
    Route::prefix('barang-titipan')->group(function () {
        Route::get('/', [BarangTitipanController::class, 'index']);             // Tampilkan semua barang titipan
        Route::post('/', [BarangTitipanController::class, 'store']);            // Tambah barang titipan baru
        Route::get('/{id}', [BarangTitipanController::class, 'show']);          // Tampilkan detail barang titipan
        Route::put('/{id}', [BarangTitipanController::class, 'update']);        // Perbarui barang titipan
        Route::delete('/{id}', [BarangTitipanController::class, 'destroy']);    // Hapus barang titipan
        Route::get('/search/{keyword}', [BarangTitipanController::class, 'search']);  // Cari barang titipan
    });

    // Route untuk Organisasi
    Route::prefix('organisasi')->group(function () {
        Route::get('/', [OrganisasiController::class, 'index']);            // Tampilkan semua organisasi
        Route::post('/', [OrganisasiController::class, 'store']);           // Tambah organisasi baru
        Route::get('/{id}', [OrganisasiController::class, 'show']);         // Tampilkan organisasi tertentu
        Route::put('/{id}', [OrganisasiController::class, 'update']);       // Perbarui organisasi
        Route::delete('/{id}', [OrganisasiController::class, 'destroy']);   // Hapus organisasi
        Route::get('/search/{keyword}', [OrganisasiController::class, 'search']);  // Cari organisasi
    });

    // Route untuk Pembeli
    Route::prefix('pembeli')->group(function () {
        Route::get('/', [PembeliController::class, 'index']);               // Tampilkan semua pembeli
        Route::post('/', [PembeliController::class, 'store']);              // Tambah pembeli baru
        Route::get('/{id}', [PembeliController::class, 'show']);            // Tampilkan pembeli tertentu
        Route::put('/{id}', [PembeliController::class, 'update']);          // Perbarui pembeli
        Route::delete('/{id}', [PembeliController::class, 'destroy']);      // Hapus pembeli
        Route::get('/search/{keyword}', [PembeliController::class, 'search']); // Cari pembeli
    });

    // Route untuk Penitip
    Route::prefix('penitip')->group(function () {
        Route::get('/', [PenitipController::class, 'index']);              // Tampilkan semua penitip
        Route::post('/', [PenitipController::class, 'store']);             // Tambah penitip baru
        Route::get('/{id}', [PenitipController::class, 'show']);           // Tampilkan penitip tertentu
        Route::put('/{id}', [PenitipController::class, 'update']);         // Perbarui penitip
        Route::delete('/{id}', [PenitipController::class, 'destroy']);     // Hapus penitip
        Route::get('/search/{keyword}', [PenitipController::class, 'search']);  // Cari penitip
    });

    // Route untuk Request
    Route::prefix('request')->group(function () {
        Route::get('/', [RequestController::class, 'index']);             // Tampilkan semua request
        Route::post('/', [RequestController::class, 'store']);            // Tambah request baru
        Route::get('/{id}', [RequestController::class, 'show']);          // Tampilkan request tertentu
        Route::put('/{id}', [RequestController::class, 'update']);        // Perbarui request
        Route::delete('/{id}', [RequestController::class, 'destroy']);    // Hapus request
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
        Route::delete('/destroy/{id}', [GambarBarangTitipanController::class, 'destroy']);  // Hapus gambar
    });

    // Logout & ambil user info aktif
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return $request->user();
    });
// });