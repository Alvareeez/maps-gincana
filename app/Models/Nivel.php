<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nivel extends Model
{
    protected $table = 'niveles';  // Nombre de la tabla

    protected $fillable = [
        'nombre',
        'id_lugar',
        'id_prueba',
        'id_gincana',
    ];


    public function lugar()
    {
        return $this->belongsTo(Lugar::class, 'id_lugar');  // Relación de muchos a uno con Lugar
    }

    public function prueba()
    {
        return $this->belongsTo(Prueba::class, 'id_prueba');  // Relación de muchos a uno con Prueba
    }

    public function gincana()
    {
        return $this->belongsTo(Gincana::class, 'id_gincana');  // Relación de muchos a uno con Gincana
    }
}
