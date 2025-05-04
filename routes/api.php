<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});