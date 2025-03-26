<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorito extends Model
{
    use HasFactory;

    protected $table = 'favoritos';

    protected $fillable = [
        'id_lista',
        'lugar_destacado_id',
        'tipoMarcador' // Mantenemos este nombre para coincidir con la migración
    ];

    public function lista()
    {
        return $this->belongsTo(Lista::class, 'id_lista');
    }

    public function lugarDestacado()
    {
        return $this->belongsTo(LugarDestacado::class, 'lugar_destacado_id');
    }

    // Añade esta relación
    public function tipoMarcador()
    {
        return $this->belongsTo(TipoMarcador::class, 'tipoMarcador');
    }
}
