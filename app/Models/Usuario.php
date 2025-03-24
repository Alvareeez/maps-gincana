<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;  // Esto es importante si quieres usar notificaciones

    // Nombre de la tabla
    protected $table = 'usuarios';

    // Los atributos que se pueden asignar en masa
    protected $fillable = [
        'nombre',
        'apellido',   // Asegúrate de que 'apellido' esté también en fillable si es necesario
        'email',
        'password',
        'username',   // Agregar 'username' aquí
        'id_rol',
    ];
    

    // Los atributos que deberían ser ocultados para los arrays
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Los atributos que deberían ser convertidos a tipo primitivo
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relación de muchos a uno con Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'id_rol');
    }

    // Relación uno a muchos con Jugador
    public function jugadores()
    {
        return $this->hasMany(Jugador::class, 'id_usuario');
    }

    // Relación uno a muchos con LugarPersonalizado
    public function lugaresPersonalizados()
    {
        return $this->hasMany(LugarPersonalizado::class, 'id_usuario');
    }

    // Relación uno a muchos con Gincana
    public function gincanas()
    {
        return $this->hasMany(Gincana::class, 'id_ganador');
    }

    public function favoritos()
{
    return $this->hasMany(Favorito::class, 'usuario_id');
}

}
