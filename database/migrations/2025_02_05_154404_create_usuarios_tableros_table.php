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
        Schema::create('usuarios_tableros', function (Blueprint $table) {
            $table->foreignId('id_usuario') // Relación con la tabla 'users'
                  ->constrained('users') 
                  ->onDelete('cascade'); 
            $table->foreignId('id_tablero') // Relación con la tabla 'tableros'
                  ->constrained('tableros', 'id_tablero')
                  ->onDelete('cascade');
            $table->dateTime('fecha_incorporacion')->default(DB::raw('CURRENT_TIMESTAMP')); // DATETIME con valor por defecto
            $table->string('rol', 50)->nullable(); // VARCHAR(50), puede ser NULL

            // Definir la clave primaria compuesta por 'id_usuario' e 'id_tablero'
            $table->primary(['id_usuario', 'id_tablero']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios_tableros');
    }
};
