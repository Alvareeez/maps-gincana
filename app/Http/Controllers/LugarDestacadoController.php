<?php

namespace App\Http\Controllers;

use App\Models\LugarDestacado;
use Illuminate\Http\Request;

class LugarDestacadoController extends Controller
{
    // Obtener todos los lugares destacados
    public function index()
    {
        $lugares = LugarDestacado::all();
        return response()->json($lugares);
    }
    public function buscar(Request $request)
    {
        $query = $request->input('query');

        // Buscar lugares por nombre o dirección
        $lugares = LugarDestacado::where('nombre', 'LIKE', "%{$query}%")
            ->orWhere('direccion', 'LIKE', "%{$query}%")
            ->get();

        return response()->json($lugares);
    }
    // Crear un nuevo lugar destacado
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'direccion' => 'required|string',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
            'tipoMarcador' => 'nullable|integer',
        ]);

        $lugar = LugarDestacado::create([
            'nombre' => $request->input('nombre'),
            'descripcion' => $request->input('descripcion'),
            'direccion' => $request->input('direccion'),
            'latitud' => $request->input('latitud'),
            'longitud' => $request->input('longitud'),
            'tipoMarcador' => $request->input('tipoMarcador') ?? 1, // Valor predeterminado
        ]);

        return response()->json($lugar);
    }

    // Actualizar un lugar destacado existente
    public function update(Request $request, $id)
    {
        $lugar = LugarDestacado::findOrFail($id);
        $lugar->update([
            'nombre' => $request->input('nombre'),
            'descripcion' => $request->input('descripcion'),
            'direccion' => $request->input('direccion'),
            'latitud' => $request->input('latitud'),
            'longitud' => $request->input('longitud'),
            'tipoMarcador' => $request->input('tipoMarcador'),
        ]);

        return response()->json($lugar);
    }

    // Eliminar un lugar destacado
    public function destroy($id)
    {
        $lugar = LugarDestacado::findOrFail($id);
        $lugar->delete();

        return response()->json(['success' => true, 'message' => 'Lugar destacado eliminado correctamente.']);
    }
}
