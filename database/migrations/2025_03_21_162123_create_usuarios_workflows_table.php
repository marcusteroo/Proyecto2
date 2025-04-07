<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios_workflows', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('workflow_id')->constrained('workflows', 'id_workflow')->onDelete('cascade');
            $table->enum('rol', ['propietario', 'editor', 'espectador'])->default('espectador');
            $table->timestamp('fecha_compartido')->useCurrent();
            // Define la clave primaria compuesta
            $table->primary(['user_id', 'workflow_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuarios_workflows');
    }
};