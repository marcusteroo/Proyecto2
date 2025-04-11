<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;

use Tests\TestCase;
use App\Models\User;

class CheckEmailTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_check_if_email_exists()
    {
        // Crear un usuario con un email específico
        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        // Probar con un email que existe
        $response = $this->postJson('/api/check-email', [
            'email' => 'test@example.com'
        ]);

        $response->assertStatus(200)
                 ->assertJson(['exists' => true]);

        // Probar con un email que no existe
        $response = $this->postJson('/api/check-email', [
            'email' => 'nonexistent@example.com'
        ]);

        $response->assertStatus(200)
                 ->assertJson(['exists' => false]);
    }
}