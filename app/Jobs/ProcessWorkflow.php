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
//Esto es para ejecutar los workflows en segundo plano sin afectar a la pagina

class ProcessWorkflow implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $workflow;

    public function __construct(Workflow $workflow)
    {
        $this->workflow = $workflow;
    }

    public function handle(): void
    {
        
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
                            
                            break;
                            
                        case 'Enviar Email':
                            $this->processEmailAction($action);
                            break;
                    }

                  
                    $actionLog->update([
                        'status' => 'completed',
                        'completed_at' => now()
                    ]);

                } catch (\Exception $e) {
                    
                    $actionLog->update([
                        'status' => 'failed',
                        'result' => $e->getMessage(),
                        'completed_at' => now()
                    ]);
                    
                    throw $e; 
                }
            }

            
            $execution->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);

        } catch (\Exception $e) {
            
            $execution->update([
                'status' => 'failed',
                'result' => $e->getMessage(),
                'completed_at' => now()
            ]);
            
            Log::error('Error al ejecutar workflow: ' . $e->getMessage());
        }
    }

    /**
     * Procesa la accion del Email
     */
    protected function processEmailAction($action): void
    {
        // Verificar si config ya es un array o es una cadena JSON
        $config = is_string($action->config) ? json_decode($action->config, true) : $action->config;
        
        if (empty($config['to']) || empty($config['subject']) || empty($config['message'])) {
            Log::error("Configuración de email incompleta para la acción #{$action->id_action}");
            throw new \Exception("Configuración de email incompleta");
        }
        
        try {
            // Esto es para enviar el correo usando la plantilla que tenemos por defecto
            Mail::send('emails.workflow-notification', [
                'email_subject' => $config['subject'],   
                'email_message' => $config['message']    
            ], function ($message) use ($config) {
                // Esto es para usar el correo que tenemos configurado en la automatización
                $message->to($config['to']);
                $message->subject($config['subject']);
            });
            
            Log::info("Email enviado correctamente a: {$config['to']}, asunto: {$config['subject']}");
        } catch (\Exception $e) {
            Log::error("Error al enviar email: " . $e->getMessage());
            throw $e;
        }
    }
}