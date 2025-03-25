<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MapaController extends Controller
{
    // Asegúrate de que el método sea público
    public function index()
    {
        return view('mapa'); 
    }
}
