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
    try {
        Log::info("Buscando workflows para activar con la tarea #{$kanban->id_tarea}, estado='{$kanban->estado}'");
        
        $workflows = Workflow::where('trigger_type', 'task_status')
        ->where('status', 'active')
        ->get();
    
    Log::info("Se encontraron {$workflows->count()} workflows activos para revisar");
    
    foreach ($workflows as $workflow) {
        $triggerParams = json_decode($workflow->trigger_params, true);
        
        Log::info("Revisando workflow #{$workflow->id_workflow}: " . json_encode($triggerParams));
        
        // Verificar que coincida TANTO la tarea como el estado configurado
        if (
            isset($triggerParams['taskId']) && 
            isset($triggerParams['status']) && 
            $triggerParams['taskId'] == $kanban->id_tarea &&
            $triggerParams['status'] == $kanban->estado
        ) {
            Log::info("¡Coincidencia encontrada! Ejecutando workflow #{$workflow->id_workflow} para tarea #{$kanban->id_tarea} con estado '{$kanban->estado}'");
            
            // Ejecutar el workflow
            ProcessWorkflow::dispatch($workflow);
        } else {
            Log::info("El workflow #{$workflow->id_workflow} no coincide con la tarea #{$kanban->id_tarea} y estado '{$kanban->estado}'");
        }
    }
        
    } catch (\Exception $e) {
        Log::error("Error al procesar workflows para tarea #{$kanban->id_tarea}: {$e->getMessage()}");
    }
    
    
}
}