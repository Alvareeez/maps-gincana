<?php

namespace App\Http\Controllers;

use App\Models\Nivel;
use App\Models\Lugar;
use App\Models\Prueba;
use App\Models\Gincana;
use Illuminate\Http\Request;

class NivelController extends Controller
{
    public function index()
    {
        $niveles = Nivel::with(['lugar', 'prueba', 'gincana'])->get();
        $lugares = Lugar::all();
        $pruebas = Prueba::all();
        $gincanas = Gincana::all();

        return view('niveles.index', compact('niveles', 'lugares', 'pruebas', 'gincanas'));
    }

    public function store(Request $request)
    {
        Nivel::create($request->all());
        return redirect()->route('niveles.index');
    }

    public function destroy($id)
    {
        $nivel = Nivel::findOrFail($id);
        $nivel->delete();

        return redirect()->route('niveles.index');
    }
}
