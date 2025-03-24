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
                'pista' => 'Jesuites Joan 23',
                'latitud' => 41.3851, // Ejemplo de latitud
                'longitud' => 2.1734,  // Ejemplo de longitud
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Puedes agregar más lugares si lo deseas
            [
                'pista' => 'Jesuites Joan 24',
                'latitud' => 41.3860,
                'longitud' => 2.1740,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
