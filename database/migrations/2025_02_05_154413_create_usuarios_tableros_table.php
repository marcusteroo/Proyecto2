<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usuarios_tableros', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tablero_id')->constrained('tableros', 'id_tablero')->onDelete('cascade');
            $table->timestamp('fecha_compartido')->useCurrent();
            // Clave primaria compuesta
            $table->primary(['user_id', 'tablero_id']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuarios_tableros');
    }
};
