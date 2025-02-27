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
        Schema::table('workflows', function (Blueprint $table) {
            $table->string('trigger_type')->nullable()->after('descripcion');
            $table->json('trigger_params')->nullable()->after('trigger_type');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('trigger_params');
        });

        Schema::table('workflow_actions', function (Blueprint $table) {
            $table->string('action_type')->after('id_workflow')->nullable();
            $table->integer('order_index')->default(0)->after('description');
            $table->json('config')->nullable()->after('y_position');
        });

        // Tabla para ejecutar workflows
        Schema::create('workflow_executions', function (Blueprint $table) {
            $table->id('id_execution');
            $table->foreignId('id_workflow')->constrained('workflows', 'id_workflow')->onDelete('cascade');
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->default('pending');
            $table->text('result')->nullable();
            $table->json('trigger_data')->nullable();
            $table->timestamps();
        });

        // Tabla para logs de ejecución de acciones
        Schema::create('workflow_action_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_execution')->constrained('workflow_executions', 'id_execution')->onDelete('cascade');
            $table->foreignId('id_action')->constrained('workflow_actions', 'id_action')->onDelete('cascade');
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->default('pending');
            $table->text('result')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_action_logs');
        Schema::dropIfExists('workflow_executions');

        Schema::table('workflow_actions', function (Blueprint $table) {
            $table->dropColumn(['action_type', 'order_index', 'config']);
        });

        Schema::table('workflows', function (Blueprint $table) {
            $table->dropColumn(['trigger_type', 'trigger_params', 'status']);
        });
    }
};