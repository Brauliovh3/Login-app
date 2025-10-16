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
            // Agregar campo estado si no existe
            if (!Schema::hasColumn('actas', 'estado')) {
                $table->enum('estado', ['Pendiente', 'Aprobado', 'Anulado'])->default('Pendiente')->after('codigo_infraccion');
            }
            
            // Agregar campo motivo_anulacion si no existe
            if (!Schema::hasColumn('actas', 'motivo_anulacion')) {
                $table->text('motivo_anulacion')->nullable()->after('estado');
            }
            
            // Agregar campo fecha_anulacion si no existe
            if (!Schema::hasColumn('actas', 'fecha_anulacion')) {
                $table->datetime('fecha_anulacion')->nullable()->after('motivo_anulacion');
            }
            
            // Agregar campo anulado_por si no existe
            if (!Schema::hasColumn('actas', 'anulado_por')) {
                $table->unsignedBigInteger('anulado_por')->nullable()->after('fecha_anulacion');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actas', function (Blueprint $table) {
            if (Schema::hasColumn('actas', 'anulado_por')) {
                $table->dropColumn('anulado_por');
            }
            if (Schema::hasColumn('actas', 'fecha_anulacion')) {
                $table->dropColumn('fecha_anulacion');
            }
            if (Schema::hasColumn('actas', 'motivo_anulacion')) {
                $table->dropColumn('motivo_anulacion');
            }
            if (Schema::hasColumn('actas', 'estado')) {
                $table->dropColumn('estado');
            }
        });
    }
};
