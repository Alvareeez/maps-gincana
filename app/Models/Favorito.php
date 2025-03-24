<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorito extends Model
{
    use HasFactory;

    protected $table = 'favoritos';

    protected $fillable = [
        'usuario_id',
        'lugar_destacado_id',
        'tipoMarcador',
    ];
    

    // Relación con Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Relación con LugarDestacado
    public function lugarDestacado()
    {
        return $this->belongsTo(LugarDestacado::class, 'lugar_destacado_id');
    }
}
