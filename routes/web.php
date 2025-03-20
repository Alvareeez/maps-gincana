<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LugarController;

Route::get('/', function () {
    return view('mapa');
});

// Rutas para guardar y eliminar lugares
Route::post('/lugares', [LugarController::class, 'store']);
Route::delete('/lugares/{id}', [LugarController::class, 'destroy']);
