<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    public function run()
    {
        // Insertar usuarios con roles definidos directamente
        DB::table('usuarios')->insert([
            [
                'username' => 'admin',
                'nombre' => 'Admin',
                'apellido' => 'User',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('qweQWE123'),
                'id_rol' => 1, // ID de 'Admin' (asumiendo que el rol Admin tiene id 1)
            ],
            [
                'username' => 'usuario1',
                'nombre' => 'Juan',
                'apellido' => 'Pérez',
                'email' => 'juan.perez@gmail.com',
                'password' => Hash::make('qweQWE123'),
                'id_rol' => 2, // ID de 'Usuario' (asumiendo que el rol Usuario tiene id 2)
            ],
            [
                'username' => 'usuario2',
                'nombre' => 'Ana',
                'apellido' => 'Gómez',
                'email' => 'ana.gomez@gmail.com',
                'password' => Hash::make('qweQWE123'),
                'id_rol' => 2, // ID de 'Usuario'
            ],
            [
                'username' => 'editor1',
                'nombre' => 'Carlos',
                'apellido' => 'Sánchez',
                'email' => 'carlos.sanchez@gmail.com',
                'password' => Hash::make('qweQWE123'),
                'id_rol' => 2, // ID de 'Usuario'
            ],
            [
                'username' => 'editor2',
                'nombre' => 'Marta',
                'apellido' => 'López',
                'email' => 'marta.lopez@gmail.com',
                'password' => Hash::make('qweQWE123'),
                'id_rol' => 2, // ID de 'Usuario'
            ]
        ]);
    }
}
