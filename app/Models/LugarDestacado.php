<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LugarDestacado extends Model
{
    protected $table = 'lugares_destacados';  // Nombre de la tabla

    public function etiquetas()
    {
        return $this->belongsToMany(Etiqueta::class, 'lugar_etiqueta', 'id_lugar', 'id_etiqueta');  // Relación muchos a muchos con Etiqueta
    }

    public function favoritos()
    {
        return $this->hasMany(Favorito::class, 'lugar_destacado_id');
    }

}
