<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrganisasiController;
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\PenitipController;
use App\Http\Controllers\DonasiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RequestController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Hanya bisa diakses kalau sudah login (pakai token)
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Gunakan Sanctum Auth middleware untuk route yang memerlukan autentikasi
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route untuk resource organisasi
Route::prefix('organisasi')->middleware('auth:sanctum')->group(function () {
    Route::get('/search/{keyword}', [OrganisasiController::class, 'search']); 
    Route::get('/', [OrganisasiController::class, 'index']);                  
    Route::post('/', [OrganisasiController::class, 'store']);                 
    Route::get('/{id}', [OrganisasiController::class, 'show']);               
    Route::put('/{id}', [OrganisasiController::class, 'update']);             
    Route::delete('/{id}', [OrganisasiController::class, 'destroy']);         
});

// Route untuk resource pembeli
Route::prefix('pembeli')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [PembeliController::class, 'index']);           
    Route::post('/', [PembeliController::class, 'store']);          
    Route::get('/{pembeli}', [PembeliController::class, 'show']);   
    Route::put('/{pembeli}', [PembeliController::class, 'update']); 
    Route::delete('/{pembeli}', [PembeliController::class, 'destroy']);
});

// Route untuk penitip
Route::prefix('penitip')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [PenitipController::class, 'index']);              
    Route::post('/', [PenitipController::class, 'store']);             
    Route::get('/{penitip}', [PenitipController::class, 'show']);      
    Route::put('/{penitip}', [PenitipController::class, 'update']);    
    Route::delete('/{penitip}', [PenitipController::class, 'destroy']);
});

// Route untuk donasi
Route::prefix('donasi')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [DonasiController::class, 'index']);            
    Route::post('/', [DonasiController::class, 'store']);           
    Route::get('/{id}', [DonasiController::class, 'show']);         
    Route::put('/{id}', [DonasiController::class, 'update']);       
    Route::delete('/{id}', [DonasiController::class, 'destroy']);   
});

// Route untuk request
Route::prefix('request')->middleware('auth:sanctum')->group(function () {
    Route::get('/search/{keyword}', [RequestController::class, 'search']);
    Route::get('/', [RequestController::class, 'index']);                 
    Route::post('/', [RequestController::class, 'store']);                
    Route::get('/{id}', [RequestController::class, 'show']);             
    Route::put('/{id}', [RequestController::class, 'update']);           
    Route::delete('/{id}', [RequestController::class, 'destroy']);       
});