<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NivelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('niveles')->insert([
            'nombre' => 'Nivel 1',
            'id_lugar' => 1,  // Usamos el ID 1 para el lugar
            'id_prueba' => 1,  // Usamos el ID 1 para la prueba
            'id_gincana' => 1,  // Usamos el ID 1 para la gincana
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
