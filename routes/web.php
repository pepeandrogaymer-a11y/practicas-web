<?php

use App\Http\Controllers\AdminGameController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\ComputerController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\MyGradesController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

    Route::get('/registro', [RegisterController::class, 'showRegistrationForm'])->name('registro');
    Route::post('/registro', [RegisterController::class, 'register'])->middleware('throttle:10,1');
});

// Solo usuarios con sesión iniciada
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    // ---------- Práctica 1.2: usuarios ----------
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

    // ---------- Práctica 1.3: sistema académico ----------
    Route::middleware('role:profesor')->group(function () {
        Route::resource('alumnos', StudentController::class)
            ->parameters(['alumnos' => 'student'])
            ->except(['show']);
    });

    Route::get('/mi-informacion', MyGradesController::class)
        ->middleware('role:alumno')
        ->name('mi-informacion');

    // ---------- Práctica 1.4: productos ----------
    Route::get('productos', [ProductController::class, 'index'])
        ->middleware('role:admin,empleado')
        ->name('productos.index');

    Route::middleware('role:admin')->group(function () {
        Route::resource('productos', ProductController::class)
            ->parameters(['productos' => 'product'])
            ->except(['index', 'show']);
    });

    // ---------- Práctica 1.5: equipos y reservaciones ----------
    Route::get('equipos', [ComputerController::class, 'index'])
        ->middleware('role:admin,estudiante')
        ->name('equipos.index');

    Route::middleware('role:admin')->group(function () {
        Route::resource('equipos', ComputerController::class)
            ->parameters(['equipos' => 'computer'])
            ->except(['index', 'show']);

        Route::get('reservaciones', [ReservationController::class, 'index'])
            ->name('reservaciones.index');
    });

    Route::middleware('role:estudiante')->group(function () {
        Route::post('equipos/{computer}/reservar', [ReservationController::class, 'store'])
            ->name('equipos.reservar');
        Route::get('mis-reservaciones', [ReservationController::class, 'mine'])
            ->name('reservaciones.mias');
    });

    Route::middleware('role:admin,estudiante')->group(function () {
        Route::get('reservaciones/{reservation}', [ReservationController::class, 'show'])
            ->name('reservaciones.show');
        Route::patch('reservaciones/{reservation}/cancelar', [ReservationController::class, 'cancel'])
            ->name('reservaciones.cancel');
    });

    // ---------- Práctica 1.6: juego del gato ----------
    // Solo jugadores
    Route::middleware('role:jugador')->prefix('juego')->name('juego.')->group(function () {
        Route::get('/', [GameController::class, 'index'])->name('index');
        Route::get('historial', [GameController::class, 'history'])->name('historial');
        Route::post('partidas', [GameController::class, 'store'])->name('store');
        Route::post('partidas/{game}/unirse', [GameController::class, 'join'])->name('join');
        Route::post('partidas/{game}/movimientos', [GameController::class, 'move'])->name('move');
        Route::post('partidas/{game}/abandonar', [GameController::class, 'leave'])->name('leave');
    });

    // Ver una partida y consultar su estado: jugadores y admin (la política decide si es tuya)
    Route::middleware('role:jugador,admin')->prefix('juego/partidas')->name('juego.')->group(function () {
        Route::get('{game}', [GameController::class, 'show'])->name('show');
        Route::get('{game}/estado', [GameController::class, 'state'])->name('state');
    });

    // Administrador: todas las partidas y cancelación
    Route::middleware('role:admin')->prefix('admin/partidas')->name('admin.partidas.')->group(function () {
        Route::get('/', [AdminGameController::class, 'index'])->name('index');
        Route::patch('{game}/cancelar', [AdminGameController::class, 'cancel'])->name('cancel');
    });
});