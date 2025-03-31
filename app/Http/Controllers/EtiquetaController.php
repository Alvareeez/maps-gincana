<?php

namespace App\Http\Controllers;

use App\Models\Etiqueta;
use Illuminate\Http\Request;

class EtiquetaController extends Controller
{
    public function index()
    {
        $etiquetas = Etiqueta::all();
        return view('admin.etiquetas.index', compact('etiquetas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:etiquetas,nombre'
        ]);

        $etiqueta = Etiqueta::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Etiqueta creada correctamente',
            'data' => $etiqueta
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:etiquetas,nombre,'.$id
        ]);

        $etiqueta = Etiqueta::findOrFail($id);
        $etiqueta->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Etiqueta actualizada correctamente',
            'data' => $etiqueta
        ]);
    }

    public function destroy($id)
    {
        $etiqueta = Etiqueta::findOrFail($id);
        $etiqueta->delete();

        return response()->json([
            'success' => true,
            'message' => 'Etiqueta eliminada correctamente'
        ]);
    }
}