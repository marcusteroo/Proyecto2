<?php 

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subtarea;
use Illuminate\Support\Facades\Log;

class SubtareaController extends Controller 
{
    public function index()
    {
        $subtareas = Subtarea::all();
        return response()->json($subtareas);
    }

    public function store(Request $request)
    {
        // Crea la subtarea con 'estado' = 0 (no completado) por defecto, 
        // o lo que llegue en el request
        $subtarea = Subtarea::create([
            'titulo' => $request->titulo,
            'estado' => $request->estado ?? 0,
            'id_tarea' => $request->id_tarea
        ]);

        return response()->json(['id_subtarea' => $subtarea->id_subtarea], 201);
    }

    public function show($id)
    {
        $subtarea = Subtarea::findOrFail($id);
        return response()->json($subtarea);
    }

    public function update(Request $request, $id)
    {
        $subtarea = Subtarea::findOrFail($id);

        $subtarea->update([
            'titulo' => $request->titulo,
            'estado' => $request->estado,
            'id_tarea' => $request->id_tarea
        ]);

        return response()->json(['message' => 'Subtarea actualizada'], 200);
    }

    public function destroy($id)
    {
        $subtarea = Subtarea::findOrFail($id);
        $subtarea->delete();

        return response()->json(['message' => 'Subtarea eliminada'], 200);
    }

    // Obtener subtareas por ID de tarea
    public function getSubtareasByTarea($idTarea)
    {
        try {
            // Opcional: logueamos el id recibido para confirmar que es correcto
            Log::info("Obteniendo subtareas para id_tarea: " . $idTarea);
    
            // Obtenemos la colección sin convertirla a array
            $subtareas = Subtarea::where('id_tarea', $idTarea)->get();
            return response()->json($subtareas);
        } catch (\Exception $e) {
            Log::error('Error al obtener subtareas de la tarea: ' . $e->getMessage());
            return response()->json(['error' => 'Error al cargar las subtareas'], 500);
        }
    }
    
    

    // Método para actualizar múltiples subtareas de una tarea
    public function updateSubtareas(Request $request, $idTarea)
    {
        try {
            $subtareasData = $request->input('subtareas', []);

            foreach ($subtareasData as $subtareaData) {
                // Si la subtarea tiene ID, se actualiza; si no, se crea
                if (isset($subtareaData['id_subtarea'])) {
                    $subtarea = Subtarea::findOrFail($subtareaData['id_subtarea']);
                    $subtarea->update([
                        'titulo' => $subtareaData['titulo'],
                        'estado' => $subtareaData['estado'] ?? 0
                    ]);
                } else {
                    Subtarea::create([
                        'titulo' => $subtareaData['titulo'],
                        'estado' => $subtareaData['estado'] ?? 0,
                        'id_tarea' => $idTarea
                    ]);
                }
            }

            return response()->json(['message' => 'Subtareas actualizadas'], 200);
        } catch (\Exception $e) {
            Log::error('Error al actualizar subtareas: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar subtareas'], 500);
        }
    }
}
