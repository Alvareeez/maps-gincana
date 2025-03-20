<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lugar extends Model
{
    protected $table = 'lugares';  // Nombre de la tabla

    public function niveles()
    {
        return $this->hasMany(Nivel::class, 'id_lugar');  // Relación uno a muchos con Nivel
    }
}
