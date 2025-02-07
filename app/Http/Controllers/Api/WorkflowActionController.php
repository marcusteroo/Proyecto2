<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkflowAction;

class WorkflowActionController extends Controller
{
    public function store(Request $request, $id)
    {
        $action = WorkflowAction::create([
            'id_workflow' => $id,
            'name' => $request->name,
            'description' => $request->description,
            'x_position' => $request->x_position,
            'y_position' => $request->y_position,
        ]);

        return response()->json(['id_action' => $action->id_action], 201);
    }
}