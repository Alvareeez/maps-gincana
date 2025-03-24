<?php

namespace App\Http\Controllers;
use App\Models\Gincana;
use App\Models\Grupo;

use Illuminate\Http\Request;

class GincanaController extends Controller
{
    //--------- Gincana ---------
    // Vistas
        
        // Vista para las gincanas disponibles
        public function vistaGincanaMenu()
        {
            return view('gincana.paginaPrincipal');
        }

        // Vista para los grupos disponibles
        public function vistaGincanaLobby($id)
        {
            return view('gincana.menuLobby');
        }

    // Funciones

        // Recupera todas las gincanas que están abiertas
        public function obtenerGincanasAbiertas()
        {
            $gincanas = Gincana::where('estado', 'abierta')
            ->select('id', 'nombre', 'cantidad_jugadores', 'cantidad_grupos')
            ->get();

            if ($gincanas->isEmpty()) {
                $resultado = [
                    'estado' => 'no encontrado',
                    'respuesta' => 'No se han encontrado gincanas abiertas.'
                ];
            } else {
                $resultado = [
                    'estado' => 'encontrado',
                    'respuesta' => $gincanas
                ];
            }

            return response()->json($resultado);
        }

        // Comprueba si la gincana existe o sigue abierta
        public function gincanaAbierta($id)
        {
            $gincana = Gincana::find($id);
            if ($gincana){
                if ($gincana->estado === 'abierta'){
                    return 'abierta';
                } else {
                    return 'cerrada';
                }
            } else{
                return 'no existe';
            }
        }

        // Recupera los grupos de una gincana que estan disponibles
        public function obtenerGruposGincana($id)
        {
            if ($this->gincanaAbierta($id) != 'abierta') {
                $resultado = [
                    'estado' => 'no disponible',
                    'respuesta' => 'La gincana ya no esta disponible.'
                ];

                return response()->json($resultado);
            }

            // falta recuperar solo los que no estén llenos
            $grupos = Grupo::where('id_gincana', $id)
            ->select('id', 'nombre')
            ->get();
            
            if ($grupos->isEmpty()) {
                $resultado = [
                    'estado' => 'no encontrado',
                    'respuesta' => 'No se han encontrado grupos abiertos.'
                ];
            } else {
                $resultado = [
                    'estado' => 'encontrado',
                    'respuesta' => $grupos
                ];
            }

            return response()->json($resultado);
        }
}
