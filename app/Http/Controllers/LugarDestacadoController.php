<?php

namespace App\Http\Controllers;

use App\Models\LugarDestacado;
use App\Models\Favorito;
use Illuminate\Http\Request;

class LugarDestacadoController extends Controller
{
    // Obtener todos los lugares destacados con posibilidad de filtrar por etiqueta
    public function index(Request $request)
    {
        try {
            $query = LugarDestacado::with(['etiquetas', 'tipoMarcador'])
                ->withCount(['favoritos' => function ($q) {
                    $q->whereNull('id_lista'); // Solo contar favoritos sin lista
                }]);

            // Filtro por etiqueta
            if ($request->has('etiqueta') && $request->etiqueta) {
                $query->whereHas('etiquetas', function ($q) use ($request) {
                    $q->where('etiquetas.id', $request->etiqueta);
                });
            }

            // Filtro por favoritos (ahora basado en favoritos sin lista)
            if ($request->has('favoritos')) {
                $favoritos = $request->favoritos === 'true';
                if ($favoritos) {
                    $query->has('favoritos', '>', 0); // Tiene al menos un favorito (sin lista)
                } else {
                    $query->doesntHave('favoritos'); // No tiene favoritos
                }
            }

            $lugares = $query->get()->map(function ($lugar) {
                $lugar->esFavorito = $lugar->favoritos_count > 0;
                return $lugar;
            });

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
    public function addToFavorites(Request $request)
    {
        $request->validate([
            'lugar_destacado_id' => 'required|exists:lugares_destacados,id',
            'tipoMarcador' => 'required|exists:tipo_marcador,id'
        ]);

        try {
            // Verificar si ya es favorito (sin lista específica)
            if (Favorito::where('lugar_destacado_id', $request->lugar_destacado_id)
                ->whereNull('id_lista')
                ->exists()
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este lugar ya está en tus favoritos'
                ], 409);
            }

            $favorito = Favorito::create([
                'lugar_destacado_id' => $request->lugar_destacado_id,
                'id_lista' => null, // Sin lista
                'tipoMarcador' => $request->tipoMarcador
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lugar añadido a favoritos',
                'data' => $favorito,
                'esFavorito' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al añadir a favoritos: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // App/Http/Controllers/LugarDestacadoController.php
    public function quitarDeFavoritos($id)
    {
        try {
            // Buscar y eliminar el favorito (sin lista específica)
            $favorito = Favorito::where('lugar_destacado_id', $id)
                ->whereNull('id_lista')
                ->first();

            if (!$favorito) {
                return response()->json([
                    'success' => false,
                    'message' => 'El lugar no estaba en favoritos'
                ], 404);
            }

            $favorito->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lugar quitado de favoritos correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al quitar de favoritos: ' . $e->getMessage()
            ], 500);
        }
    }
}
