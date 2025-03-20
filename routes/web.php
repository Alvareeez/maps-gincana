<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Ruta para mostrar el formulario de login
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');

// Ruta para manejar el login
Route::post('login', [AuthController::class, 'login']);

// Ruta para manejar el logout
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Página principal después de iniciar sesión (con middleware de autenticación)
Route::get('/home', function () {
    return view('home');
})->name('home')->middleware('auth');