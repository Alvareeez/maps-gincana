<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Role; // Asegúrate de tener el modelo Role si lo usas
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // Mostrar la vista de administración con los usuarios
    public function index()
    {
        // Verificar si el usuario está autenticado y si tiene el rol de administrador
        if (Auth::check() && Auth::user()->id_rol == 1) {
            // Obtener todos los usuarios
            $users = Usuario::all();
            $roles = Role::all(); 
            return view('admin.index', compact('users','roles')); 
        }

        // Si no es administrador, redirigir a la página principal con un mensaje de error
        return redirect('/')->with('error', 'No tienes acceso a esta sección');
    }

    // Método para añadir un usuario
    public function store(Request $request)
    {
        // Validación de los datos
        $validated = $request->validate([
            'username' => 'required|unique:usuarios,username',
            'nombre' => 'required',
            'apellido' => 'required',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|min:6',
            'id_rol' => 'required|exists:roles,id',
        ]);

        // Crear un nuevo usuario
        $usuario = Usuario::create([
            'username' => $validated['username'],
            'nombre' => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'id_rol' => $validated['id_rol'],
        ]);

        // Devolver la respuesta en formato JSON
        return response()->json(['usuario' => $usuario]);
    }

    // Método para actualizar un usuario
    public function update(Request $request, $id)
    {
        // Buscar al usuario por su ID
        $usuario = Usuario::findOrFail($id);

        // Validación de los datos
        $validated = $request->validate([
            'username' => 'required|unique:usuarios,username,' . $usuario->id,
            'nombre' => 'required',
            'apellido' => 'required',
            'email' => 'required|email|unique:usuarios,email,' . $usuario->id,
            'password' => 'nullable|min:6',
            'id_rol' => 'required|exists:roles,id',
        ]);

        // Actualizar el usuario
        $usuario->update([
            'username' => $validated['username'],
            'nombre' => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'email' => $validated['email'],
            'password' => $validated['password'] ? bcrypt($validated['password']) : $usuario->password,
            'id_rol' => $validated['id_rol'],
        ]);

        // Devolver la respuesta en formato JSON
        return response()->json(['usuario' => $usuario]);
    }

    // Método para eliminar un usuario
    public function destroy($id)
    {
        // Buscar al usuario por su ID
        $usuario = Usuario::findOrFail($id);

        // Eliminar el usuario
        $usuario->delete();

        // Devolver un mensaje de éxito
        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }
}
