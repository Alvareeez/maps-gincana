<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PruebaController;
use App\Http\Controllers\LugarController;
use App\Http\Controllers\NivelController;
use App\Http\Controllers\GincanaController;
use App\Http\Controllers\EtiquetaController;

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
    Route::get('/mapaUser', [MapaController::class, 'indexx'])->name('mapaUser');

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


    // CRUDS LUGAR DESTACADO
    Route::get('/admin/lugares-destacados', [LugarDestacadoController::class, 'index'])->name('admin.lugares-destacados.index');
    Route::post('/admin/lugares-destacados', [LugarDestacadoController::class, 'store'])->name('admin.lugares-destacados.store');
    Route::put('/admin/lugares-destacados/{id}', [LugarDestacadoController::class, 'update'])->name('admin.lugares-destacados.update');
    Route::delete('/admin/lugares-destacados/{id}', [LugarDestacadoController::class, 'destroy'])->name('admin.lugares-destacados.destroy');

    // CRUDS ETIQUETAS
    Route::get('/admin/etiquetas', [EtiquetaController::class, 'index'])->name('admin.etiquetas.index');
    Route::post('/admin/etiquetas', [EtiquetaController::class, 'store'])->name('admin.etiquetas.store');
    Route::put('/admin/etiquetas/{id}', [EtiquetaController::class, 'update'])->name('admin.etiquetas.update');
    Route::delete('/admin/etiquetas/{id}', [EtiquetaController::class, 'destroy'])->name('admin.etiquetas.destroy');

    // CRUDS TIPO MARCADOR
    Route::get('/admin/tipo-marcadores', [TipoMarcadorController::class, 'index'])->name('admin.tipo-marcadores.index');
    Route::post('/admin/tipo-marcadores', [TipoMarcadorController::class, 'store'])->name('admin.tipo-marcadores.store');
    Route::put('/admin/tipo-marcadores/{id}', [TipoMarcadorController::class, 'update'])->name('admin.tipo-marcadores.update');
    Route::delete('/admin/tipo-marcadores/{id}', [TipoMarcadorController::class, 'destroy'])->name('admin.tipo-marcadores.destroy');




    // Rutas de gincana
    Route::prefix('gincana')->group(function () {
        // Vistas
        Route::get('/', [GincanaController::class, 'vistaGincanaMenu'])->name('gincana.menu');
        Route::get('/lobby/{id}', [GincanaController::class, 'vistaGincanaLobby'])->name('gincana.lobby');
        Route::get('/juego/{id}', [GincanaController::class, 'vistaGincanaJuego'])->name('gincana.juego');
        
        // Acciones
        Route::post('/unirse', [GincanaController::class, 'unirseAGrupo'])->name('gincana.unirse');
        Route::post('/salir', [GincanaController::class, 'salirGrupo'])->name('gincana.salir');
        Route::post('/responder/{id}', [GincanaController::class, 'responderPrueba']);
        
        // APIs
        Route::prefix('api')->group(function () {
            Route::get('/estado-juego/{id}', [GincanaController::class, 'estadoJuego']);
            Route::get('/nivel-actual/{id}', [GincanaController::class, 'nivelActual']);
            Route::post('/responder/{id}', [GincanaController::class, 'responderPrueba']);
            Route::get('/gincanas-abiertas', [GincanaController::class, 'obtenerGincanasAbiertas'])->name('gincana.gincanas-abiertas');
            Route::get('/grupos-disponibles/{id}', [GincanaController::class, 'obtenerGruposGincana'])->name('gincana.grupos-disponibles');
        });
    });
});