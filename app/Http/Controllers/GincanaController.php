<?php

namespace App\Http\Controllers;

use App\Models\Gincana;
use App\Models\Grupo;
use App\Models\Jugador;
use App\Models\Nivel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GincanaController extends Controller
{
    // --------- VISTAS ---------

    /**
     * Muestra la página principal de gincanas
     */
    public function vistaGincanaMenu()
    {
        // Verificar si el usuario ya está en un grupo
        $jugador = Jugador::where('id_usuario', Auth::id())->first();
        if ($jugador) {
            return redirect()->route('gincana.juego', ['id' => $jugador->grupo->id_gincana]);
        }
        return view('gincana.paginaPrincipal');
    }

    /**
     * Muestra el lobby para unirse a grupos de una gincana
     */
    public function vistaGincanaLobby($id)
    {
        // Verificar si el usuario ya está en un grupo
        $jugador = Jugador::where('id_usuario', Auth::id())->first();
        if ($jugador) {
            return redirect()->route('gincana.juego', ['id' => $jugador->grupo->id_gincana]);
        }
        return view('gincana.menuLobby');
    }

    /**
     * Muestra la vista principal del juego
     */
    public function vistaGincanaJuego($id)
    {
        // Verificar si el usuario está en un grupo de esta gincana
        $jugador = Jugador::where('id_usuario', Auth::id())->first();
        if (!$jugador || $jugador->grupo->id_gincana != $id) {
            return redirect()->route('gincana.menu');
        }
        return view('gincana.juego', ['id' => $id]);
    }

    // --------- FUNCIONES PÚBLICAS ---------

    /**
     * Obtiene todas las gincanas abiertas con 4 niveles
     */
    public function obtenerGincanasAbiertas()
    {
        try {
            // Obtener IDs de gincanas con exactamente 4 niveles
            $gincanasValidas = DB::table('niveles')
                ->select('id_gincana')
                ->groupBy('id_gincana')
                ->havingRaw('COUNT(*) = 4')
                ->pluck('id_gincana');

            // Obtener gincanas abiertas que están en esa lista
            $gincanas = Gincana::where('estado', 'abierta')
                ->whereIn('id', $gincanasValidas)
                ->withCount('niveles') // Asegurarnos de que tiene 4 niveles
                ->select('id', 'nombre', 'cantidad_jugadores', 'cantidad_grupos')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $gincanas,
                'message' => $gincanas->isEmpty() ? 'No hay gincanas disponibles' : 'Gincanas cargadas correctamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener gincanas abiertas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar gincanas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene información de una gincana específica
     */
    public function infoGincana($id)
    {
        $gincana = Gincana::find($id);

        if ($gincana) {
            return response()->json([
                'estado' => 'encontrado',
                'respuesta' => $gincana
            ]);
        }

        return response()->json([
            'estado' => 'no encontrado',
            'respuesta' => 'No se ha encontrado la gincana.'
        ]);
    }

    /**
     * Verifica si una gincana existe y está abierta
     */
    public function gincanaAbierta($id)
    {
        try {
            $gincana = Gincana::find($id);
            
            if (!$gincana) {
                return 'no existe';
            }

            // Verificar que tenga exactamente 4 niveles
            $countNiveles = DB::table('niveles')
                ->where('id_gincana', $id)
                ->count();

            if ($gincana->estado === 'abierta' && $countNiveles === 4) {
                return 'abierta';
            }
            
            return 'cerrada';

        } catch (\Exception $e) {
            Log::error('Error verificando gincana abierta: ' . $e->getMessage());
            return 'cerrada';
        }
    }

    /**
     * Obtiene los grupos disponibles de una gincana
     */
    public function obtenerGruposGincana($id)
    {
        try {
            // Verificar estado de la gincana
            $estadoGincana = $this->gincanaAbierta($id);
            
            if ($estadoGincana != 'abierta') {
                return response()->json([
                    'success' => false,
                    'message' => 'La gincana ya no está disponible',
                    'redirect' => route('gincana.menu')
                ], 400);
            }

            // Obtener grupos con información de jugadores
            $grupos = Grupo::where('id_gincana', $id)
                ->withCount('jugadores')
                ->get()
                ->map(function ($grupo) {
                    return [
                        'id' => $grupo->id,
                        'nombre' => $grupo->nombre,
                        'jugadores_actuales' => $grupo->jugadores_count,
                        'max_jugadores' => $grupo->gincana->cantidad_jugadores,
                        'disponible' => $grupo->jugadores_count < $grupo->gincana->cantidad_jugadores
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $grupos,
                'message' => 'Grupos cargados correctamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener grupos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar grupos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Permite a un usuario unirse a un grupo
     */
    public function unirseAGrupo(Request $request)
    {
        $id_grupo = $request->input('id_grupo');
        
        // Comprobar si el grupo existe
        $grupo = Grupo::find($id_grupo);
        if (!$grupo) {
            return response()->json([
                'estado' => 'error',
                'respuesta' => 'El grupo no existe.'
            ]);
        }

        // Comprobar si la gincana existe y está abierta
        $estadoGincana = $this->gincanaAbierta($grupo->id_gincana);
        if ($estadoGincana !== 'abierta') {
            return response()->json([
                'estado' => 'error',
                'respuesta' => 'La gincana no está disponible.'
            ]);
        }

        // Comprobar que el grupo no esté lleno (usando bloqueo para evitar concurrencia)
        DB::beginTransaction();
        
        try {
            // Bloquear el grupo para evitar que otros usuarios lo modifiquen
            $grupo = Grupo::lockForUpdate()->find($id_grupo);
            
            $jugadoresEnGrupo = Jugador::where('id_grupo', $id_grupo)->count();
            $gincana = Gincana::find($grupo->id_gincana);
            
            if ($jugadoresEnGrupo >= $gincana->cantidad_jugadores) {
                DB::rollback();
                return response()->json([
                    'estado' => 'error',
                    'respuesta' => 'El grupo está lleno.'
                ]);
            }

            $idUser = Auth::id();

            // Comprobar si el jugador ya existe
            $jugadorExistente = Jugador::where('id_usuario', $idUser)->first();
            
            if ($jugadorExistente) {
                // Comprobar si el jugador ya está en este grupo
                if ($jugadorExistente->id_grupo == $id_grupo) {
                    DB::rollback();
                    return response()->json([
                        'estado' => 'error',
                        'respuesta' => 'Ya estás en este grupo.'
                    ]);
                }
                
                // Si existe, actualizar su grupo y reiniciar completado
                $jugadorExistente->id_grupo = $id_grupo;
                $jugadorExistente->completado = 0;
                $jugadorExistente->save();
            } else {
                // Si no existe, crear nuevo jugador
                $jugador = new Jugador();
                $jugador->id_usuario = $idUser;
                $jugador->id_grupo = $id_grupo;
                $jugador->completado = 0;
                $jugador->save();
            }

            DB::commit();

            // Verificar si todos los grupos están completos
            $grupo = Grupo::find($id_grupo);
            $todosConectados = $this->verificarGruposCompletos($grupo->id_gincana);
            
            if ($todosConectados) {
                // Actualizar estado de la gincana
                Gincana::where('id', $grupo->id_gincana)
                    ->update(['estado' => 'ocupada']);
            }

            // Redirigir a la página del juego
            return redirect()->route('gincana.juego', ['id' => $grupo->id_gincana]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al unirse al grupo: ' . $e->getMessage());
            return response()->json([
                'estado' => 'error',
                'respuesta' => 'Error al unirse al grupo: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Permite a un usuario salir de un grupo
     */
    public function salirGrupo()
    {
        DB::beginTransaction();
        
        try {
            $jugador = Jugador::where('id_usuario', Auth::id())->first();
            if ($jugador) {
                // Comprobar si la gincana sigue abierta
                $estadoGincana = $this->gincanaAbierta($jugador->grupo->id_gincana);
                if ($estadoGincana !== 'abierta') {
                    DB::rollback();
                    return redirect()->route('gincana.menu')->with('error', 'No puedes salir de una gincana que ya ha terminado.');
                }
                
                $jugador->delete();
            }
            
            DB::commit();
            return redirect()->route('gincana.menu');
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al salir del grupo: ' . $e->getMessage());
            return redirect()->route('gincana.menu')->with('error', 'Error al salir del grupo: ' . $e->getMessage());
        }
    }

    /**
     * Verifica si todos los grupos de una gincana están completos
     */
    public function verificarGruposCompletos($gincanaId)
    {
        $gincana = Gincana::findOrFail($gincanaId);
        $grupos = Grupo::where('id_gincana', $gincanaId)->get();
        
        foreach ($grupos as $grupo) {
            $jugadoresEnGrupo = Jugador::where('id_grupo', $grupo->id)->count();
            if ($jugadoresEnGrupo < $gincana->cantidad_jugadores) {
                return false;
            }
        }
        
        return true;
    }

    // --------- APIs DEL JUEGO ---------

    /**
     * API para verificar el estado del juego
     */
    public function estadoJuego($gincanaId)
    {
        $jugador = Jugador::with(['grupo.gincana'])
            ->where('id_usuario', Auth::id())
            ->firstOrFail();

        // Verificar que el jugador pertenece a esta gincana
        if ($jugador->grupo->id_gincana != $gincanaId) {
            return response()->json(['error' => 'No perteneces a esta gincana'], 403);
        }

        $gincana = $jugador->grupo->gincana;
        $todosConectados = $this->verificarGruposCompletos($gincanaId);

        if ($todosConectados) {
            Gincana::where('id', $gincanaId)
                ->where('estado', 'abierta')
                ->update(['estado' => 'ocupada']);

            return response()->json([
                'estado' => 'iniciado',
                'mensaje' => 'El juego ha comenzado!'
            ]);
        }

        $grupos = Grupo::where('id_gincana', $gincanaId)
            ->withCount('jugadores')
            ->get()
            ->map(function($grupo) use ($jugador) {
                return [
                    'id' => $grupo->id,
                    'nombre' => $grupo->nombre,
                    'jugadores' => $grupo->jugadores_count,
                    'max_jugadores' => $grupo->gincana->cantidad_jugadores,
                    'es_mi_grupo' => $grupo->id === $jugador->id_grupo
                ];
            });

        return response()->json([
            'estado' => 'esperando',
            'grupos' => $grupos,
            'mensaje' => 'Esperando a que se unan todos los jugadores...'
        ]);
    }

    /**
     * API para obtener el nivel actual del jugador
     */
    public function nivelActual($gincanaId)
    {
        try {
            // Verificar autenticación
            if (!Auth::check()) {
                return response()->json(['error' => 'No autenticado'], 401);
            }

            // Obtener jugador y grupo con relaciones necesarias
            $jugador = Jugador::with(['grupo.gincana.niveles' => function($query) {
                $query->with(['lugar', 'prueba'])->orderBy('nombre');
            }])->where('id_usuario', Auth::id())->first();
            
            if (!$jugador) {
                return response()->json(['error' => 'Jugador no encontrado'], 404);
            }

            $grupo = $jugador->grupo;
            
            // Verificar que el grupo pertenece a la gincana solicitada
            if ($grupo->id_gincana != $gincanaId) {
                return response()->json(['error' => 'Grupo no pertenece a esta gincana'], 400);
            }

            // Obtener el nivel actual del grupo
            $nivelActual = $grupo->nivel;
            
            // Buscar el nivel correspondiente (los niveles están ordenados)
            $nivel = $grupo->gincana->niveles->get($nivelActual - 1);
            
            if (!$nivel) {
                // Si no se encuentra, intentar con el primer nivel
                $nivel = $grupo->gincana->niveles->first();
                if (!$nivel) {
                    return response()->json(['error' => 'No hay niveles en esta gincana'], 404);
                }
                // Actualizar el nivel del grupo si era incorrecto
                $grupo->nivel = (int) str_replace('Nivel ', '', $nivel->nombre);
                $grupo->save();
            }

            // Verificar que el nivel tiene las relaciones necesarias
            if (!$nivel->lugar || !$nivel->prueba) {
                return response()->json(['error' => 'Datos del nivel incompletos'], 500);
            }

            return response()->json([
                'estado' => 'iniciado',
                'nivel' => $nivelActual,
                'pista' => $nivel->lugar->pista,
                'pregunta' => $nivel->prueba->pregunta,
                'ubicacion' => [
                    'latitud' => $nivel->lugar->latitud,
                    'longitud' => $nivel->lugar->longitud
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error en nivelActual: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error del servidor',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Procesa la respuesta del jugador a una prueba
     */
    public function responderPrueba(Request $request, $gincanaId)
    {
        $request->validate([
            'respuesta' => 'required|string'
        ]);

        $jugador = Jugador::where('id_usuario', Auth::id())
            ->whereHas('grupo', function($q) use ($gincanaId) {
                $q->where('id_gincana', $gincanaId);
            })
            ->firstOrFail();

        $grupo = $jugador->grupo;
        $nivelActual = $grupo->nivel;

        $nivel = Nivel::with(['prueba'])
            ->where('id_gincana', $gincanaId)
            ->where('nombre', 'Nivel '.$nivelActual)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $respuestaCorrecta = strtolower(trim($nivel->prueba->respuesta));
            $respuestaUsuario = strtolower(trim($request->respuesta));
            
            if ($respuestaUsuario === $respuestaCorrecta) {
                $jugador->completado = true;
                $jugador->save();
                
                // Verificar si todos los jugadores han completado el nivel
                $jugadoresCompletados = Jugador::where('id_grupo', $grupo->id)
                    ->where('completado', true)
                    ->count();
                
                $totalJugadores = $grupo->gincana->cantidad_jugadores;
                
                if ($jugadoresCompletados >= $totalJugadores) {
                    // Avanzar al siguiente nivel
                    $nuevoNivel = $nivelActual + 1;
                    
                    // Verificar si es el último nivel
                    $ultimoNivel = Nivel::where('id_gincana', $gincanaId)
                        ->orderBy('nombre', 'desc')
                        ->first();
                    
                    if ($nuevoNivel > (int) str_replace('Nivel ', '', $ultimoNivel->nombre)) {
                        // Juego completado
                        $grupo->gincana->estado = 'completada';
                        $grupo->gincana->id_ganador = $grupo->id;
                        $grupo->gincana->save();
                        
                        DB::commit();
                        
                        return response()->json([
                            'estado' => 'completado',
                            'ganador' => true
                        ]);
                    } else {
                        // Avanzar al siguiente nivel
                        $grupo->nivel = $nuevoNivel;
                        $grupo->save();
                        
                        // Reiniciar estado de completado para todos los jugadores
                        Jugador::where('id_grupo', $grupo->id)
                            ->update(['completado' => false]);
                        
                        DB::commit();
                        
                        return response()->json([
                            'estado' => 'nivel_completado',
                            'nuevo_nivel' => $nuevoNivel
                        ]);
                    }
                }
                
                DB::commit();
                return response()->json(['estado' => 'correcto', 'completado' => true]);
            } else {
                DB::commit();
                return response()->json(['estado' => 'incorrecto', 'completado' => false]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error en responderPrueba: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // --------- ADMINISTRACIÓN ---------
    
    /**
     * Muestra el listado de gincanas (admin)
     */
    public function index()
    {
        $gincanas = Gincana::with(['ganadorGrupo', 'grupos'])->get();
        $grupos = Grupo::all();
        return view('gincanas.index', compact('gincanas', 'grupos'));
    }

    /**
     * Almacena una nueva gincana (admin)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'estado' => 'required|in:abierta,ocupada',
            'cantidad_jugadores' => 'required|integer|min:1',
            'cantidad_grupos' => 'required|integer|min:1',
            'id_ganador' => 'nullable|exists:grupos,id'
        ]);

        // Usamos transacción para asegurar integridad de datos
        $gincana = DB::transaction(function () use ($validated) {
            // Crear la gincana
            $gincana = Gincana::create($validated);
            
            // Crear los grupos para esta gincana
            $this->crearGruposParaGincana($gincana);
            
            return $gincana;
        });

        return response()->json([
            'success' => true,
            'message' => 'Gincana creada correctamente con '.$gincana->cantidad_grupos.' grupos',
            'data' => $gincana->load('grupos')
        ]);
    }

    /**
     * Actualiza una gincana existente (admin)
     */
    public function update(Request $request, $id)
    {
        $gincana = Gincana::findOrFail($id);
    
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'estado' => 'required|in:abierta,ocupada',
            'cantidad_jugadores' => 'required|integer|min:1',
            'cantidad_grupos' => 'required|integer|min:1',
            'id_ganador' => 'nullable|exists:grupos,id'
        ]);
    
        DB::transaction(function () use ($gincana, $validated) {
            // Guardar la cantidad actual de grupos
            $cantidadActual = $gincana->cantidad_grupos;
            $nuevaCantidad = $validated['cantidad_grupos'];
            
            // Actualizar los datos de la gincana
            $gincana->update($validated);
            
            // Manejar cambios en la cantidad de grupos
            if ($nuevaCantidad != $cantidadActual) {
                $gruposActuales = $gincana->grupos()->count();
                
                if ($nuevaCantidad > $gruposActuales) {
                    // Crear los grupos adicionales necesarios
                    $this->crearGruposAdicionales($gincana, $gruposActuales, $nuevaCantidad);
                } elseif ($nuevaCantidad < $gruposActuales) {
                    // Eliminar los grupos sobrantes (los más nuevos primero)
                    $this->eliminarGruposSobrantes($gincana, $nuevaCantidad);
                }
            }
        });
    
        return response()->json([
            'success' => true,
            'message' => 'Gincana actualizada correctamente',
            'data' => $gincana->load('grupos')
        ]);
    }

    /**
     * Elimina una gincana (admin)
     */
    public function destroy($id)
    {
        $gincana = Gincana::findOrFail($id);
        
        DB::transaction(function () use ($gincana) {
            // 1. Eliminar jugadores de los grupos de esta gincana
            Jugador::whereIn('id_grupo', $gincana->grupos()->pluck('id'))->delete();
            
            // 2. Eliminar los grupos asociados
            $gincana->grupos()->delete();
            
            // 3. Eliminar los niveles asociados
            Nivel::where('id_gincana', $gincana->id)->delete();
            
            // 4. Finalmente eliminar la gincana
            $gincana->delete();
        });
    
        return response()->json([
            'success' => true,
            'message' => 'Gincana eliminada completamente con todos sus grupos, jugadores y niveles'
        ]);
    }

    // --------- MÉTODOS PROTEGIDOS ---------

    /**
     * Crea los grupos para una gincana
     */
    protected function crearGruposParaGincana(Gincana $gincana)
    {
        $grupos = [];
        
        for ($i = 1; $i <= $gincana->cantidad_grupos; $i++) {
            $grupos[] = [
                'nombre' => 'Grupo '.$i,
                'nivel' => 0,
                'id_gincana' => $gincana->id,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        // Insertar todos los grupos en una sola operación
        Grupo::insert($grupos);
    }

    /**
     * Crea grupos adicionales para una gincana
     */
    protected function crearGruposAdicionales(Gincana $gincana, $cantidadActual, $nuevaCantidad)
    {
        // Obtener el número más alto actual de grupo
        $ultimoNumero = $gincana->grupos()
            ->orderByRaw('CAST(SUBSTRING(nombre, 7) AS UNSIGNED) DESC')
            ->value(DB::raw('CAST(SUBSTRING(nombre, 7) AS UNSIGNED)'));
        
        $numeroInicial = $ultimoNumero ? $ultimoNumero + 1 : $cantidadActual + 1;
        $grupos = [];
        
        for ($i = $numeroInicial; $i <= $numeroInicial + ($nuevaCantidad - $cantidadActual) - 1; $i++) {
            $grupos[] = [
                'nombre' => 'Grupo '.$i,
                'nivel' => 0,
                'id_gincana' => $gincana->id,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        Grupo::insert($grupos);
    }

    /**
     * Elimina grupos sobrantes de una gincana
     */
    protected function eliminarGruposSobrantes(Gincana $gincana, $nuevaCantidad)
    {
        // Obtener los grupos ordenados por el número en el nombre (de mayor a menor)
        $gruposAEliminar = $gincana->grupos()
            ->orderByRaw('CAST(SUBSTRING(nombre, 7) AS UNSIGNED) ASC')
            ->skip($nuevaCantidad)
            ->take(PHP_INT_MAX)
            ->get();
        
        // Primero eliminamos los grupos que no tienen jugadores (los más nuevos)
        $gruposSinJugadores = $gruposAEliminar->filter(function($grupo) {
            return $grupo->jugadores()->count() === 0;
        });
        
        if ($gruposSinJugadores->isNotEmpty()) {
            Grupo::whereIn('id', $gruposSinJugadores->pluck('id'))->delete();
        }
        
        // Si todavía necesitamos eliminar más grupos para llegar a la cantidad deseada
        $eliminados = $gruposSinJugadores->count();
        $necesariosEliminar = $gruposAEliminar->count() - $nuevaCantidad;
        
        if ($eliminados < $necesariosEliminar) {
            $gruposRestantes = $gruposAEliminar->whereNotIn('id', $gruposSinJugadores->pluck('id'))
                ->sortByDesc(function($grupo) {
                    return (int) str_replace('Grupo ', '', $grupo->nombre);
                })
                ->take($necesariosEliminar - $eliminados);
            
            // Eliminar jugadores de estos grupos primero
            Jugador::whereIn('id_grupo', $gruposRestantes->pluck('id'))->delete();
            
            // Luego eliminar los grupos
            Grupo::whereIn('id', $gruposRestantes->pluck('id'))->delete();
        }
    }
}