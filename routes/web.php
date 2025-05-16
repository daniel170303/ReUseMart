<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangTitipanController;
use App\Http\Controllers\AuthController;
use App\Models\BarangTitipan;
<<<<<<< HEAD
use App\Http\Controllers\DiskusiProdukController;
use App\Http\Controllers\Admin\DashboardController;



// // Menggunakan middleware api dan menambahkan prefix 'api'
// Route::prefix('api')->middleware('api')->group(function () {


Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

=======

// // Menggunakan middleware api dan menambahkan prefix 'api'
// Route::prefix('api')->middleware('api')->group(function () {
>>>>>>> origin/pipi

Route::get('/', function () {
    $barangTitipan = BarangTitipan::take(3)->get();
    return view('landingPage.landingPage', compact('barangTitipan'));
});

Route::get('/login', function () {
    return view('login.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login']);

<<<<<<< HEAD
Route::get('/admin', function () {
    return view('admin.dashboard');
})->name('admin');

Route::get('/barang/{id}', [BarangTitipanController::class, 'showDetail'])->name('barang.show');

    // // Rute API untuk Pegawai
    // Route::apiResource('/pegawai', PegawaiController::class);

    // // Rute API untuk Barang Titipan
    // Route::apiResource('/barang-titipan', BarangTitipanController::class);
// });


Route::post('/diskusi/{id_barang}', [DiskusiProdukController::class, 'store'])->name('diskusi.store');
=======
// Tampilkan halaman register
Route::get('/register', function () {
    return view('register.register');
})->name('register');

// Proses data register
Route::post('/register', [AuthController::class, 'register']);


Route::get('/admin', function () {
    return view('admin.admin');
})->name('admin');

Route::get('/barang/{id}', [BarangTitipanController::class, 'showDetail'])->name('barang.show');
>>>>>>> origin/pipi
