<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [\App\Http\Controllers\User\Auth\AuthController::class, 'login']);
    Route::post('/register', [\App\Http\Controllers\User\Auth\AuthController::class, 'register']);
    Route::post('/forgot-password', [\App\Http\Controllers\User\Auth\AuthController::class, 'sendResetLinkEmail']);
    Route::post('/reset-password', [\App\Http\Controllers\User\Auth\AuthController::class, 'resetPassword']);


    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [\App\Http\Controllers\User\Auth\AuthController::class, 'logout']);
    });
});


Route::post('/contact-messages', [\App\Http\Controllers\Api\Admin\ContactMessage\ContactMessageController::class, 'storeMessage']);


require base_path('routes/admin.php');
