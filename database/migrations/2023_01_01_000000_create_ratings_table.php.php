<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingsTable extends Migration
{
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ID del usuario que valora
            $table->tinyInteger('score');          // Puntuación de 1 a 5
            $table->text('comment')->nullable();   // Comentario opcional
            $table->timestamps();

            // Clave foránea
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade'); // Si se borra el usuario, se borran sus valoraciones
        });
    }

    public function down()
    {
        Schema::dropIfExists('ratings');
    }
}
