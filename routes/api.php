<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrganisasiController;
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\PenitipController;
use App\Http\Controllers\DonasiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RequestController;



// Gunakan Sanctum Auth middleware untuk route yang memerlukan autentikasi
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Route untuk login
Route::post('/login', [AuthController::class, 'login']);

// Route untuk resource organisasi
Route::prefix('organisasi')->middleware('auth:sanctum')->group(function () {
    Route::get('/search/{keyword}', [OrganisasiController::class, 'search']); // cari organisasi
    Route::get('/', [OrganisasiController::class, 'index']);                   // tampilkan semua organisasi
    Route::post('/', [OrganisasiController::class, 'store']);                  // tambah organisasi baru
    Route::get('/{id}', [OrganisasiController::class, 'show']);                // tampilkan organisasi tertentu
    Route::put('/{id}', [OrganisasiController::class, 'update']);              // perbarui organisasi
    Route::delete('/{id}', [OrganisasiController::class, 'destroy']);          // hapus organisasi
});

// Route untuk resource pembeli
Route::prefix('pembeli')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [PembeliController::class, 'index']);            // tampilkan semua pembeli
    Route::post('/', [PembeliController::class, 'store']);           // tambah pembeli baru
    Route::get('/{pembeli}', [PembeliController::class, 'show']);    // tampilkan pembeli tertentu
    Route::put('/{pembeli}', [PembeliController::class, 'update']);  // perbarui pembeli
    Route::delete('/{pembeli}', [PembeliController::class, 'destroy']); // hapus pembeli
});

// Route untuk penitip
Route::prefix('penitip')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [PenitipController::class, 'index']);               // tampilkan semua penitip
    Route::post('/', [PenitipController::class, 'store']);              // tambah penitip baru
    Route::get('/{penitip}', [PenitipController::class, 'show']);       // tampilkan penitip tertentu
    Route::put('/{penitip}', [PenitipController::class, 'update']);     // perbarui penitip
    Route::delete('/{penitip}', [PenitipController::class, 'destroy']); // hapus penitip
});

// Route untuk donasi
Route::prefix('donasi')->group(function () {
    Route::get('/', [DonasiController::class, 'index']);             // tampilkan semua donasi
    Route::post('/', [DonasiController::class, 'store']);            // tambah donasi baru
    Route::get('/{id}', [DonasiController::class, 'show']);          // tampilkan detail donasi
    Route::put('/{id}', [DonasiController::class, 'update']);        // perbarui donasi
    Route::delete('/{id}', [DonasiController::class, 'destroy']);    // hapus donasi
});


Route::prefix('request')->group(function () {
    Route::get('/search/{keyword}', [RequestController::class, 'search']); // cari request
    Route::get('/', [RequestController::class, 'index']);                  // tampilkan semua request
    Route::post('/', [RequestController::class, 'store']);                 // tambah request baru
    Route::get('/{id}', [RequestController::class, 'show']);              // tampilkan request tertentu
    Route::put('/{id}', [RequestController::class, 'update']);            // perbarui request
    Route::delete('/{id}', [RequestController::class, 'destroy']);        // hapus request
});
