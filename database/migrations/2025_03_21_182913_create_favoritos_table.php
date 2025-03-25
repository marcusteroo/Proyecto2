<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('favoritos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->morphs('favorable'); // Crea columnas favorable_id y favorable_type
            $table->timestamp('fecha_marcado')->useCurrent();
            $table->timestamps();
            
            // Asegurar que un usuario no pueda marcar el mismo elemento como favorito múltiples veces
            $table->unique(['user_id', 'favorable_id', 'favorable_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('favoritos');
    }
};