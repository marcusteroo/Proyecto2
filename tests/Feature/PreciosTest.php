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
        PrecioHome::where('categoria', 'basico')->delete();
        PrecioHome::where('categoria', 'premium')->delete();
        PrecioHome::where('categoria', 'business')->delete();

        PrecioHome::create([
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

        PrecioHome::create([
            'categoria' => 'premium', 
            'nombre_plan' => 'Plan Premium',
            'descripcion' => 'Ideal para equipos en crecimiento',
            'precio_mensual' => 29.99,
            'precio_anual' => 299.90,
            'destacado' => false,
            'activo' => true,
            'caracteristicas' => [
                'Hasta 10 usuarios',
                'Almacenamiento de 50GB',
                'Soporte prioritario',
                'Funciones avanzadas'
            ]
        ]);

        $response = $this->getJson('/api/public/precios');

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