<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LugarDestacadoController;

Route::get('/', function () {
    return view('mapa');
});

// Rutas para manejar los lugares destacados
Route::get('/lugares-destacados', [LugarDestacadoController::class, 'index']); // Obtener todos los lugares
Route::post('/lugares-destacados', [LugarDestacadoController::class, 'store']); // Crear un lugar
Route::put('/lugares-destacados/{id}', [LugarDestacadoController::class, 'update']); // Actualizar un lugar
Route::delete('/lugares-destacados/{id}', [LugarDestacadoController::class, 'destroy']); // Eliminar un lugar