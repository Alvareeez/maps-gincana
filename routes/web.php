<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\AdminController;


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

// Página principal después de iniciar sesión (con middleware de autenticación)
Route::get('/home', function () {
    return view('home');
})->name('home')->middleware('auth');

Route::get('/mapa', [MapaController::class, 'index'])->name('mapa')->middleware('auth');

Route::get('/admin', [AdminController::class, 'index'])->name('admin.index')->middleware('auth', 'id_rol:1');

