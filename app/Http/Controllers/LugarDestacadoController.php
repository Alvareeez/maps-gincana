<?php

namespace App\Http\Controllers;

use App\Models\LugarDestacado;
use App\Models\Etiqueta;
use Illuminate\Http\Request;

class LugarDestacadoController extends Controller
{
    // Obtener todos los lugares destacados con posibilidad de filtrar por etiqueta
    public function index(Request $request)
    {
        try {
            $query = LugarDestacado::with('etiquetas', 'tipoMarcador'); // Cargar relaciones de etiquetas

            if ($request->has('etiqueta') && $request->etiqueta) {
                $query->whereHas('etiquetas', function ($q) use ($request) {
                    $q->where('etiquetas.id', $request->etiqueta);
                });
            }

            $lugares = $query->get();
            return response()->json($lugares);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar lugares',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Buscar lugares por nombre o dirección
    public function buscar(Request $request)
    {
        $query = $request->input('query');

        // Buscar lugares por nombre o dirección
        $lugares = LugarDestacado::where('nombre', 'LIKE', "%{$query}%")
            ->orWhere('direccion', 'LIKE', "%{$query}%")
            ->with('etiquetas')
            ->get();

        return response()->json($lugares);
    }

    // Crear un nuevo lugar destacado con etiquetas
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'direccion' => 'required|string',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
            'tipoMarcador' => 'nullable|integer',
            'etiquetas' => 'nullable|array',
            'etiquetas.*' => 'exists:etiquetas,id'
        ]);

        $lugar = LugarDestacado::create([
            'nombre' => $request->input('nombre'),
            'descripcion' => $request->input('descripcion'),
            'direccion' => $request->input('direccion'),
            'latitud' => $request->input('latitud'),
            'longitud' => $request->input('longitud'),
            'tipoMarcador' => $request->input('tipoMarcador') ?? 1, // Valor predeterminado
        ]);

        // Asociar etiquetas si se proporcionan
        if ($request->has('etiquetas')) {
            $lugar->etiquetas()->sync($request->etiquetas);
        }

        // Cargar las etiquetas en la respuesta
        $lugar->load('etiquetas');

        return response()->json($lugar);
    }

    // Actualizar un lugar destacado existente con etiquetas
    public function update(Request $request, $id)
    {
        $lugar = LugarDestacado::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'direccion' => 'required|string',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
            'tipoMarcador' => 'nullable|integer',
            'etiquetas' => 'nullable|array',
            'etiquetas.*' => 'exists:etiquetas,id'
        ]);

        $lugar->update([
            'nombre' => $request->input('nombre'),
            'descripcion' => $request->input('descripcion'),
            'direccion' => $request->input('direccion'),
            'latitud' => $request->input('latitud'),
            'longitud' => $request->input('longitud'),
            'tipoMarcador' => $request->input('tipoMarcador'),
        ]);

        // Sincronizar etiquetas si se proporcionan
        if ($request->has('etiquetas')) {
            $lugar->etiquetas()->sync($request->etiquetas);
        }

        // Cargar las etiquetas en la respuesta
        $lugar->load('etiquetas');

        return response()->json($lugar);
    }

    // Eliminar un lugar destacado (las relaciones se eliminarán en cascada)
    public function destroy($id)
    {
        $lugar = LugarDestacado::findOrFail($id);
        $lugar->delete();

        return response()->json(['success' => true, 'message' => 'Lugar destacado eliminado correctamente.']);
    }

    // Método para obtener un lugar específico con sus etiquetas
    public function show($id)
    {
        $lugar = LugarDestacado::with('etiquetas')->findOrFail($id);
        return response()->json($lugar);
    }
}
