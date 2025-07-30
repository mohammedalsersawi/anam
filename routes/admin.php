<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;


Route::controller(\App\Http\Controllers\Api\Auth\Admin\AuthController::class)->group(function () {
    Route::post('admin/login', 'login');
    Route::post('admin/register', 'register');
    Route::post('admin/logout', 'logout');
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('admin/logout', 'logout');
    });
});


Route::group(
    [
        'prefix' => 'admin',
        'middleware' => ['localizationRedirect', 'localeViewPath', 'auth:sanctum']
    ],
    function () {

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
            Route::get('/{type}', 'type');
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
        Route::controller(\App\Http\Controllers\Api\Admin\SuccessStorie\SuccessStorieController::class)->prefix('successStorie')->group(function () {
            Route::post('/store', 'store');
            Route::post('/update/{id}', 'update');
            Route::get('/getData', 'getData');
        });
    }
);




Route::group(
    [
        'prefix' => 'admin/blog',
        'middleware' => ['localizationRedirect', 'localeViewPath']
    ],
    function () {
        Route::controller(\App\Http\Controllers\Api\Admin\blog\BlogCategory\BlogCategoryController::class)->prefix('category')->group(function () {
            Route::post('/store', 'store');
            Route::post('/update/{id}', 'update');
            Route::delete('destroy/{id}', 'destroy');
            Route::get('/index', 'index');
            Route::put('/updateStatus/{id}', 'updateStatus');
        });
        Route::controller(\App\Http\Controllers\Api\Admin\blog\BlogArticle\BlogArticleController::class)->prefix('article')->group(function () {
            Route::post('/store', 'store');
            Route::post('/update/{id}', 'update');
            Route::delete('destroy/{id}', 'destroy');
            Route::get('/index', 'index');
            Route::put('/updateStatus/{id}', 'updateStatus');
        });
    }
);
