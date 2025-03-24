<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Prueba; 
use App\Models\Role;
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
            $pruebas = Prueba::all();
            return view('admin.index', compact('users','roles','pruebas')); 
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
    public function update(Request $request, $id)
    {
        // Validación de los datos (ajusta según tus necesidades)
        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email,' . $id,
            'password' => 'nullable|string|min:8',
            'id_rol' => 'required|integer',
        ]);
    
        // Buscar el usuario
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
    
        // Actualizar los campos del usuario
        $usuario->username = $request->input('username');
        $usuario->nombre = $request->input('nombre');
        $usuario->apellido = $request->input('apellido');
        $usuario->email = $request->input('email');
    
        // Solo actualizar la contraseña si se proporciona
        if ($request->filled('password')) {
            $usuario->password = bcrypt($request->input('password'));
        }
    
        $usuario->id_rol = $request->input('id_rol');
    
        // Guardar el usuario actualizado
        $usuario->save();
    
        return response()->json(['success' => 'Usuario actualizado correctamente']);
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
