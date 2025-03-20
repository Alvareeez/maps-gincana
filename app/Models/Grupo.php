<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table = 'grupos';  // Nombre de la tabla

    public function gincana()
    {
        return $this->belongsTo(Gincana::class, 'id_gincana');  // Relación de muchos a uno con Gincana
    }

    public function jugadores()
    {
        return $this->hasMany(Jugador::class, 'id_grupo');  // Relación uno a muchos con Jugador
    }
}
