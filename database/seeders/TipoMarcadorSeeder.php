<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoMarcadorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tipo_marcador')->insert([
            ['nombre' => 'COLEGIO', 'icono' => null],
            ['nombre' => 'RESTAURANTE', 'icono' => null],
            ['nombre' => 'PARQUE', 'icono' => null],
            ['nombre' => 'CAFETERIA', 'icono' => null],
            ['nombre' => 'HOTEL', 'icono' => null],
        ]);
    }
}
