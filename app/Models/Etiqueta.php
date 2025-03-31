<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etiqueta extends Model
{
    protected $table = 'etiquetas';  // Nombre de la tabla

    protected $fillable = [
        'nombre'
    ];

    public function lugares()
    {
        return $this->belongsToMany(LugarDestacado::class, 'lugar_etiqueta', 'id_etiqueta', 'id_lugar');  // Relación muchos a muchos con LugarDestacado
    }
}
