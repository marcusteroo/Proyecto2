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
        Schema::create('tareas_subtareas', function (Blueprint $table) {
            $table->foreignId('id_tarea') // Relación con la tabla 'users'
                  ->constrained('users') 
                  ->onDelete('cascade'); 
            $table->foreignId('id_subtarea') // Relación con la tabla 'subtareas'
                  ->constrained('subtareas', 'id_subtarea')
                  ->onDelete('cascade');
            $table->dateTime('fecha_incorporacion')->default(DB::raw('CURRENT_TIMESTAMP')); // DATETIME con valor por defecto
            $table->string('rol', 50)->nullable(); // VARCHAR(50), puede ser NULL

            // Definir la clave primaria compuesta por 'id_tarea' e 'id_subtarea'
            $table->primary(['id_tarea', 'id_subtarea']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tareas_subtareas');
    }
};
