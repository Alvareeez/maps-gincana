<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_rol');
    }
}
