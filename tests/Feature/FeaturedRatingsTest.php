<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\Rating;
use App\Models\User;

class FeaturedRatingsTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_get_featured_ratings_for_home()
    {
        // Limpiar reseñas destacadas existentes
        Rating::where('featured', true)->update(['featured' => false]);

        // Crear usuario para las reseñas
        $user = User::factory()->create([
            'name' => 'Usuario Test',
            'email' => 'test.rating@example.com'
        ]);

        // Crear reseñas destacadas
        Rating::create([
            'user_id' => $user->id,
            'score' => 5,
            'comment' => 'Esta aplicación es excelente para la gestión de proyectos',
            'categories' => 'Desarrollo,Customización',
            'job_position' => 'Desarrollador Senior',
            'company' => 'TestCompany',
            'photo_path' => '/images/testimonials/test.webp',
            'featured' => true,
            'featured_order' => 1,
            'verified' => true
        ]);

        Rating::create([
            'user_id' => $user->id,
            'score' => 4,
            'comment' => 'Muy buena herramienta para equipos',
            'categories' => 'Marketing,Startup',
            'job_position' => 'Marketing Manager',
            'company' => 'StartupX',
            'photo_path' => null, // Para probar que usa el avatar del usuario
            'featured' => true,
            'featured_order' => 2,
            'verified' => true
        ]);

        // Hacer la petición al endpoint
        $response = $this->getJson('/api/public/featured-ratings');

        // Verificar la respuesta
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ])
                 ->assertJsonCount(2, 'data')
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => [
                             'nombre',
                             'position',
                             'company',
                             'foto',
                             'texto',
                             'rating',
                             'verified',
                             'featured',
                             'tags'
                         ]
                     ]
                 ]);
                 
        // Verificar el orden correcto basado en featured_order
        $responseData = $response->json('data');
        $this->assertEquals('Esta aplicación es excelente para la gestión de proyectos', $responseData[0]['texto']);
        $this->assertEquals('Muy buena herramienta para equipos', $responseData[1]['texto']);
    }
}