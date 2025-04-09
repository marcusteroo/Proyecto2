<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Workflow;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkflowController extends Controller
{
    public function index()
{
    // 1. Obtener los workflows creados por el usuario
    $ownedWorkflows = Workflow::where('id_creador', Auth::id())->get()
        ->map(function($workflow) {
            $workflow->is_shared = false;
            $workflow->is_owner = true;
            return $workflow;
        });
    
    // 2. Obtener los workflows compartidos con el usuario
    $sharedWorkflowIds = \DB::table('usuarios_workflows')
        ->where('user_id', Auth::id())
        ->pluck('workflow_id');
    
    $sharedWorkflows = Workflow::whereIn('id_workflow', $sharedWorkflowIds)
        ->where('id_creador', '!=', Auth::id())
        ->get()
        ->map(function($workflow) {
            // Buscar quién compartió este workflow (el creador)
            $creador = User::find($workflow->id_creador);
            $rol = \DB::table('usuarios_workflows')
                ->where('workflow_id', $workflow->id_workflow)
                ->where('user_id', Auth::id())
                ->value('rol');
            
            $workflow->is_shared = true;
            $workflow->is_owner = false;
            $workflow->shared_by = $creador ? $creador->name : 'Usuario desconocido';
            $workflow->shared_role = $rol;
            return $workflow;
        });
    
    // 3. Combinar ambos conjuntos de workflows
    $allWorkflows = $ownedWorkflows->concat($sharedWorkflows);
    
    return response()->json($allWorkflows);
}
    
    public function store(Request $request)
    {
        $workflow = Workflow::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'trigger_type' => $request->trigger['type'] ?? null,
            'trigger_params' => json_encode($request->trigger ?? []),
            'status' => 'active',
            'id_creador' => auth()->id() // Asignar el usuario autenticado
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
public function shareWorkflow(Request $request, $id)
{
    $workflow = Workflow::findOrFail($id);
    
    // Verificar que el usuario actual es el propietario
    if (!$workflow->isOwnedBy(Auth::id())) {
        return response()->json([
            'message' => 'No tienes permisos para compartir este workflow'
        ], 403);
    }
    
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'rol' => 'required|in:editor,espectador'
    ]);
    
    // Verificar que no se esté compartiendo consigo mismo
    if ($request->user_id == Auth::id()) {
        return response()->json([
            'message' => 'No puedes compartir el workflow contigo mismo'
        ], 422);
    }
    
    // Compartir el workflow
    $workflow->users()->syncWithoutDetaching([
        $request->user_id => [
            'rol' => $request->rol,
            'fecha_compartido' => now()
        ]
    ]);
    
    return response()->json([
        'message' => 'Workflow compartido exitosamente'
    ]);
}
public function getPotentialUsers()
    {
        $users = User::select('id', 'name', 'email')
                ->where('id', '!=', Auth::id())
                ->get();
                
        return response()->json($users);
    }
public function getAllWorkflows()
{
    $workflows = Workflow::all();
    
    return response()->json($workflows);
}
}
