<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

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


Route::prefix('{locale}')->middleware(['localeViewPath'])->group(function () {
    Route::controller(\App\Http\Controllers\Api\User\MainController::class)
        ->group(function () {
            Route::get('/heroSection', 'heroSection');
            Route::get('/SuccessStorie', 'SuccessStorie');
        });
});

Route::controller()
    ->prefix('article')
    ->group(function () {
        Route::post('comment/store', 'store');
        Route::get('/SuccessStorie', 'SuccessStorie');
    });
Route::prefix('blog/article')->group(function () {
    Route::post('comment/store', [\App\Http\Controllers\Api\Admin\blog\BlogInteractions\ArticleCommentController::class, 'store']);
    Route::post('like/store', [\App\Http\Controllers\Api\Admin\blog\BlogInteractions\ArticleCommentController::class, 'store']);
});

require base_path('routes/admin.php');
