<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrganisasiController;

// Route untuk user login (jika pakai sanctum atau auth lainnya)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route untuk resource organisasi
Route::prefix('organisasi')->group(function () {
    Route::get('/search/{keyword}', [OrganisasiController::class, 'search']); // cari organisasi
    Route::get('/', [OrganisasiController::class, 'index']);                   // tampilkan semua organisasi
    Route::post('/', [OrganisasiController::class, 'store']);                  // tambah organisasi baru
    Route::get('/{id}', [OrganisasiController::class, 'show']);                // tampilkan organisasi tertentu
    Route::put('/{id}', [OrganisasiController::class, 'update']);              // perbarui organisasi
    Route::delete('/{id}', [OrganisasiController::class, 'destroy']);          // hapus organisasi
});
