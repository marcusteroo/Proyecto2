<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkflowAction;
use App\Models\Workflow;

class WorkflowActionController extends Controller
{
    public function store(Request $request, $id)
    {
        // Obtener el workflow
        $workflow = Workflow::findOrFail($id);
        
        // Calcular el orden (última posición + 1)
        $latestOrderIndex = WorkflowAction::where('id_workflow', $id)
            ->max('order_index') ?? -1;
            
        $action = WorkflowAction::create([
            'id_workflow' => $id,
            'action_type' => $request->name, // Usamos el name como tipo de acción
            'name' => $request->name,
            'description' => $request->description,
            'order_index' => $latestOrderIndex + 1,
            'x_position' => $request->x_position,
            'y_position' => $request->y_position,
            'config' => $request->config,
        ]);

        return response()->json(['id_action' => $action->id_action], 201);
    }
    
    public function update(Request $request, $id)
    {
        $action = WorkflowAction::findOrFail($id);
        $action->update([
            'name' => $request->name,
            'action_type' => $request->name, // Actualizamos el tipo de acción
            'description' => $request->description,
            'x_position' => $request->x_position,
            'y_position' => $request->y_position,
            'config' => $request->config,
        ]);

        return response()->json(['message' => 'Acción actualizada'], 200);
    }
    
    public function getActions($id)
    {
        $actions = WorkflowAction::where('id_workflow', $id)
            ->orderBy('order_index')
            ->get();
        return response()->json($actions);
    }
}