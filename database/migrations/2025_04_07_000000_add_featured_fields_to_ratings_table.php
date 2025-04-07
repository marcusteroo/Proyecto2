<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->boolean('featured')->default(false); // Indica si la reseña se mostrará en home
            $table->integer('featured_order')->nullable(); // Orden de visualización en home
            $table->string('photo_path')->nullable(); // Ruta a la imagen del usuario (si no se usa su avatar)
            $table->json('tags')->nullable(); // Tags para categorizar la reseña
            $table->boolean('verified')->default(true); // Si el usuario está verificado
        });
    }

    public function down()
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropColumn(['featured', 'featured_order', 'photo_path', 'tags', 'verified']);
        });
    }
};