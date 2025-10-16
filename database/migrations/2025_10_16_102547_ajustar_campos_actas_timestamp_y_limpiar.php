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
        Schema::table('actas', function (Blueprint $table) {
            // Agregar hora_inicio_registro si no existe
            if (!Schema::hasColumn('actas', 'hora_inicio_registro')) {
                $table->time('hora_inicio_registro')->nullable()->after('hora_intervencion');
            }
            
            // Asegurar que codigo_infraccion exista
            if (!Schema::hasColumn('actas', 'codigo_infraccion')) {
                $table->string('codigo_infraccion', 50)->nullable()->after('lugar_intervencion');
            }
            
            // Asegurar que descripcion_infraccion exista
            if (!Schema::hasColumn('actas', 'descripcion_infraccion')) {
                $table->text('descripcion_infraccion')->nullable()->after('codigo_infraccion');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actas', function (Blueprint $table) {
            if (Schema::hasColumn('actas', 'hora_inicio_registro')) {
                $table->dropColumn('hora_inicio_registro');
            }
        });
    }
};
