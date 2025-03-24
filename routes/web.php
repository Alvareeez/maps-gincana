<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GincanaController;

Route::get('/', function () {
    return view('welcome');
});

// rutas para la gincana
Route::prefix('gincana')->group(function () {
    // Vistas
    Route::get('/', [GincanaController::class, 'vistaGincanaMenu'])->name('gincana.menu');
    Route::get('/lobby/{id}', [GincanaController::class, 'vistaGincanaLobby'])->name('gincana.lobby');

    // APIs
    Route::get('/api/gincanasAbiertas', [GincanaController::class, 'obtenerGincanasAbiertas'])->name('gincana.api.gincanasAbiertas');
    Route::get('/api/gruposDisponibles/{id}', [GincanaController::class, 'obtenerGruposGincana'])->name('gincana.api.gruposDisponibles');
});
