<?php

namespace App\Http\Controllers;

use App\Models\Lugar;
use Illuminate\Http\Request;

class LugarController extends Controller
{
    // Método para guardar un lugar
    public function store(Request $request)
    {
        $lugar = Lugar::create([
            'pista' => $request->input('pista'),
            'latitud' => $request->input('latitud'),
            'longitud' => $request->input('longitud'),
        ]);

        return response()->json($lugar);
    }

    // Método para eliminar un lugar
    public function destroy($id)
    {
        $lugar = Lugar::findOrFail($id);
        $lugar->delete();

        return response()->json(['success' => true]);
    }
}
