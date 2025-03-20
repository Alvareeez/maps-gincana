<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Muestra el formulario de login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Maneja la autenticación del usuario
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->intended('/home');
        }

        return back()->withErrors(['email' => 'Credenciales incorrectas.']);
    }

    // Muestra el formulario de registro
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Maneja el registro de un nuevo usuario
    public function register(Request $request)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios',
            'password' => 'required|string|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return redirect()->route('register')
                ->withErrors($validator)
                ->withInput();
        }

        // Crear el usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Iniciar sesión automáticamente después del registro
        Auth::login($user);

        return redirect('/home');
    }

    // Maneja el logout del usuario
    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
