<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group(
    [
        'prefix' =>'admin',
        'middleware' => [ 'localizationRedirect', 'localeViewPath']
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
            Route::get('/getData', 'getData');
            Route::put('/updateStatus/{id}', 'updateStatus');
        });
        Route::controller(\App\Http\Controllers\Api\Admin\LandPage\JourneySectionController::class)->prefix('journey')->group(function () {
            Route::post('/store', 'store');
            Route::post('/update/{id}', 'update');
            Route::delete('destroy/{id}', 'destroy');
            Route::get('/getData', 'getData');
            Route::put('/updateStatus/{id}', 'updateStatus');
        });
        Route::controller(\App\Http\Controllers\Api\Admin\LandPage\ServiceSectionController::class)->prefix('service')->group(function () {
            Route::post('/store', 'store');
            Route::post('/update/{id}', 'update');
            Route::delete('destroy/{id}', 'destroy');
            Route::get('/getData', 'getData');
            Route::put('/updateStatus/{id}', 'updateStatus');
        });
    }
);
