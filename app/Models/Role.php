<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';  // Nombre de la tabla

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_rol');  // Relación uno a muchos con usuarios
    }
}
