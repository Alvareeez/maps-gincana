<?php

namespace App\Http\Controllers;

use App\Models\Prueba;
use Illuminate\Http\Request;

class PruebaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'pregunta' => 'required|string',
            'respuesta' => 'required|string',
        ]);

        $prueba = Prueba::create([
            'pregunta' => $request->pregunta,
            'respuesta' => $request->respuesta,
        ]);

        return response()->json(['prueba' => $prueba]);
    }

    public function update(Request $request, $id)
    {
        $prueba = Prueba::findOrFail($id);
        $prueba->update([
            'pregunta' => $request->pregunta,
            'respuesta' => $request->respuesta,
        ]);

        return response()->json(['message' => 'Prueba actualizada correctamente']);
    }

    public function destroy($id)
    {
        $prueba = Prueba::findOrFail($id);
        $prueba->delete();

        return response()->json(['message' => 'Prueba eliminada correctamente']);
    }
}
