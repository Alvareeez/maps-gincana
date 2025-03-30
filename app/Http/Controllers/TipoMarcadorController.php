<?php

namespace App\Http\Controllers;

use App\Models\TipoMarcador; // Asegúrate de que este modelo esté creado
use Illuminate\Http\Request;

class TipoMarcadorController extends Controller
{
    // Mostrar todos los tipos de marcadores
    public function index()
    {
        // Obtener todos los tipos de marcadores
        $tiposMarcadores = TipoMarcador::all();

        // Retornar la vista o los datos en formato JSON, dependiendo de lo que necesites
        return response()->json($tiposMarcadores);
    }

    // Crear un nuevo tipo de marcador
    public function store(Request $request)
    {
        // Validar los datos
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        try {
            // Crear el tipo de marcador
            $tipoMarcador = TipoMarcador::create([
                'nombre' => $request->nombre,
            ]);

            return response()->json([
                'message' => 'Tipo Marcador creado correctamente.',
                'data' => $tipoMarcador
            ], 201); // Código de respuesta 201 (creado)
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el tipo de marcador.',
                'error' => $e->getMessage(),
            ], 500); // Código de respuesta 500 (error del servidor)
        }
    }

    // Actualizar un tipo de marcador existente
    public function update(Request $request, $id)
    {
        // Validar los datos
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        // Buscar el tipo de marcador por ID
        $tipoMarcador = TipoMarcador::find($id);

        if (!$tipoMarcador) {
            return response()->json([
                'message' => 'Tipo Marcador no encontrado.'
            ], 404); // Código de respuesta 404 (no encontrado)
        }

        try {
            // Actualizar los datos del tipo de marcador
            $tipoMarcador->nombre = $request->nombre;
            $tipoMarcador->save();

            return response()->json([
                'message' => 'Tipo Marcador actualizado correctamente.',
                'data' => $tipoMarcador
            ], 200); // Código de respuesta 200 (éxito)
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el tipo de marcador.',
                'error' => $e->getMessage(),
            ], 500); // Código de respuesta 500 (error del servidor)
        }
    }

    // Eliminar un tipo de marcador
    public function destroy($id)
    {
        // Buscar el tipo de marcador por ID
        $tipoMarcador = TipoMarcador::find($id);

        if (!$tipoMarcador) {
            return response()->json([
                'message' => 'Tipo Marcador no encontrado.'
            ], 404); // Código de respuesta 404 (no encontrado)
        }

        try {
            // Eliminar el tipo de marcador
            $tipoMarcador->delete();

            return response()->json([
                'message' => 'Tipo Marcador eliminado correctamente.',
            ], 200); // Código de respuesta 200 (éxito)
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el tipo de marcador.',
                'error' => $e->getMessage(),
            ], 500); // Código de respuesta 500 (error del servidor)
        }
    }
}
