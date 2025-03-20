<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jugador extends Model
{
    protected $table = 'jugadores';  // Nombre de la tabla

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');  // Relación de muchos a uno con Usuario
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo');  // Relación de muchos a uno con Grupo
    }
}
