<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kanban;
use App\Models\Tablero; // Añade el modelo Tablero
use Illuminate\Support\Facades\DB; // Añade esta línea
use Illuminate\Support\Facades\Log; // Añade esta línea

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
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'estado' => $request->estado,
            'id_tablero' => $request->id_tablero
        ]);
    
        return response()->json(['id_tarea' => $kanban->id_tarea], 201);
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
            'id_tarea' => $request->id_tarea,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'estado' => $request->estado,
            'id_tablero' => $request->id_tablero
        ]);

        return response()->json(['message' => 'Kanban actualizado'], 200);
    }

    public function destroy($id)
    {
        $kanban = Kanban::findOrFail($id);
        $kanban->delete();

        return response()->json(['message' => 'Kanban eliminado'], 200);
    }
    public function getBoards()
    {
        try {
            // Usar el modelo Tablero en lugar de consultar directamente la tabla tareas
            $boards = Tablero::all()->map(function($tablero) {
                return [
                    'id' => $tablero->id_tablero,
                    'title' => $tablero->nombre
                ];
            });

            return response()->json($boards);
        } catch (\Exception $e) {
            Log::error('Error en getBoards: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function getTasks($idTablero)
    {
        try {
            // Buscar tareas filtrando por el id_tablero específico
            $tareas = Kanban::where('id_tablero', $idTablero)->get();
            return response()->json($tareas);
        } catch (\Exception $e) {
            Log::error('Error al obtener tareas del tablero: ' . $e->getMessage());
            return response()->json(['error' => 'Error al cargar las tareas'], 500);
        }
    }
}
