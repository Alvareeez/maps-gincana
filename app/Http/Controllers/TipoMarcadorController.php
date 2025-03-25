<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoMarcador;

class TipoMarcadorController extends Controller
{
    // Obtener todos los tipos de marcadores
    public function index()
    {
        $tipos = TipoMarcador::all();
        return response()->json($tipos);
    }
}
