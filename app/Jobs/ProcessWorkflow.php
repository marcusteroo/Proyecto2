<?php

namespace App\Jobs;

use App\Models\Workflow;
use App\Models\WorkflowExecution;
use App\Models\WorkflowActionLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessWorkflow implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $workflow;

    /**
     * Create a new job instance.
     */
    public function __construct(Workflow $workflow)
    {
        $this->workflow = $workflow;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Registrar inicio de ejecución
        $execution = WorkflowExecution::create([
            'id_workflow' => $this->workflow->id_workflow,
            'status' => 'running',
            'trigger_data' => $this->workflow->trigger_params
        ]);

        try {
            // Obtener todas las acciones del workflow en orden
            $actions = $this->workflow->actions()->orderBy('order_index')->get();
            
            foreach ($actions as $action) {
                // Registrar inicio de acción
                $actionLog = WorkflowActionLog::create([
                    'id_execution' => $execution->id_execution,
                    'id_action' => $action->id_action,
                    'status' => 'running'
                ]);

                try {
                    // Procesar según el tipo de acción
                    switch ($action->action_type) {
                        case 'Tarea':
                            // El trigger ya se ejecutó, nada que hacer aquí
                            break;
                            
                        case 'Enviar Email':
                            $this->processEmailAction($action);
                            break;
                            
                        case 'Esperar':
                            $this->processWaitAction($action);
                            break;
                    }

                    // Marcar acción como completada
                    $actionLog->update([
                        'status' => 'completed',
                        'completed_at' => now()
                    ]);

                } catch (\Exception $e) {
                    // Marcar acción como fallida
                    $actionLog->update([
                        'status' => 'failed',
                        'result' => $e->getMessage(),
                        'completed_at' => now()
                    ]);
                    
                    throw $e; // Re-lanzar para detener todo el workflow
                }
            }

            // Marcar ejecución como completada
            $execution->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);

        } catch (\Exception $e) {
            // Marcar ejecución como fallida
            $execution->update([
                'status' => 'failed',
                'result' => $e->getMessage(),
                'completed_at' => now()
            ]);
            
            Log::error('Error al ejecutar workflow: ' . $e->getMessage());
        }
    }

    /**
     * Procesa una acción de tipo Email
     */
    protected function processEmailAction($action): void
{
    $config = json_decode($action->config, true);
    
    if (empty($config['to']) || empty($config['subject']) || empty($config['message'])) {
        Log::error("Configuración de email incompleta para la acción #{$action->id_action}");
        throw new \Exception("Configuración de email incompleta");
    }
    
    try {
        // Enviar correo usando la plantilla
        Mail::send('emails.workflow-notification', [
            'subject' => $config['subject'],
            'message' => $config['message']
        ], function ($message) use ($config) {
            $message->to($config['to']);
            $message->subject($config['subject']);
        });
        
        Log::info("Email enviado correctamente a: {$config['to']}, asunto: {$config['subject']}");
    } catch (\Exception $e) {
        Log::error("Error al enviar email: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Procesa una acción de tipo Esperar (versión de prueba)
 */
protected function processWaitAction($action): void
{
    $config = json_decode($action->config, true);
    $seconds = $config['seconds'] ?? 5;
    Log::info("SIMULACIÓN: Esperaría {$seconds} segundos");
    // No hacer sleep realmente para las pruebas
    // sleep($seconds);
}
}