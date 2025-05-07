<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangTitipanController;
use App\Http\Controllers\PegawaiController;
use App\Models\BarangTitipan;

// Menggunakan middleware api dan menambahkan prefix 'api'
Route::prefix('api')->middleware('api')->group(function () {

    // Rute untuk mengambil 3 barang titipan terbaru
    Route::get('/', function () {
        $barangTitipan = BarangTitipan::take(3)->get(); // Ambil 3 barang titipan terbaru
        return response()->json($barangTitipan); // Kirim dalam bentuk JSON
    });

    // Rute API untuk Pegawai
    Route::apiResource('/pegawai', PegawaiController::class);

    // Rute API untuk Barang Titipan
    Route::apiResource('/barang-titipan', BarangTitipanController::class);
});
