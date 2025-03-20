<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gincana extends Model
{
    protected $table = 'gincanas';  // Nombre de la tabla

    public function ganador()
    {
        return $this->belongsTo(Usuario::class, 'id_ganador');  // Relación de muchos a uno con Usuario
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_gincana');  // Relación uno a muchos con Grupo
    }
}
