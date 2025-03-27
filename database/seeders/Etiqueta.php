<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Etiqueta extends Seeder
{
    public function run()
    {
        $etiquetas = ['BAR', 'RESTAURANTE', 'COLEGIO', 'DISCOTECA', 'HOTEL', 'CAFETERIA'];

        foreach ($etiquetas as $etiqueta) {
            DB::table('etiquetas')->insert(['nombre' => $etiqueta]);
        }
    }
}
