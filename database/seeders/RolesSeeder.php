<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run()
    {
        // Insertar roles básicos
        DB::table('roles')->insert([
            ['nombre' => 'Admin'],
            ['nombre' => 'Usuario'],
        ]);
    }
}
