<?php
namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        // Verificar si el usuario está autenticado y si tiene el rol de administrador
        if (Auth::check() && Auth::user()->id_rol == 1) {
            // Si es administrador, mostrar la vista
            $users = Usuario::all();  // O cualquier lógica para obtener los usuarios
            return view('admin.index', compact('users'));
        }

        // Si no es administrador, redirigir a la página principal con un mensaje de error
        return redirect('/')->with('error', 'No tienes acceso a esta sección');
    }
}
