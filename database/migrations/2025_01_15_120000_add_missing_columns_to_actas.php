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
            // Agregar columna fiscalizador_id si no existe
            if (!Schema::hasColumn('actas', 'fiscalizador_id')) {
                $table->unsignedBigInteger('fiscalizador_id')->nullable()->after('user_id');
                $table->index('fiscalizador_id');
            }
            
            // Agregar columnas para anulación si no existen
            if (!Schema::hasColumn('actas', 'motivo_anulacion')) {
                $table->text('motivo_anulacion')->nullable()->after('observaciones_inspector');
            }
            
            if (!Schema::hasColumn('actas', 'fecha_anulacion')) {
                $table->timestamp('fecha_anulacion')->nullable()->after('motivo_anulacion');
            }
            
            if (!Schema::hasColumn('actas', 'anulado_por')) {
                $table->unsignedBigInteger('anulado_por')->nullable()->after('fecha_anulacion');
                $table->index('anulado_por');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actas', function (Blueprint $table) {
            $table->dropColumn(['fiscalizador_id', 'motivo_anulacion', 'fecha_anulacion', 'anulado_por']);
        });
    }
};