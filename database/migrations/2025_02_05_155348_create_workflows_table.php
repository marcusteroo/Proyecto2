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
        Schema::create('workflows', function (Blueprint $table) {
            $table->id('id_workflow'); // Auto-incremental, equivale a INT AUTO_INCREMENT
            $table->string('nombre', 100); // VARCHAR(100) NOT NULL
            $table->text('descripcion')->nullable(); // TEXT, puede ser NULL
            $table->unsignedBigInteger('id_creador'); // Añadir la columna para el usuario creador
            $table->foreign('id_creador')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade'); // Si se elimina un usuario, se eliminarán sus workflows
            $table->timestamps(); // created_at y updated_at con los valores por defecto
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflows');
    }
};
