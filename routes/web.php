<?php

use Illuminate\Support\Facades\Route;
use App\Models\BarangTitipan;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AuthController;

// Halaman Utama (Landing Page)
Route::get('/', function () {
    // Ambil 3 barang titipan terbaru
    $barangTitipan = BarangTitipan::take(3)->get();

    // Kirim data barangTitipan ke view
    return view('landingPage.landingPage', compact('barangTitipan'));
});

Route::get('/login', function () {
    return view('login.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/admin', function () {
    return view('admin.admin');
})->name('admin');

    // // Rute API untuk Pegawai
    // Route::apiResource('/pegawai', PegawaiController::class);

    // // Rute API untuk Barang Titipan
    // Route::apiResource('/barang-titipan', BarangTitipanController::class);
// });
