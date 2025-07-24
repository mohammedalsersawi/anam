<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group(
    [
        'prefix' => 'admin',
        'middleware' => ['localizationRedirect', 'localeViewPath']
    ],
    function () {
        Route::controller(\App\Http\Controllers\Api\Auth\Admin\AuthController::class)->group(function () {
            Route::post('/login', 'login');
            Route::post('/register', 'register');
            Route::post('/logout', 'logout');
            Route::middleware('auth:sanctum')->group(function () {
                Route::post('/logout', 'logout');
            });
        });

        Route::controller(\App\Http\Controllers\Api\Admin\LandPage\NavigationLinkController::class)->prefix('navigationLink')->group(function () {
            Route::post('/store', 'store');
            Route::post('/update/{id}', 'update');
            Route::delete('destroy/{id}', 'destroy');
            Route::get('/getData', 'getData');
            Route::put('/updateStatus/{id}', 'updateStatus');
        });
        Route::controller(\App\Http\Controllers\Api\Admin\LandPage\HeroSectionController::class)->prefix('hero')->group(function () {
            Route::post('/store', 'store');
            Route::post('/update/{id}', 'update');
            Route::delete('destroy/{id}', 'destroy');
            Route::get('/index', 'index');
            Route::put('/updateStatus/{id}', 'updateStatus');
        });
        Route::controller(\App\Http\Controllers\Api\Admin\LandPage\JourneySectionController::class)->prefix('journey')->group(function () {
            Route::post('/store', 'store');
            Route::post('/update/{id}', 'update');
            Route::delete('destroy/{id}', 'destroy');
            Route::get('/index', 'index');
            Route::put('/updateStatus/{id}', 'updateStatus');
        });
        Route::controller(\App\Http\Controllers\Api\Admin\LandPage\ServiceSectionController::class)->prefix('service')->group(function () {
            Route::post('/store', 'store');
            Route::post('/update/{id}', 'update');
            Route::get('/getData', 'getData');
        });
        // Route::controller(\App\Http\Controllers\Api\Admin\LandPage\ServiceSectionController::class)->prefix('service')->group(function () {
        //     Route::post('/store', 'store');
        //     Route::post('/update/{id}', 'update');
        //     Route::delete('destroy/{id}', 'destroy');
        //     Route::get('/index', 'index');
        //     Route::put('/updateStatus/{id}', 'updateStatus');
        // });
        Route::controller(\App\Http\Controllers\Api\Admin\Category\CategoryController::class)->prefix('category')->group(function () {
            Route::post('/store', 'store');
            Route::post('/update/{id}', 'update');
            Route::delete('destroy/{id}', 'destroy');
            Route::get('/index', 'index');
            Route::put('/updateStatus/{id}', 'updateStatus');
        });
        Route::controller(\App\Http\Controllers\Api\Admin\Tests\TestController::class)->prefix('tests')->group(function () {
            Route::post('/store', 'store');
            Route::post('/update/{id}', 'update');
            Route::delete('destroy/{id}', 'destroy');
            Route::get('/index', 'index');
            Route::put('/updateStatus/{id}', 'updateStatus');
        });
        Route::controller(\App\Http\Controllers\Api\Admin\Course\CourseController::class)->prefix('courses')->group(function () {
            Route::post('/store', 'store');
            Route::post('/update/{id}', 'update');
            Route::delete('destroy/{id}', 'destroy');
            Route::get('/index', 'index');
            Route::put('/updateStatus/{id}', 'updateStatus');
        });
        Route::controller(\App\Http\Controllers\Api\Admin\LandPage\FeatureController::class)->prefix('features')->group(function () {
            Route::post('/store', 'store');
            Route::post('/update/{id}', 'update');
            Route::get('/getData', 'getData');
        });
        Route::controller(\App\Http\Controllers\Api\Admin\ContactInfo\ContactInfoController::class)->prefix('contactInfo')->group(function () {
            Route::post('/store', 'store');
            Route::post('/update/{id}', 'update');
            Route::get('/getData', 'getData');
        });
        Route::controller(\App\Http\Controllers\Api\Admin\ContactMessage\ContactMessageController::class)->prefix('contactInfo')->group(function () {
            Route::get('/getMessages', 'getMessages');
        });
    }
);



