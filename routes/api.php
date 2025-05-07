<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\BarangTitipanController;
use App\Http\Controllers\OrganisasiController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\PenitipController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\RewardPembeliController;
use App\Http\Controllers\RolePegawaiController;
use App\Http\Controllers\TransaksiController;

Route::middleware(['auth:sanctum'])->group(function () {

    // Barang Titipan
    Route::get('/barang-titipan', [BarangTitipanController::class, 'index']);
    Route::get('/barang-titipan/{id}', [BarangTitipanController::class, 'show']);
    Route::post('/barang-titipan', [BarangTitipanController::class, 'store']);
    Route::put('/barang-titipan/{id}', [BarangTitipanController::class, 'update']);
    Route::delete('/barang-titipan/{id}', [BarangTitipanController::class, 'destroy']);

    // Organisasi
    Route::get('/organisasi', [OrganisasiController::class, 'index']);
    Route::get('/organisasi/{id}', [OrganisasiController::class, 'show']);
    Route::post('/organisasi', [OrganisasiController::class, 'store']);
    Route::put('/organisasi/{id}', [OrganisasiController::class, 'update']);
    Route::delete('/organisasi/{id}', [OrganisasiController::class, 'destroy']);

    // Pegawai
    Route::get('/pegawai', [PegawaiController::class, 'index']);
    Route::get('/pegawai/{id}', [PegawaiController::class, 'show']);
    Route::post('/pegawai', [PegawaiController::class, 'store']);
    Route::put('/pegawai/{id}', [PegawaiController::class, 'update']);
    Route::delete('/pegawai/{id}', [PegawaiController::class, 'destroy']);

    // Pembeli
    Route::get('/pembeli', [PembeliController::class, 'index']);
    Route::get('/pembeli/{id}', [PembeliController::class, 'show']);
    Route::post('/pembeli', [PembeliController::class, 'store']);
    Route::put('/pembeli/{id}', [PembeliController::class, 'update']);
    Route::delete('/pembeli/{id}', [PembeliController::class, 'destroy']);

    // Penitip
    Route::get('/penitip', [PenitipController::class, 'index']);
    Route::get('/penitip/{id}', [PenitipController::class, 'show']);
    Route::post('/penitip', [PenitipController::class, 'store']);
    Route::put('/penitip/{id}', [PenitipController::class, 'update']);
    Route::delete('/penitip/{id}', [PenitipController::class, 'destroy']);

    // Request
    Route::get('/request', [RequestController::class, 'index']);
    Route::get('/request/{id}', [RequestController::class, 'show']);
    Route::post('/request', [RequestController::class, 'store']);
    Route::put('/request/{id}', [RequestController::class, 'update']);
    Route::delete('/request/{id}', [RequestController::class, 'destroy']);

    // Reward Pembeli
    Route::get('/reward-pembeli', [RewardPembeliController::class, 'index']);
    Route::get('/reward-pembeli/{id}', [RewardPembeliController::class, 'show']);
    Route::post('/reward-pembeli', [RewardPembeliController::class, 'store']);
    Route::put('/reward-pembeli/{id}', [RewardPembeliController::class, 'update']);
    Route::delete('/reward-pembeli/{id}', [RewardPembeliController::class, 'destroy']);

    // Role Pegawai
    Route::get('/role-pegawai', [RolePegawaiController::class, 'index']);
    Route::get('/role-pegawai/{id}', [RolePegawaiController::class, 'show']);
    Route::post('/role-pegawai', [RolePegawaiController::class, 'store']);
    Route::put('/role-pegawai/{id}', [RolePegawaiController::class, 'update']);
    Route::delete('/role-pegawai/{id}', [RolePegawaiController::class, 'destroy']);

    // Transaksi
    Route::get('/transaksi', [TransaksiController::class, 'index']);
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show']);
    Route::post('/transaksi', [TransaksiController::class, 'store']);
    Route::put('/transaksi/{id}', [TransaksiController::class, 'update']);
    Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy']);
});
