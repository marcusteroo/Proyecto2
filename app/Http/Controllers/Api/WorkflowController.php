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
        ]);

        return response()->json(['id_workflow' => $workflow->id_workflow], 201);
    }
    public function destroy($id)
    {
        $workflow = Workflow::findOrFail($id);
        $workflow->delete();

        return response()->json(['message' => 'Workflow eliminado'], 200);
    }
}