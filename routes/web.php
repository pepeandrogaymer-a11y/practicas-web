<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\TwoFactorController;

Route::middleware(['guest', 'two-factor.pending', 'signed'])->group(function () {
    Route::get('/verificacion/codigo', [TwoFactorController::class, 'show'])->name('two-factor.show');
    Route::post('/verificacion/codigo', [TwoFactorController::class, 'verify'])
        ->middleware('throttle:5,1')
        ->name('two-factor.verify');
});

// Solo para visitantes sin sesión
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Solo para usuarios con sesión iniciada
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
});
