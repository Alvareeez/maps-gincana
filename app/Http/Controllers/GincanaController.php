<?php

namespace App\Http\Controllers;

use App\Models\Gincana;
use App\Models\Grupo;
use App\Models\Jugador;
use App\Models\Nivel;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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
            $gincanas = Gincana::where('estado', 'abierta')
            ->select('id', 'nombre', 'cantidad_jugadores', 'cantidad_grupos')
            ->get();

            if ($gincanas->isEmpty()) {
                $resultado = [
                    'estado' => 'no encontrado',
                    'respuesta' => 'No se han encontrado gincanas abiertas.'
                ];
            } else {
                $resultado = [
                    'estado' => 'encontrado',
                    'respuesta' => $gincanas
                ];
            }

            return response()->json($resultado);
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
            $gincana = Gincana::find($id);
            if ($gincana){
                if ($gincana->estado === 'abierta'){
                    return 'abierta';
                } else {
                    return 'cerrada';
                }
            } else{
                return 'no existe';
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

        // Si cambia el número de grupos, actualizamos
        if ($gincana->cantidad_grupos != $validated['cantidad_grupos']) {
            DB::transaction(function () use ($gincana, $validated) {
                // Eliminar grupos existentes
                $gincana->grupos()->delete();
                
                // Actualizar gincana
                $gincana->update($validated);
                
                // Crear nuevos grupos
                $this->crearGruposParaGincana($gincana);
            });
        } else {
            // Solo actualizar si no cambió el número de grupos
            $gincana->update($validated);
        }

        return response()->json([
            'success' => true,
            'message' => 'Gincana actualizada correctamente',
            'data' => $gincana->load('grupos')
        ]);
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
