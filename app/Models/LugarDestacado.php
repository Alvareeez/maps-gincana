<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Factories\HasFactory;
=======
>>>>>>> 52327f8369e6e5e24774d51247681feb70f76882
use Illuminate\Database\Eloquent\Model;

class LugarDestacado extends Model
{
<<<<<<< HEAD
    use HasFactory;

    protected $table = 'lugares_destacados'; // Nombre de la tabla
    protected $fillable = ['nombre', 'descripcion', 'direccion', 'latitud', 'longitud', 'tipoMarcador'];
=======
    protected $table = 'lugares_destacados';  // Nombre de la tabla

    public function etiquetas()
    {
        return $this->belongsToMany(Etiqueta::class, 'lugar_etiqueta', 'id_lugar', 'id_etiqueta');  // Relación muchos a muchos con Etiqueta
    }
>>>>>>> 52327f8369e6e5e24774d51247681feb70f76882
}
