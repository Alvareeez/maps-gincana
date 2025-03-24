<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Función para mostrar la vista de administración
    public function index()
    {
        return view('admin.index'); // Asegúrate de tener la vista 'admin.index'
    }
}
