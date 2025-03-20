<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';  // Nombre de la tabla

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_rol');  // Relación de muchos a uno con Role
    }

    public function jugadores()
    {
        return $this->hasMany(Jugador::class, 'id_usuario');  // Relación uno a muchos con Jugador
    }

    public function lugaresPersonalizados()
    {
        return $this->hasMany(LugarPersonalizado::class, 'id_usuario');  // Relación uno a muchos con LugarPersonalizado
    }

    public function gincanas()
    {
        return $this->hasMany(Gincana::class, 'id_ganador');  // Relación uno a muchos con Gincana
    }
}
