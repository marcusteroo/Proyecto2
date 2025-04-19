<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Workflow;

class WorkflowTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_create_workflow()
    {
        // Eliminar workflows existentes para evitar conflictos
        Workflow::where('nombre', 'Workflow de Prueba')->delete();

        // Crear un usuario para autenticación
        $user = User::factory()->create();
        
        // Autenticar al usuario
        $this->actingAs($user);

        // Datos del workflow a crear
        $workflowData = [
            'nombre' => 'Workflow de Prueba',
            'descripcion' => 'Este es un workflow de prueba creado para testing',
            'trigger' => [
                'type' => 'manual',
                'params' => []
            ],
            'status' => 'active'
        ];

        // Hacer la petición para crear el workflow
        $response = $this->postJson('/api/workflows', $workflowData);

        // Verificar respuesta exitosa con ID de workflow
        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'id_workflow'
                 ]);

        // Verificar que el workflow se creó en la base de datos
        $this->assertDatabaseHas('workflows', [
            'nombre' => 'Workflow de Prueba',
            'descripcion' => 'Este es un workflow de prueba creado para testing',
            'status' => 'active',
            'id_creador' => $user->id
        ]);
        
        // Obtener el workflow creado y verificar sus propiedades
        $createdWorkflowId = $response->json('id_workflow');
        $workflow = Workflow::find($createdWorkflowId);
        
        $this->assertEquals('Workflow de Prueba', $workflow->nombre);
        $this->assertEquals('active', $workflow->status);
        $this->assertEquals($user->id, $workflow->id_creador);
    }
}