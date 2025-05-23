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
        Schema::create('precios_home', function (Blueprint $table) {
            $table->id();
            $table->decimal('precio_mensual', 10, 2);
            $table->decimal('precio_anual', 10, 2);
            $table->enum('categoria', ['basico', 'premium', 'business']);
            $table->string('nombre_plan', 100);
            $table->string('descripcion', 255)->nullable();
            $table->boolean('destacado')->default(false);
            $table->boolean('activo')->default(true);
            $table->json('caracteristicas')->nullable(); 
            $table->timestamps();
            
            
            $table->unique(['categoria', 'activo'], 'categoria_activo_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('precios_home');
    }
};