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
        Schema::table('tableros', function (Blueprint $table) {
            $table->string('color_fondo')->nullable()->after('id_creador');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tableros', function (Blueprint $table) {
            $table->dropColumn('color_fondo');
        });
    }
};