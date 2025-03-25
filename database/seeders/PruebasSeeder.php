<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class PruebasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pruebas')->insert([
            'pregunta' => '¿Quién grabó un famoso video aquí?',
            'respuesta' => 'Jordi',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
