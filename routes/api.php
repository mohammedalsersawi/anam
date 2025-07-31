<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::prefix('auth')->group(function () {
    Route::post('/login', [\App\Http\Controllers\Api\Auth\User\AuthController::class, 'login']);
    Route::post('/register', [\App\Http\Controllers\Api\Auth\User\AuthController::class, 'register']);
    Route::post('/forgot-password', [\App\Http\Controllers\Api\Auth\User\AuthController::class, 'sendResetLinkEmail']);
    Route::post('/reset-password', [\App\Http\Controllers\Api\Auth\User\AuthController::class, 'resetPassword']);


    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [\App\Http\Controllers\Api\Auth\User\AuthController::class, 'logout']);
    });
});


Route::post('/contact-messages', [\App\Http\Controllers\Api\Admin\ContactMessage\ContactMessageController::class, 'storeMessage']);


Route::prefix('{locale}')->middleware(['localeViewPath'])->group(function () {
    Route::controller(\App\Http\Controllers\Api\User\MainController::class)
        ->group(function () {
            Route::get('/heroSection', 'heroSection');
            Route::get('/homepageContent', 'homepageContent');
        });
    Route::get('/activities',  [\App\Http\Controllers\Api\Admin\Activity\ActivityController::class, 'getActivities']);
    Route::prefix('blog')->controller(\App\Http\Controllers\Api\User\Blog\MainBlogController::class)
        ->group(function () {
            Route::get('/articles', 'getAllBlogSections');
            Route::get('/article/details/{id}', 'detailsArticle');
            Route::get('/article/filtre/{id}', 'filtreArticle');
            Route::get('/article/search', 'searchArticle');
        });
});


Route::prefix('blog/article')->middleware('auth:sanctum')->group(function () {
    Route::controller(\App\Http\Controllers\Api\Admin\blog\BlogInteractions\InteractionController::class)
        ->group(function () {
            Route::post('comment/store', 'store');
            Route::post('like/store', 'toggleLike');
        });
});
require base_path('routes/admin.php');
