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
        Schema::create('tableros_tareas', function (Blueprint $table) {
            $table->foreignId('id_tablero') // Relación con la tabla 'tableros'
                  ->constrained('tableros', 'id_tablero') // Especifica 'id_tablero' como columna de referencia
                  ->onDelete('cascade'); 
            $table->foreignId('id_tarea') // Relación con la tabla 'tareas'
                  ->constrained('tareas', 'id_tarea')
                  ->onDelete('cascade');
            $table->dateTime('fecha_incorporacion')->default(DB::raw('CURRENT_TIMESTAMP')); // DATETIME con valor por defecto
            $table->string('rol', 50)->nullable(); // VARCHAR(50), puede ser NULL

            // Definir la clave primaria compuesta por 'id_tablero' e 'id_tarea'
            $table->primary(['id_tablero', 'id_tarea']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tableros_tareas');
    }
};
