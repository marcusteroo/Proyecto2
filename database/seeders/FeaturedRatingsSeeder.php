<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FeaturedRatingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Para cada testimonio, crear usuario si no existe y luego crear o actualizar la reseña
        $testimonials = [
            [
                'name' => 'Elena Martínez',
                'email' => 'elena.martinez@example.com',
                'job_position' => 'Directora de Proyectos',
                'company' => 'TechFlow Solutions',
                'photo_path' => '/images/testimonials/elena.webp',
                'comment' => 'KanFlow ha revolucionado la manera en que gestionamos los proyectos. Las automatizaciones nos permiten ahorrar horas cada semana en tareas repetitivas. La integración con otras herramientas es impecable.',
                'score' => 5,
                'categories' => 'Automatización,Productividad', // Categories como string separado por comas
                'featured' => true,
                'featured_order' => 1
            ],
            [
                'name' => 'Carlos Rodríguez',
                'email' => 'carlos.rodriguez@example.com',
                'job_position' => 'Emprendedor',
                'company' => 'GrowthHackers',
                'photo_path' => '/images/testimonials/carlos.webp',
                'comment' => 'Después de probar decenas de herramientas, por fin encontré KanFlow. La combinación de gestión de tareas y automatizaciones es perfecta para mi startup. El ROI fue inmediato.',
                'score' => 5,
                'categories' => 'Startups,Escalabilidad', // Categories como string separado por comas
                'featured' => true,
                'featured_order' => 2
            ],
            [
                'name' => 'Laura Gómez',
                'email' => 'laura.gomez@example.com',
                'job_position' => 'Desarrolladora Senior',
                'company' => 'InnovaTech',
                'photo_path' => '/images/testimonials/laura.webp',
                'comment' => 'Como desarrolladora, aprecio el enfoque limpio y eficiente de KanFlow. La API es robusta y la posibilidad de personalizar los flujos de trabajo hace que se adapte perfectamente a nuestro stack tecnológico.',
                'score' => 5,
                'categories' => 'Desarrollo,Customización', // Categories como string separado por comas
                'featured' => true,
                'featured_order' => 3
            ],
            [
                'name' => 'Miguel Ángel',
                'email' => 'miguel.angel@example.com',
                'job_position' => 'Consultor de Marketing Digital',
                'company' => null,
                'photo_path' => '/images/testimonials/miguel.webp',
                'comment' => 'KanFlow me permite gestionar todas las campañas de marketing de mis clientes en un solo lugar. Las automatizaciones con WhatsApp y email me han ahorrado horas de trabajo manual.',
                'score' => 5,
                'categories' => 'Marketing,Comunicación', // Categories como string separado por comas
                'featured' => true,
                'featured_order' => 4
            ],
            [
                'name' => 'Sofía Herrera',
                'email' => 'sofia.herrera@example.com',
                'job_position' => 'Gerente de Recursos Humanos',
                'company' => 'Global Services Inc.',
                'photo_path' => '/images/testimonials/sofia.webp',
                'comment' => 'Hemos implementado KanFlow en nuestro departamento de RRHH y la mejora en la eficiencia es notable. El seguimiento de procesos de selección y onboarding nunca había sido tan sencillo y efectivo.',
                'score' => 5,
                'categories' => 'RRHH,Onboarding', // Categories como string separado por comas
                'featured' => true,
                'featured_order' => 5
            ],
            [
                'name' => 'Javier Torres',
                'email' => 'javier.torres@example.com',
                'job_position' => 'Autónomo',
                'company' => null,
                'photo_path' => '/images/testimonials/javier.webp',
                'comment' => 'Como freelancer, necesitaba una herramienta que me ayudara a organizar mis proyectos sin perder tiempo en la gestión. KanFlow es exactamente lo que buscaba. La relación calidad-precio es insuperable.',
                'score' => 5,
                'categories' => 'Freelance,Gestión de tiempo', // Categories como string separado por comas
                'featured' => true,
                'featured_order' => 6
            ]
        ];

        foreach ($testimonials as $testimonial) {
            // Buscar o crear usuario
            $user = User::firstOrCreate(
                ['email' => $testimonial['email']],
                [
                    'name' => $testimonial['name'],
                    'password' => bcrypt('password123'),
                    'email_verified_at' => now()
                ]
            );

            // Buscar si ya existe una reseña para este usuario
            $rating = Rating::where('user_id', $user->id)->first();

            // Si no existe, crear una nueva
            if (!$rating) {
                $rating = new Rating();
                $rating->user_id = $user->id;
            }

            // Actualizar los campos de la reseña
            $rating->score = $testimonial['score'];
            $rating->comment = $testimonial['comment'];
            $rating->job_position = $testimonial['job_position'];
            $rating->company = $testimonial['company'];
            $rating->photo_path = $testimonial['photo_path'];
            $rating->categories = $testimonial['categories']; // Guardar directamente como string separado por comas
            $rating->featured = $testimonial['featured'];
            $rating->featured_order = $testimonial['featured_order'];
            $rating->verified = true;
            
            $rating->save();
        }
    }
}