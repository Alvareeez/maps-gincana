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
    //--------- Gincana ---------
    // Vistas
        
        // Vista para las gincanas disponibles
        public function vistaGincanaMenu()
        {
            // Comprobar si el usuario ya está en un grupo
            $jugador = Jugador::where('id_usuario', Auth::id())->first();
            if ($jugador) {
                return redirect()->route('gincana.juego', ['id' => $jugador->grupo->id_gincana]);
            }
            return view('gincana.paginaPrincipal');
        }

        // Vista para los grupos disponibles
        public function vistaGincanaLobby($id)
        {
            // Comprobar si el usuario ya está en un grupo
            $jugador = Jugador::where('id_usuario', Auth::id())->first();
            if ($jugador) {
                return redirect()->route('gincana.juego', ['id' => $jugador->grupo->id_gincana]);
            }
            return view('gincana.menuLobby');
        }

        // Vista para el juego
        public function vistaGincanaJuego($id)
        {
            // Comprobar si el usuario está en un grupo de esta gincana
            $jugador = Jugador::where('id_usuario', Auth::id())->first();
            if (!$jugador || $jugador->grupo->id_gincana != $id) {
                return redirect()->route('gincana.menu');
            }
            return view('gincana.juego', ['id' => $id]);
        }

    // Funciones

        // Recupera todas las gincanas que están abiertas
        public function obtenerGincanasAbiertas()
        {
            // Obtener IDs de gincanas con exactamente 4 niveles
            $gincanasValidas = DB::table('niveles')
                ->select('id_gincana')
                ->groupBy('id_gincana')
                ->havingRaw('COUNT(*) = 4')
                ->pluck('id_gincana');

            // Obtener gincanas abiertas que están en esa lista
            $gincanas = Gincana::where('estado', 'abierta')
                ->whereIn('id', $gincanasValidas)
                ->select('id', 'nombre', 'cantidad_jugadores', 'cantidad_grupos')
                ->get();

            return response()->json([
                'estado' => $gincanas->isEmpty() ? 'no encontrado' : 'encontrado',
                'respuesta' => $gincanas->isEmpty() 
                    ? 'No hay gincanas disponibles.' 
                    : $gincanas,
                'debug_gincanas_validas' => $gincanasValidas // Para diagnóstico
            ]);
        }

        // Recupera la información de una gincana
        public function infoGincana($id)
        {
            $gincana = Gincana::find($id);

            if ($gincana) {
                $resultado = [
                    'estado' => 'encontrado',
                    'respuesta' => $gincana
                ];
            } else {
                $resultado = [
                    'estado' => 'no encontrado',
                    'respuesta' => 'No se ha encontrado la gincana.'
                ];
            }

            return response()->json($resultado);
        }

        // Comprueba si la gincana existe o sigue abierta
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
                } else {
                    return 'cerrada';
                }

            } catch (\Exception $e) {
                // Log del error si es necesario
                return 'cerrada';
            }
        }

        // Recupera los grupos de una gincana que estan disponibles
        public function obtenerGruposGincana($id)
        {
            if ($this->gincanaAbierta($id) != 'abierta') {
                $resultado = [
                    'estado' => 'no disponible',
                    'respuesta' => 'La gincana ya no esta disponible.'
                ];

                return response()->json($resultado);
            }

            // Obtener la cantidad máxima de jugadores por grupo
            $gincana = Gincana::find($id);
            $maxJugadores = $gincana->cantidad_jugadores;

            // Obtener grupos con la cantidad de jugadores actual
            $grupos = Grupo::where('id_gincana', $id)
                ->select('id', 'nombre')
                ->get()
                ->map(function ($grupo) use ($maxJugadores) {
                    $jugadoresActuales = Jugador::where('id_grupo', $grupo->id)->count();
                    return [
                        'id' => $grupo->id,
                        'nombre' => $grupo->nombre,
                        'jugadores_actuales' => $jugadoresActuales,
                        'max_jugadores' => $maxJugadores,
                        'disponible' => $jugadoresActuales < $maxJugadores
                    ];
                })
                ->filter(function ($grupo) {
                    return $grupo['disponible'];
                })
                ->values();
            
            if ($grupos->isEmpty()) {
                $resultado = [
                    'estado' => 'no encontrado',
                    'respuesta' => 'No se han encontrado grupos disponibles.'
                ];
            } else {
                $resultado = [
                    'estado' => 'encontrado',
                    'respuesta' => $grupos
                ];
            }

            return response()->json($resultado);
        }

        // Unirse a un grupo
        public function unirseAGrupo(Request $request){
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

                $idUser = Auth::user()->id;

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
                return response()->json([
                    'estado' => 'error',
                    'respuesta' => 'Error al unirse al grupo: ' . $e->getMessage()
                ]);
            }
        }

        // Función para salir del grupo
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
                return redirect()->route('gincana.menu')->with('error', 'Error al salir del grupo: ' . $e->getMessage());
            }
        }

        // Verificar si todos los grupos están completos
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

        // API para verificar estado del juego
        public function estadoJuego($gincanaId)
        {
            $jugador = Jugador::where('id_usuario', Auth::id())
                ->whereHas('grupo', function($q) use ($gincanaId) {
                    $q->where('id_gincana', $gincanaId);
                })
                ->first();
            
            if (!$jugador) {
                return response()->json([
                    'estado' => 'error',
                    'mensaje' => 'No estás en esta gincana'
                ], 403);
            }
            
            $todosConectados = $this->verificarGruposCompletos($gincanaId);
            
            if ($todosConectados) {
                // Actualizar estado de la gincana si no estaba ya actualizado
                Gincana::where('id', $gincanaId)
                    ->where('estado', 'abierta')
                    ->update(['estado' => 'ocupada']);
                    
                return response()->json([
                    'estado' => 'iniciado',
                    'mensaje' => 'El juego ha comenzado!'
                ]);
            } else {
                // Obtener información de los grupos para mostrar
                $grupos = Grupo::where('id_gincana', $gincanaId)
                    ->withCount('jugadores')
                    ->get()
                    ->map(function($grupo) use ($gincanaId, $jugador) {
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
        }

    // Admin
    
    public function index()
    {
        $gincanas = Gincana::with(['ganadorGrupo', 'grupos'])->get();
        $grupos = Grupo::all(); // Para el dropdown de ganador

        return view('gincanas.index', compact('gincanas', 'grupos'));
    }

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
            'data' => $gincana->load('grupos') // Cargar la relación de grupos en la respuesta
        ]);
    }

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

    protected function eliminarGruposSobrantes(Gincana $gincana, $nuevaCantidad)
    {
        // Obtener los grupos ordenados por el número en el nombre (de mayor a menor)
        $gruposAEliminar = $gincana->grupos()
            ->orderByRaw('CAST(SUBSTRING(nombre, 7) AS UNSIGNED) ASC') // Extrae el número del nombre "Grupo X"
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


    public function destroy($id)
    {
        $gincana = Gincana::findOrFail($id);
        
        DB::transaction(function () use ($gincana) {
            // 1. Eliminar jugadores de los grupos de esta gincana
            Jugador::whereIn('id_grupo', $gincana->grupos()->pluck('id'))->delete();
            
            // 2. Eliminar los grupos asociados
            $gincana->grupos()->delete();
            
            // 3. Eliminar los niveles asociados (aunque en la migración tienes onDelete('cascade'))
            Nivel::where('id_gincana', $gincana->id)->delete();
            
            // 4. Finalmente eliminar la gincana
            $gincana->delete();
        });
    
        return response()->json([
            'success' => true,
            'message' => 'Gincana eliminada completamente con todos sus grupos, jugadores y niveles'
        ]);
    }

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
}
