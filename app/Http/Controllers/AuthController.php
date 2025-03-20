<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Mostrar el formulario de login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Manejar el login
    public function login(Request $request)
    {
        // Validación de los datos
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Intentar autenticar al usuario
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Si la autenticación es exitosa, redirige a la página principal
            return redirect()->intended('/home');
        }

        // Si la autenticación falla, redirige de nuevo al login con un mensaje de error
        return back()->withErrors(['email' => 'Las credenciales no coinciden.']);
    }

    // Manejar el logout
    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}

