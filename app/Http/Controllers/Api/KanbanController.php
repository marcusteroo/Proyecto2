<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kanban;

class KanbanController extends Controller
{
    public function index()
    {
        $kanbans = Kanban::all();
        return response()->json($kanbans);
    }

    public function store(Request $request)
    {
        $kanban = Kanban::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'estado' => $request->estado,
            'id_creador' => $request->id_creador
        ]);

        return response()->json(['id_tablero' => $kanban->id_tablero], 201);
    }

    public function show($id)
    {
        $kanban = Kanban::findOrFail($id);
        return response()->json($kanban);
    }

    public function update(Request $request, $id)
    {
        $kanban = Kanban::findOrFail($id);

        $kanban->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'estado' => $request->estado,
            'id_creador' => $request->id_creador
        ]);

        return response()->json(['message' => 'Kanban actualizado'], 200);
    }

    public function destroy($id)
    {
        $kanban = Kanban::findOrFail($id);
        $kanban->delete();

        return response()->json(['message' => 'Kanban eliminado'], 200);
    }
}
