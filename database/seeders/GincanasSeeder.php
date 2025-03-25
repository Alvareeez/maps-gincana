<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GincanasSeeder extends Seeder
{
    public function run()
    {
        // Insertar datos de prueba
        DB::table('gincanas')->insert([
            [
                'nombre' => 'Gincana Aventura',
                'estado' => 'abierta',
                'cantidad_jugadores' => 20,
                'cantidad_grupos' => 4,
                'id_ganador' => null, // No hay ganador todavía
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'Gincana Ciencia',
                'estado' => 'ocupada',
                'cantidad_jugadores' => 15,
                'cantidad_grupos' => 3,
                'id_ganador' => 1, // Suponiendo que el usuario con ID 1 ha ganado
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'Gincana Exploración',
                'estado' => 'abierta',
                'cantidad_jugadores' => 30,
                'cantidad_grupos' => 5,
                'id_ganador' => null, // No hay ganador todavía
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
