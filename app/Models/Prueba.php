<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prueba extends Model
{
    protected $table = 'pruebas'; 

    protected $fillable = ['pregunta', 'respuesta'];

    public function niveles()
    {
        return $this->hasMany(Nivel::class, 'id_prueba');  // Relación uno a muchos con Nivel
    }
}
