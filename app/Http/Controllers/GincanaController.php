<?php

namespace App\Http\Controllers;

use App\Models\Gincana;
use App\Models\Grupo;
use Illuminate\Http\Request;

class GincanaController extends Controller
{
    public function index()
    {
        $gincanas = Gincana::with(['ganadorGrupo'])->get();
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

        $gincana = Gincana::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Gincana creada correctamente',
            'data' => $gincana
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

        $gincana->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Gincana actualizada correctamente',
            'data' => $gincana
        ]);
    }

    public function destroy($id)
    {
        $gincana = Gincana::findOrFail($id);
        $gincana->delete();

        return response()->json([
            'success' => true,
            'message' => 'Gincana eliminada correctamente'
        ]);
    }
}