<?php

namespace App\Http\Controllers;

use App\Models\Prueba;
use Illuminate\Http\Request;

class PruebaController extends Controller
{
// Crear nueva prueba
public function store(Request $request)
{
    // Validar los datos
    $validated = $request->validate([
        'pregunta' => 'required|string',
        'respuesta' => 'required|string',
    ]);

    // Crear la nueva prueba
    $prueba = Prueba::create($validated);

    return response()->json($prueba, 201); // Responder con la prueba creada
}

// Actualizar una prueba
public function update(Request $request, $id)
{
    // Validación de datos
    $validatedData = $request->validate([
        'pregunta' => 'required|string|max:255',
        'respuesta' => 'required|string|max:255',
    ]);

    // Buscar la prueba y actualizarla
    $prueba = Prueba::findOrFail($id);
    $prueba->pregunta = $request->pregunta;
    $prueba->respuesta = $request->respuesta;
    $prueba->save();

    return response()->json($prueba);  // Retornar la prueba actualizada en formato JSON
}


    public function destroy($id)
    {
        $prueba = Prueba::findOrFail($id);
        $prueba->delete();

        return response()->json(['message' => 'Prueba eliminada correctamente']);
    }
}
