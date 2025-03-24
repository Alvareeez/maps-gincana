<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PruebaController;
use Illuminate\Support\Facades\Auth;

// Rutas de acceso público (sin autenticación) ----------------------------------------------------------

// Ruta para mostrar la página de inicio (home)
Route::get('/', function () {
    return view('home');
})->name('home');

// Ruta para mostrar el formulario de login
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');

// Ruta para manejar el login
Route::post('login', [AuthController::class, 'login']);

// Ruta para manejar el logout
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Ruta para mostrar el formulario de registro
Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');

// Ruta para manejar el registro de usuarios
Route::post('register', [AuthController::class, 'register']);

// ------------------------------------------------------------------------------------------------------

// Rutas protegidas por autenticación (requieren estar logueado) ----------------------------------------

// Middleware 'auth' garantiza que solo los usuarios logueados puedan acceder a estas rutas
Route::middleware(['auth'])->group(function () {

    // Página principal después de iniciar sesión
    Route::get('/home', function () {
        return view('home');
    })->name('home');

    // Ruta para ver el mapa (requiere estar logueado)
    Route::get('/mapa', [MapaController::class, 'index'])->name('mapa');

    // ----------------------------------------
    // -------------- CRUDS ADMIN -------------
    // ----------------------------------------
    // Rutas para administrador (requiere ser administrador, se verfica en AdminController)
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

    // CRUDS USUARIOS 

    // Ruta para almacenar (añadir) un usuario
    Route::post('/admin/usuario', [AdminController::class, 'store'])->name('usuario.store');

    // Actualizar usuarios
    Route::put('/admin/usuario/{id}', [AdminController::class, 'update']);

    // Eliminar usuarios
    Route::delete('/admin/usuario/{id}', [AdminController::class, 'destroy'])->name('admin.usuario.destroy');


    // CRUDS PRUEBAS

    // Crear prueba
    Route::post('/pruebas', [PruebaController::class, 'store'])->name('pruebas.store');
    
    // Actualizar prueba
    Route::put('/pruebas/{id}', [PruebaController::class, 'update'])->name('pruebas.update');

    // Borrar prueba
    Route::delete('/pruebas/{id}', [PruebaController::class, 'destroy'])->name('pruebas.destroy');



});

