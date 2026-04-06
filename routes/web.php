<?php

use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\Logout;
use App\Http\Controllers\Auth\Register;
use App\Http\Controllers\ChirpController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ChirpController::class, 'index'])
    ->name('home');

Route::middleware('auth')->group(function () {
    Route::resource('chirps', ChirpController::class)
        ->only(['store', 'edit', 'update', 'destroy']);
});

Route::view('/register', 'auth.register')
    ->middleware('guest')
    ->name('register');

Route::post('/register', Register::class)
    ->middleware('guest')
    ->name('register.store');

Route::view('/login', 'auth.login')
    ->middleware('guest')
    ->name('login');
Route::post('/login', Login::class)
    ->middleware('guest')
    ->name('login.attempt');

Route::post('/logout', Logout::class)
    ->middleware('auth')
    ->name('logout');
