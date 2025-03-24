<?php

namespace App\Http\Controllers;

use App\Models\Lugar;
use Illuminate\Http\Request;

class LugarController extends Controller
{
    public function index()
    {
        $lugares = Lugar::all();
        return view('admin.lugares.index', compact('lugares'));
    }

    public function store(Request $request)
    {
        // Validación de los datos
        $request->validate([
            'pista' => 'required|string|max:255',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
        ]);
    
        // Crear el lugar
        $lugar = new Lugar();
        $lugar->pista = $request->input('pista');
        $lugar->latitud = $request->input('latitud');
        $lugar->longitud = $request->input('longitud');
        $lugar->save();
    
        return response()->json($lugar, 201);
    }
    
    public function update(Request $request, $id)
    {
        // Validación de los datos
        $request->validate([
            'pista' => 'required|string|max:255',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
        ]);
    
        // Encontrar el lugar
        $lugar = Lugar::findOrFail($id);
        $lugar->pista = $request->input('pista');
        $lugar->latitud = $request->input('latitud');
        $lugar->longitud = $request->input('longitud');
        $lugar->save();
    
        return response()->json($lugar, 200);
    }
    

    public function destroy($id)
    {
        $lugar = Lugar::find($id);
    
        if ($lugar) {
            $lugar->delete();
            return response()->json(['message' => 'Lugar eliminado correctamente']);
        }
    
    }
    
    
}
