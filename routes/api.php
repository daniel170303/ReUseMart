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

// Route auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes
// Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('barang-titipan', BarangTitipanController::class);
    Route::apiResource('organisasi', OrganisasiController::class);
    Route::apiResource('pegawai', PegawaiController::class);
    Route::apiResource('pembeli', PembeliController::class);
    Route::apiResource('penitip', PenitipController::class);
    Route::apiResource('request', RequestController::class);
    Route::apiResource('reward-pembeli', RewardPembeliController::class);
    Route::apiResource('role-pegawai', RolePegawaiController::class);
    Route::apiResource('transaksi', TransaksiController::class);

    // Optional: Logout & user info
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return $request->user();
    });
// });
