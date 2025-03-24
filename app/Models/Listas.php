<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lista extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'id_usuario'];

    /**
     * Obtiene el usuario al que pertenece la lista.
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    /**
     * Obtiene los favoritos asociados a la lista.
     */
    public function favoritos()
    {
        return $this->hasMany(Favorito::class, 'id_lista');
    }
}
