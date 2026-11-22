<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MyGradesController;
use App\Http\Controllers\StudentController;

Route::get('/', fn () => redirect()->route('login'));

// Ruta temporal de verificación (práctica 1.1)
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

    // Solo administrador. Va PRIMERO para que "papelera" no se confunda con {user}
    Route::middleware('role:admin')->prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('papelera', [UserController::class, 'trashed'])->name('trashed');
        Route::delete('{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::patch('{user}/restaurar', [UserController::class, 'restore'])
            ->withTrashed()->name('restore');
        Route::delete('{user}/forzar', [UserController::class, 'forceDelete'])
            ->withTrashed()->name('force-delete');
    });

    // Admin y empleado (sin eliminar)
    Route::middleware('role:admin,empleado')->group(function () {
        Route::resource('usuarios', UserController::class)
            ->parameters(['usuarios' => 'user'])
            ->except(['destroy']);
    });

        // Profesor: administra alumnos
    Route::middleware('role:profesor')->group(function () {
        Route::resource('alumnos', StudentController::class)
            ->parameters(['alumnos' => 'student'])
            ->except(['show']);
    });

    // Alumno: solo consulta su información
    Route::get('/mi-informacion', MyGradesController::class)
        ->middleware('role:alumno')
        ->name('mi-informacion');
});
