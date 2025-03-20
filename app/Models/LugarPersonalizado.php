<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LugarPersonalizado extends Model
{
    protected $table = 'lugares_personalizados';  // Nombre de la tabla

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');  // Relación de muchos a uno con Usuario
    }
}
