<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ruta para la gincana
Route::get('/gincana', function () {
    return view('gincana');
});