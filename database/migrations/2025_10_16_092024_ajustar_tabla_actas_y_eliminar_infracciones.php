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
        // 1. Eliminar tablas de infracciones (no las necesitamos)
        Schema::dropIfExists('detalle_infraccion');
        Schema::dropIfExists('infracciones');
        
        // 2. Modificar tabla actas
        Schema::table('actas', function (Blueprint $table) {
            // Agregar columna codigo_infraccion si no existe
            if (!Schema::hasColumn('actas', 'codigo_infraccion')) {
                $table->string('codigo_infraccion', 50)->nullable()->after('lugar_intervencion');
            }
            
            // Agregar columna descripcion_infraccion si no existe
            if (!Schema::hasColumn('actas', 'descripcion_infraccion')) {
                $table->text('descripcion_infraccion')->nullable()->after('codigo_infraccion');
            }
            
            // Eliminar columnas innecesarias si existen
            if (Schema::hasColumn('actas', 'descripcion_hechos')) {
                $table->dropColumn('descripcion_hechos');
            }
            if (Schema::hasColumn('actas', 'observaciones_adicionales')) {
                $table->dropColumn('observaciones_adicionales');
            }
            if (Schema::hasColumn('actas', 'tipo_infraccion')) {
                $table->dropColumn('tipo_infraccion');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No revertir - esta migración es irreversible
    }
};
