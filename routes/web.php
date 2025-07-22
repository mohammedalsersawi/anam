<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleAuthController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/login', function () {
    return view('login');
});
Route::get('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

Route::get('auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('authgoogle');
Route::get('auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
