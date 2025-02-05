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
        Schema::create('workflows_tareas', function (Blueprint $table) {
            $table->foreignId('id_workflow') // Relación con la tabla 'workflows'
                  ->constrained('workflows', 'id_workflow') // Relación con la columna 'id_workflow'
                  ->onDelete('cascade'); // ON DELETE CASCADE
            $table->foreignId('id_tarea') // Relación con la tabla 'tareas'
                  ->constrained('tareas', 'id_tarea') // Relación con la columna 'id_tarea'
                  ->onDelete('cascade'); // ON DELETE CASCADE
            $table->integer('prioridad')->nullable(); // INT, puede ser NULL

            // Definir una clave primaria compuesta por id_workflow y id_tarea
            $table->primary(['id_workflow', 'id_tarea']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflows_tareas');
    }
};
