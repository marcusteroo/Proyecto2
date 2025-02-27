<?php

namespace App\Observers;

use App\Models\Kanban;
use App\Models\Workflow;
use App\Jobs\ProcessWorkflow;
use Illuminate\Support\Facades\Log;

class KanbanObserver
{
    /**
     * Handle the Kanban "updated" event.
     */
    public function updated(Kanban $kanban): void
{
    Log::info("Tarea #{$kanban->id_tarea} actualizada");
    
    // Verifica si el estado ha cambiado
    if ($kanban->isDirty('estado')) {
        $estadoAnterior = $kanban->getOriginal('estado');
        $nuevoEstado = $kanban->estado;
        
        Log::info("Cambio de estado en tarea #{$kanban->id_tarea}: de '{$estadoAnterior}' a '{$nuevoEstado}'");
        $this->checkWorkflowTriggers($kanban);
    }
}

protected function checkWorkflowTriggers(Kanban $kanban): void
{
    Log::info("Buscando workflows para activar con la tarea #{$kanban->id_tarea}, estado='{$kanban->estado}'");
    
    $workflows = Workflow::where('trigger_type', 'task_status')
        ->where('status', 'active')
        ->get();
    
    Log::info("Se encontraron {$workflows->count()} workflows activos para revisar");
    
    foreach ($workflows as $workflow) {
        $triggerParams = json_decode($workflow->trigger_params, true);
        
        Log::info("Revisando workflow #{$workflow->id_workflow}: " . json_encode($triggerParams));
        
        // Continúa con tu lógica...
    }
}
}