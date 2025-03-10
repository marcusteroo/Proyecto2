<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Workflow;

class WorkflowController extends Controller
{
    public function index()
    {
        $workflows = Workflow::all();
        return response()->json($workflows);
    }
    
    public function store(Request $request)
{
    $workflow = Workflow::create([
        'nombre' => $request->nombre,
        'descripcion' => $request->descripcion,
        'trigger_type' => $request->trigger['type'] ?? null,
        'trigger_params' => json_encode($request->trigger ?? []), // Cambio aquí: convertir a JSON
        'status' => 'active',
    ]);

    return response()->json(['id_workflow' => $workflow->id_workflow], 201);
}
    
    public function destroy($id)
    {
        $workflow = Workflow::findOrFail($id);
        $workflow->delete();    

        return response()->json(['message' => 'Workflow eliminado'], 200);
    }
    
    public function update(Request $request, $id)
{
    $workflow = Workflow::findOrFail($id);
    
    $workflow->update([
        'nombre' => $request->nombre,
        'descripcion' => $request->descripcion,
        'trigger_type' => $request->trigger['type'],
        'trigger_params' => json_encode($request->trigger),
        'status' => 'active'
    ]);
    
    return response()->json($workflow);
}
    
    public function show($id)
    {
        $workflow = Workflow::with('actions')->findOrFail($id);
        return response()->json($workflow);
    }

    public function getActions($id) 
    {
        $workflow = Workflow::findOrFail($id);
        $actions = $workflow->actions;
        return response()->json($actions);
    }
    public function deleteActions($id)
{
    $workflow = Workflow::findOrFail($id);
    // Eliminar todas las acciones del workflow
    $workflow->actions()->delete();
    
    return response()->json(['message' => 'Actions deleted']);
}
public function execute($id)
{
    $workflow = Workflow::find($id);
    
    if (!$workflow) {
        return response()->json([
            'success' => false,
            'message' => 'Workflow no encontrado'
        ], 404);
    }
    
    try {
        \App\Jobs\ProcessWorkflow::dispatchSync($workflow);
        
        return response()->json([
            'success' => true,
            'message' => "Workflow #{$id} ejecutado correctamente"
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => "Error: " . $e->getMessage()
        ], 500);
    }
}
}