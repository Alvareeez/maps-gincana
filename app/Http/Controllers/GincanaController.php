<?php

namespace App\Http\Controllers;

use App\Models\Gincana;
use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GincanaController extends Controller
{
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
        
        // Eliminar en transacción para asegurar integridad
        DB::transaction(function () use ($gincana) {
            // Primero eliminamos los grupos asociados
            $gincana->grupos()->delete();
            
            // Luego eliminamos la gincana
            $gincana->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Gincana y sus grupos eliminados correctamente'
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