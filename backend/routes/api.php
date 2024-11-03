<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/products/getAll',[ProductController::class,'getAllProducts']);

Route::middleware(['auth:api'])->group(function () {
    // User routes.
    Route::prefix('user')->group(function () {
        Route::get('/',[UserController::class,'index']);
        Route::patch('/setup',[UserController::class,'update']);
    });

    // Orders routes.
    Route::prefix('order')->group(function () {
        Route::post('/',[OrderController::class,'show']);
        Route::post('/create',[OrderController::class,'create']);
    });

    // Admin routes.
    Route::middleware(['admin'])->group(function () {
        Route::prefix('products')->group(function() {
           Route::post('/add',[ProductController::class,'addProduct']);
           Route::post('/edit',[ProductController::class,'editProduct']);
        });
    });
});

Route::middleware(['guest'])->group(function () {
    // Authenticate routes.
    Route::prefix('authenticate')->group(function() {
        Route::post('/send_otp',[AuthController::class,'create_otp']);
        Route::post('/verify_otp',[AuthController::class,'verify_otp']);

        Route::get('/redirect/{provider}',[AuthController::class,'redirect']);
    });
});
