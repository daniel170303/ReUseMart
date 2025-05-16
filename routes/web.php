<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangTitipanController;
use App\Http\Controllers\AuthController;
use App\Models\BarangTitipan;
use App\Http\Controllers\DiskusiProdukController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\DonasiController;
use App\Http\Controllers\OrganisasiController;

// Halaman landing page, ambil 3 barang titipan yang belum ada transaksi
Route::get('/', function () {
    $barangTitipan = BarangTitipan::whereDoesntHave('transaksi')->take(3)->get();
    return view('landingPage.landingPage', compact('barangTitipan'));
});

// Cek garansi
Route::get('/cek-garansi', [BarangTitipanController::class, 'cekGaransi'])->name('cek.garansi');

// Halaman login & proses login
Route::get('/login', function () {
    return view('login.login');
})->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Halaman admin
Route::get('/admin', function () {
    return view('admin.admin');
})->name('admin');

// Halaman register & proses register
Route::get('/register', function () {
    return view('register.register');
})->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Detail barang titipan
Route::get('/barang/{id}', [BarangTitipanController::class, 'showDetail'])->name('barang.show');

// Halaman owner untuk kelola request & donasi
Route::get('/owner', [RequestController::class, 'ownerPage'])->name('owner.page');

// Terima request donasi
Route::post('/request/terima/{id_request}', [RequestController::class, 'terimaRequest'])->name('request.terima');

// Update data donasi (via form)
Route::put('/donasi/{id}/update', [DonasiController::class, 'update'])->name('donasi.update');

// Diskusi produk
Route::post('/diskusi/{id_barang}', [DiskusiProdukController::class, 'store'])->name('diskusi.store');

//Route::middleware(['auth', 'role:organisasi'])->group(function () {
Route::get('/organisasi/request-barang', [RequestController::class, 'index'])->name('organisasi.requestBarang.index');
Route::get('/organisasi/request-barang/create', [RequestController::class, 'create'])->name('organisasi.requestBarang.create');
Route::post('/organisasi/request-barang', [RequestController::class, 'store'])->name('organisasi.requestBarang.store');

Route::get('/organisasi/request-barang/{id}/edit', [RequestController::class, 'edit'])->name('organisasi.requestBarang.edit');
Route::put('/organisasi/request-barang/{id}', [RequestController::class, 'update'])->name('organisasi.requestBarang.update');
Route::delete('/organisasi/request-barang/{id}', [RequestController::class, 'destroy'])->name('organisasi.requestBarang.destroy');

Route::get('/organisasi/request-barang/search', [RequestController::class, 'search'])->name('organisasi.requestBarang.search');

Route::get('/organisasi/request-barang/{id}/edit', [RequestController::class, 'edit'])->name('organisasi.requestBarang.edit');
Route::put('/organisasi/request-barang/{id}', [RequestController::class, 'update'])->name('organisasi.requestBarang.update');

//});

Route::get('/barang-titipan', [BarangTitipanController::class, 'index'])->name('barang_titipan.index');