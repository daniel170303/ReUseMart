<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrganisasiController;

Route::prefix('organisasi')->group(function () {
    Route::get('/search/{keyword}', [OrganisasiController::class, 'search']);
    Route::get('/', [OrganisasiController::class, 'index']);
    Route::post('/', [OrganisasiController::class, 'store']);
    Route::get('/{id}', [OrganisasiController::class, 'show']);
    Route::put('/{id}', [OrganisasiController::class, 'update']);
    Route::delete('/{id}', [OrganisasiController::class, 'destroy']);
});
