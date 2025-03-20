<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LugarDestacado extends Model
{
    use HasFactory;

    protected $table = 'lugares_destacados'; // Nombre de la tabla
    protected $fillable = ['nombre', 'descripcion', 'direccion', 'latitud', 'longitud', 'tipoMarcador'];

    public function etiquetas()
    {
        return $this->belongsToMany(Etiqueta::class, 'lugar_etiqueta', 'id_lugar', 'id_etiqueta');  // Relación muchos a muchos con Etiqueta
    }
}
