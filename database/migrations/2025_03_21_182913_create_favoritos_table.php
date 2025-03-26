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
            $table->morphs('favorable'); 
            $table->timestamp('fecha_marcado')->useCurrent();
            $table->timestamps();
            
           
            $table->unique(['user_id', 'favorable_id', 'favorable_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('favoritos');
    }
};