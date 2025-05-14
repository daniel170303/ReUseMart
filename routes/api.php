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
    TransaksiController
};

// Auth Routes (login & register) - Tidak perlu autentikasi
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected Routes - hanya bisa diakses jika sudah login
Route::middleware('auth:sanctum')->group(function () {
    // Resource routes
    Route::apiResource('barang-titipan', BarangTitipanController::class);
    Route::apiResource('organisasi', OrganisasiController::class);
    Route::apiResource('pegawai', PegawaiController::class);
    Route::apiResource('pembeli', PembeliController::class);
    Route::apiResource('penitip', PenitipController::class);
    Route::apiResource('request', RequestController::class);
    Route::apiResource('reward-pembeli', RewardPembeliController::class);
    Route::apiResource('role-pegawai', RolePegawaiController::class);
    Route::apiResource('transaksi', TransaksiController::class);

    // Logout & ambil user info aktif
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return $request->user();
    });
});
