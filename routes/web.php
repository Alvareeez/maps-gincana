<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PruebaController;
use App\Http\Controllers\LugarController;
use App\Http\Controllers\NivelController;
use App\Http\Controllers\GincanaController;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LugarDestacadoController;
use App\Http\Controllers\TipoMarcadorController;

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
// Route::post('/lugares-destacados/favoritos', [LugarDestacadoController::class, 'addToFavorites']);
Route::post('/lugares-destacados/favoritos', [LugarDestacadoController::class, 'addToFavorites'])->name('lugares.favoritos');
// Route::get('/listas', [ListaController::class, 'index']);
Route::delete('/lugares-destacados/favoritos/{id}', [LugarDestacadoController::class, 'quitarDeFavoritos'])
    ->name('lugares.favoritos.quitar');
Route::post('/lugares-destacados/favoritos', [LugarDestacadoController::class, 'addToFavorites']);
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
    Route::put('/pruebas/{id}', [PruebaController::class, 'update']);

    // Borrar prueba
    Route::delete('/pruebas/{id}', [PruebaController::class, 'destroy'])->name('pruebas.destroy');


    // CRUDS LUGARES

    // Crear lugar
    Route::post('/lugares', [LugarController::class, 'store'])->name('lugares.store');

    // Actualizar lugar
    Route::put('/lugares/{id}', [LugarController::class, 'update'])->name('lugares.update');

    // Eliminar lugar
    Route::delete('/lugares/{id}', [LugarController::class, 'destroy'])->name('lugares.destroy');


    // CRUDS NIVELES

    Route::get('/niveles', [NivelController::class, 'index'])->name('niveles.index');
    Route::post('/niveles', [NivelController::class, 'store'])->name('niveles.store');
    Route::put('/niveles/{nivel}', [NivelController::class, 'update'])->name('niveles.update');
    Route::delete('/niveles/{nivel}', [NivelController::class, 'destroy'])->name('niveles.destroy');     
    
    
    // CRUDS GINCANAS

    Route::get('/gincanas', [GincanaController::class, 'index'])->name('gincanas.index');
    Route::post('/gincanas', [GincanaController::class, 'store'])->name('gincanas.store');
    Route::put('/gincanas/{gincana}', [GincanaController::class, 'update'])->name('gincanas.update');
    Route::delete('/gincanas/{gincana}', [GincanaController::class, 'destroy'])->name('gincanas.destroy');
});


    // rutas para la gincana
    Route::prefix('gincana')->group(function () {
        // Vistas
        Route::get('/', [GincanaController::class, 'vistaGincanaMenu'])->name('gincana.menu');
        Route::get('/lobby/{id}', [GincanaController::class, 'vistaGincanaLobby'])->name('gincana.lobby');
        Route::get('/juego/{id}', [GincanaController::class, 'vistaGincanaJuego'])->name('gincana.juego');
        Route::post('/salir', [GincanaController::class, 'salirGrupo'])->name('gincana.salir');

        // APIs
        Route::get('/api/gincanasAbiertas', [GincanaController::class, 'obtenerGincanasAbiertas'])->name('gincana.api.gincanasAbiertas');
        Route::get('/api/gruposDisponibles/{id}', [GincanaController::class, 'obtenerGruposGincana'])->name('gincana.api.gruposDisponibles');

        // Acciones
        Route::post('/api/unirse', [GincanaController::class, 'unirseAGrupo'])->name('gincana.api.unirse');
    });
