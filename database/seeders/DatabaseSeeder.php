<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Usuario; // Asegúrate de usar el modelo correcto
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar el seeder para los roles
        $this->call(RolesSeeder::class);

        // Ejecutar el seeder para los usuarios
        $this->call(UsuariosSeeder::class);
    }
}
