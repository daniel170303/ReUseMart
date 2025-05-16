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
    GambarBarangTitipanController,
    DonasiController
};

// Auth Routes (login & register) - Tidak perlu autentikasi
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// === ROUTE PENITIP AUTH ===
// Login penitip
Route::post('/penitip/login', [AuthController::class, 'loginPenitip']);

// Group route penitip yang membutuhkan autentikasi Sanctum
Route::middleware('auth:sanctum')->prefix('penitip')->group(function () {
    // Logout penitip
    Route::post('/logout', [AuthController::class, 'logoutPenitip']);

    // CRUD penitip
    Route::get('/', [PenitipController::class, 'index']);
    Route::get('/{id}', [PenitipController::class, 'show']);
    Route::put('/{id}', [PenitipController::class, 'update']);
    Route::delete('/{id}', [PenitipController::class, 'destroy']);
    Route::get('/search/{keyword}', [PenitipController::class, 'search']);
});


// Protected Routes - hanya bisa diakses jika sudah login
Route::middleware('auth:sanctum')->group(function () {
    
    // Route untuk Pegawai
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

    // Route untuk Request
    Route::prefix('request')->group(function () {
        Route::get('/search/{keyword}', [RequestController::class, 'search']);
        Route::get('/', [RequestController::class, 'index']);                 
        Route::post('/', [RequestController::class, 'store']);                
        Route::get('/{id}', [RequestController::class, 'show']);             
        Route::put('/{id}', [RequestController::class, 'update']);           
        Route::delete('/{id}', [RequestController::class, 'destroy']);       
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
        Route::get('/', [TransaksiController::class, 'index']);              // Semua transaksi
        Route::post('/', [TransaksiController::class, 'store']);             // Simpan transaksi baru
        Route::get('/{id}', [TransaksiController::class, 'show']);           // Detail transaksi
        Route::put('/{id}', [TransaksiController::class, 'update']);         // Update transaksi
        Route::delete('/{id}', [TransaksiController::class, 'destroy']);     // Hapus transaksi
        Route::get('/search/{keyword}', [TransaksiController::class, 'search']); // Cari transaksi
    });

    Route::prefix('gambar-barang-titipan')->group(function () {
        Route::get('/{barangId}', [GambarBarangTitipanController::class, 'index']); // List gambar per barang titipan
        Route::post('/', [GambarBarangTitipanController::class, 'store']);          // Upload banyak gambar sekaligus
        Route::delete('/destroy/{id}', [GambarBarangTitipanController::class, 'destroy']);  // Hapus gambar
    });

    Route::prefix('donasi')->group(function () {
        Route::get('/', [DonasiController::class, 'index']);            // GET semua donasi
        Route::get('/{id}', [DonasiController::class, 'show']);         // GET satu donasi berdasarkan ID
        Route::post('/', [DonasiController::class, 'store']);           // POST tambah donasi
        Route::put('/{id}', [DonasiController::class, 'update']);       // PUT update donasi
        Route::delete('/{id}', [DonasiController::class, 'destroy']);   // DELETE donasi
        Route::get('/search/{keyword}', [DonasiController::class, 'search']); // GET pencarian donasi
    });

    // Logout & ambil user info aktif
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return $request->user();
    });
});