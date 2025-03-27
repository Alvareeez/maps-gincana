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
            ['nombre' => 'EDIFICIO', 'icono' => null],
            ['nombre' => 'OCIO', 'icono' => null],
            ['nombre' => 'REPOSTERIA', 'icono' => null],
            ['nombre' => 'DEPORTES', 'icono' => null],
            ['nombre' => 'INTERES', 'icono' => null],
        ]);
    }
}
