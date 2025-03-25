<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gincana extends Model
{
    protected $table = 'gincanas';

    protected $fillable = [
        'nombre',
        'estado',
        'cantidad_jugadores',
        'cantidad_grupos',
        'id_ganador',
    ];

    // Relación con el grupo ganador
    public function ganadorGrupo()
    {
        return $this->belongsTo(Grupo::class, 'id_ganador');
    }

    // Relación con todos los grupos de la gincana
    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_gincana');
    }
}