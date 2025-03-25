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
        return $this->belongsToMany(Etiqueta::class, 'lugar_etiqueta', 'id_lugar', 'id_etiqueta');
    }
    public function tipoMarcador()
    {
        return $this->belongsTo(TipoMarcador::class, 'tipoMarcador');
    }
    public function favoritos()
    {
        return $this->hasMany(Favorito::class, 'lugar_destacado_id');
    }
}
