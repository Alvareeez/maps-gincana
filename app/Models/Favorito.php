<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorito extends Model
{
    use HasFactory;

    protected $table = 'favoritos';

    protected $fillable = [
        'lista_id',
        'lugar_destacado_id',
        'tipoMarcador',
    ];
    
    public function lista()
    {
        return $this->belongsTo(Lista::class, 'id_lista');
    }

    // Relación con LugarDestacado
    public function lugarDestacado()
    {
        return $this->belongsTo(LugarDestacado::class, 'lugar_destacado_id');
    }
}
