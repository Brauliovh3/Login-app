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
            $table->timestamp('hora_inicio_registro')->nullable()->after('hora_infraccion')->comment('Hora exacta de inicio del registro');
            $table->timestamp('hora_fin_registro')->nullable()->after('hora_inicio_registro')->comment('Hora exacta de finalización del registro');
            $table->timestamp('ultima_actualizacion')->nullable()->after('hora_fin_registro')->comment('Última vez que se guardó progreso');
            $table->integer('tiempo_total_registro')->nullable()->after('ultima_actualizacion')->comment('Tiempo total de registro en minutos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actas', function (Blueprint $table) {
            $table->dropColumn(['hora_inicio_registro', 'hora_fin_registro', 'ultima_actualizacion', 'tiempo_total_registro']);
        });
    }
};
