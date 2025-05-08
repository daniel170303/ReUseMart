<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangTitipanController;
use App\Http\Controllers\PegawaiController;

use App\Models\BarangTitipan;
use App\Models\Pegawai;

Route::get('/', function () {
    $barangTitipan = BarangTitipan::take(3)->get();
    // ambil 6 barang terbaru
    return view('landingPage.landingPage', compact('barangTitipan'));
});

Route::get('/login', function () {
    return view('login.login');
})->name('login');

Route::apiResource('/pegawai', PegawaiController::class);


// Route::get('/', function () {
//     return view('landingPage\landingPage');
// });

