<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;

use Tests\TestCase;
use App\Models\PrecioHome;

class PreciosTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_get_public_precios()
    {
        // Crear algunos precios de prueba
        Precio::create([
            'categoria' => 'basico',
            'nombre_plan' => 'Plan Básico',
            'descripcion' => 'Perfecto para pequeños equipos o proyectos personales',
            'precio_mensual' => 9.99,
            'precio_anual' => 99.90,
            'destacado' => false,
            'activo' => true,
            'caracteristicas' => [
                'Hasta 3 usuarios',
                'Almacenamiento básico de 5GB',
                'Soporte por email',
                'Funciones básicas de gestión de tareas'
            ]
        ]);

        Precio::create([
            'categoria' => 'basico',
            'nombre_plan' => 'Plan Básico',
            'descripcion' => 'Perfecto para pequeños equipos o proyectos personales',
            'precio_mensual' => 9.99,
            'precio_anual' => 99.90,
            'destacado' => false,
            'activo' => true,
            'caracteristicas' => [
                'Hasta 3 usuarios',
                'Almacenamiento básico de 5GB',
                'Soporte por email',
                'Funciones básicas de gestión de tareas'
            ]
        ]);

        // Hacer una petición al endpoint
        $response = $this->getJson('/api/public/precios');

        // Verificar respuesta
        $response->assertStatus(200)
                 ->assertJsonCount(2)
                 ->assertJsonStructure([
                     '*' => [
                         'id',
                         'nombre_plan',
                         'categoria',
                         'precio_mensual',
                         'precio_anual',
                         'caracteristicas'
                     ]
                 ]);
    }
}