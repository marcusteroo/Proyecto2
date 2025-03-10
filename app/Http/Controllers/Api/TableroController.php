<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tablero;

class TableroController extends Controller
{
    public function index()
    {
        $tableros = Tablero::all();
        return response()->json($tableros);
    }
    
    public function store(Request $request)
    {
        $tablero = new Tablero();
        $tablero->nombre = $request->nombre;
        $tablero->id_creador = $request->id_creador;
        // Si enviamos el color desde el frontend
        if ($request->has('color_fondo')) {
            $tablero->color_fondo = $request->color_fondo;
        }
        $tablero->save();

        return response()->json(['id_tablero' => $tablero->id_tablero], 201);
    }
    
    public function destroy($id)
    {
        $tablero = Tablero::findOrFail($id);
        $tablero->delete();    

        return response()->json(['message' => 'Tablero eliminado'], 200);
    }
    
    public function update(Request $request, $id)
    {
        $tablero = Tablero::findOrFail($id);
        $tablero->update([
            'nombre' => $request->nombre ?? $tablero->nombre,
            'id_creador' => $request->id_creador ?? $tablero->id_creador,
        ]);

        return response()->json(['message' => 'Tablero actualizado'], 200);
    }
    
    public function show($id)
    {
        $tablero = Tablero::findOrFail($id);
        return response()->json($tablero);
    }
}
