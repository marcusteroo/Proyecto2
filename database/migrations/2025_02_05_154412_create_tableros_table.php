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
        Schema::create('tableros', function (Blueprint $table) {
            $table->id('id_tablero'); // Auto-incremental, equivale a INT AUTO_INCREMENT
            $table->string('nombre', 100); // VARCHAR(100) NOT NULL
            $table->text('descripcion')->nullable(); // TEXT, puede ser NULL
            $table->string('estado', 100); // VARCHAR(100) NOT NULL
            $table->foreignId('id_creador') // BIGINT NOT NULL
                  ->constrained('users') // Relación con la tabla 'users'
                  ->onDelete('cascade'); // ON DELETE CASCADE
            $table->timestamps(); // created_at y updated_at con los valores por defecto
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tableros');
    }
};
