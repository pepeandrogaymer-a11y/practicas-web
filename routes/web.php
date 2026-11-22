<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MyGradesController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ComputerController;
use App\Http\Controllers\ReservationController;

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

            // Productos: admin y empleado consultan, buscan y ordenan
    Route::get('productos', [ProductController::class, 'index'])
        ->middleware('role:admin,empleado')
        ->name('productos.index');

    // Productos: solo el admin registra, actualiza y elimina
    Route::middleware('role:admin')->group(function () {
        Route::resource('productos', ProductController::class)
            ->parameters(['productos' => 'product'])
            ->except(['index', 'show']);

                // Equipos: admin y estudiante los consultan (el estudiante solo ve los disponibles)
    Route::get('equipos', [ComputerController::class, 'index'])
        ->middleware('role:admin,estudiante')
        ->name('equipos.index');

    // Solo administrador: administrar equipos y ver todas las reservaciones
    Route::middleware('role:admin')->group(function () {
        Route::resource('equipos', ComputerController::class)
            ->parameters(['equipos' => 'computer'])
            ->except(['index', 'show']);

        Route::get('reservaciones', [ReservationController::class, 'index'])
            ->name('reservaciones.index');
    });

    // Solo estudiante: reservar y ver sus propias reservaciones
    Route::middleware('role:estudiante')->group(function () {
        Route::post('equipos/{computer}/reservar', [ReservationController::class, 'store'])
            ->name('equipos.reservar');
        Route::get('mis-reservaciones', [ReservationController::class, 'mine'])
            ->name('reservaciones.mias');
    });

    // Detalle y cancelación: la política decide si es tuya (o si eres admin)
    Route::middleware('role:admin,estudiante')->group(function () {
        Route::get('reservaciones/{reservation}', [ReservationController::class, 'show'])
            ->name('reservaciones.show');
        Route::patch('reservaciones/{reservation}/cancelar', [ReservationController::class, 'cancel'])
            ->name('reservaciones.cancel');
    });
    });
});
