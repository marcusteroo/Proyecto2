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
        Schema::create('tareas', function (Blueprint $table) {
            $table->id('id_tarea'); // Auto-incremental, equivale a INT AUTO_INCREMENT
            $table->string('titulo', 100); // VARCHAR(100) NOT NULL
            $table->text('descripcion')->nullable(); // TEXT, puede ser NULL
            $table->string('estado', 50)->default('Pendiente'); // VARCHAR(50) con valor por defecto 'Pendiente'
            $table->foreignId('id_tablero') // INT NOT NULL
                  ->constrained('tableros', 'id_tablero') // Relación con la tabla 'tableros', usando 'id_tablero' como clave foránea
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
        Schema::dropIfExists('tareas');
    }
};
