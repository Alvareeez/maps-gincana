<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LugaresSeeder extends Seeder
{
    public function run()
    {
        DB::table('lugares')->insert([
            [
                'nombre' => 'Jesuites Joan 23',
                'pista' => 'Colegio',
                'latitud' => 41.3851, // Ejemplo de latitud
                'longitud' => 2.1734,  // Ejemplo de longitud
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Puedes agregar más lugares si lo deseas
            [
                'nombre' => 'Jesuites Joan 24',
                'pista' => 'Cole',
                'latitud' => 41.3860,
                'longitud' => 2.1740,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
