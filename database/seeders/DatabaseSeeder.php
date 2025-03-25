<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Usuario;
use App\Models\Prueba;
use App\Models\Lugar;
use App\Models\Nivel;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesSeeder::class);

        $this->call(UsuariosSeeder::class);

        $this->call(PruebasSeeder::class);

        $this->call(LugaresSeeder::class);

        $this->call(GincanasSeeder::class);

        $this->call(NivelSeeder::class);


    }
}
