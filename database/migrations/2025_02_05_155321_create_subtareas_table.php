<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subtareas', function (Blueprint $table) {
            $table->id('id_subtarea'); // Auto-incremental, equivale a INT AUTO_INCREMENT
            $table->string('titulo', 100); // VARCHAR(100) NOT NULL
            $table->string('estado', 50)->default('Pendiente'); // VARCHAR(50) con valor por defecto 'Pendiente'
            $table->foreignId('id_tarea') // INT NOT NULL
                  ->constrained('tareas', 'id_tarea') // Relación con la tabla 'tareas', usando 'id_tarea' como clave foránea
                  ->onDelete('cascade'); // ON DELETE CASCADE
            $table->dateTime('fecha_creacion')->default(DB::raw('CURRENT_TIMESTAMP')); // DATETIME con valor por defecto
            $table->dateTime('fecha_vencimiento')->nullable(); // DATETIME, puede ser NULL
            $table->timestamps(); // created_at y updated_at con los valores por defecto
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subtareas');
    }
};
