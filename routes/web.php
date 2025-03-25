<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LugarDestacadoController;
use App\Http\Controllers\TipoMarcadorController;


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
Route::get('/mapa', function () {
    return view('mapa');
});
Route::get('/etiquetas', function () {
    return response()->json(\App\Models\Etiqueta::all());
});
// Rutas para manejar los lugares destacados
Route::get('/lugares-destacados', [LugarDestacadoController::class, 'index']); // Obtener todos los lugares
Route::post('/lugares-destacados', [LugarDestacadoController::class, 'store']); // Crear un lugar
Route::put('/lugares-destacados/{id}', [LugarDestacadoController::class, 'update']); // Actualizar un lugar
Route::delete('/lugares-destacados/{id}', [LugarDestacadoController::class, 'destroy']); // Eliminar un lugar
Route::get('/tipo-marcadores', [TipoMarcadorController::class, 'index']);
Route::get('/buscar-lugares', [LugarDestacadoController::class, 'buscar']);
Route::delete('/lugares-destacados/{id}', [LugarDestacadoController::class, 'destroy']);
Route::get('/lugares-destacados-con-relaciones', [LugarDestacadoController::class, 'indexWithRelations']);
