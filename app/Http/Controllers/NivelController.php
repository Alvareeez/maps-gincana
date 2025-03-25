<?php

namespace App\Http\Controllers;

use App\Models\Nivel;
use App\Models\Lugar;
use App\Models\Prueba;
use App\Models\Gincana;
use Illuminate\Http\Request;

class NivelController extends Controller
{
    public function index()
    {
        $niveles = Nivel::with(['lugar', 'prueba', 'gincana'])->get();
        $lugares = Lugar::all();
        $pruebas = Prueba::all();
        $gincanas = Gincana::all();

        return view('niveles.index', compact('niveles', 'lugares', 'pruebas', 'gincanas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'id_lugar' => 'required|exists:lugares,id',
            'id_prueba' => 'required|exists:pruebas,id',
            'id_gincana' => 'required|exists:gincanas,id',
        ]);

        $nivel = Nivel::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Nivel creado correctamente',
            'data' => $nivel
        ]);
    }

    public function update(Request $request, $id)
    {
        $nivel = Nivel::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'id_lugar' => 'required|exists:lugares,id',
            'id_prueba' => 'required|exists:pruebas,id',
            'id_gincana' => 'required|exists:gincanas,id',
        ]);

        $nivel->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Nivel actualizado correctamente',
            'data' => $nivel
        ]);
    }

    public function destroy($id)
    {
        $nivel = Nivel::findOrFail($id);
        $nivel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Nivel eliminado correctamente'
        ]);
    }
}