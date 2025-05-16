<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KomunitasController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\AnggotaController;


Route::prefix('auth')->group(function () {
    // Komunitas routes
    Route::post('/komunitas/register', [AuthController::class, 'komunitasRegister']);
    Route::post('/komunitas/login', [AuthController::class, 'komunitasApiLogin']);
    
    // Admin routes
    Route::post('/admin/register', [AuthController::class, 'adminRegister']);
    Route::post('/admin/login', [AuthController::class, 'adminLogin']);
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->prefix('komunitas')->group(function () {
    Route::get('/', [KomunitasController::class, 'index']);
    Route::post('/', [KomunitasController::class, 'store']);
    Route::get('/{id}', [KomunitasController::class, 'show']);
    Route::put('/{id}', [KomunitasController::class, 'update']);
    Route::delete('/{id}', [KomunitasController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->prefix('ruangan')->group(function () {
    Route::get('/', [RuanganController::class, 'index']);
    Route::get('/{id}', [RuanganController::class, 'show']);
});

Route::middleware('auth:sanctum')->prefix('pemesanan')->group(function () {
    Route::get('/', [PemesananController::class, 'index']);
    Route::post('/', [PemesananController::class, 'store']);
    
    // Hanya admin yang bisa update status
    Route::middleware('can:admin')->group(function () {
        Route::put('/{id}/status', [PemesananController::class, 'updateStatus']);
    });
});

Route::middleware('auth:sanctum')->prefix('anggota')->group(function () {
    Route::get('/', [AnggotaController::class, 'index']);
    Route::post('/', [AnggotaController::class, 'store']);
    Route::get('/{id}', [AnggotaController::class, 'show']);
    Route::put('/{id}', [AnggotaController::class, 'update']);
    Route::delete('/{id}', [AnggotaController::class, 'destroy']);
});