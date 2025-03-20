<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LugarDestacado extends Model
{
    use HasFactory;

    protected $table = 'lugares_destacados'; // Nombre de la tabla
    protected $fillable = ['nombre', 'descripcion', 'direccion', 'latitud', 'longitud', 'tipoMarcador'];
}
