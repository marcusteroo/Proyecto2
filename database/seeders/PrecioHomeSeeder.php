<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PrecioHome;

class PrecioHomeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Plan Básico
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

        // Plan Premium
        PrecioHome::create([
            'categoria' => 'premium',
            'nombre_plan' => 'Plan Premium',
            'descripcion' => 'Ideal para equipos en crecimiento con necesidades específicas',
            'precio_mensual' => 29.99,
            'precio_anual' => 299.90,
            'destacado' => true,
            'activo' => true,
            'caracteristicas' => [
                'Hasta 10 usuarios',
                'Almacenamiento de 50GB',
                'Soporte prioritario',
                'Funciones avanzadas de gestión de tareas',
                'Integración con herramientas de terceros',
                'Análisis básicos de productividad'
            ]
        ]);

        // Plan Business
        PrecioHome::create([
            'categoria' => 'business',
            'nombre_plan' => 'Plan Business',
            'descripcion' => 'Solución completa para empresas con equipos grandes',
            'precio_mensual' => 79.99,
            'precio_anual' => 799.90,
            'destacado' => false,
            'activo' => true,
            'caracteristicas' => [
                'Usuarios ilimitados',
                'Almacenamiento de 500GB',
                'Soporte 24/7',
                'Todas las funciones premium',
                'API avanzada',
                'Análisis detallados de productividad',
                'Administración avanzada de usuarios',
                'Personalización completa de la plataforma'
            ]
        ]);
    }
}