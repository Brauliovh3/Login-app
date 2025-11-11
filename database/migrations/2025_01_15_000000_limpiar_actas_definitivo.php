<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('actas', function (Blueprint $table) {
            // Eliminar columnas duplicadas si existen
            $columns = Schema::getColumnListing('actas');
            
            if (in_array('nombre_conductor', $columns)) {
                $table->dropColumn('nombre_conductor');
            }
            if (in_array('conductor_nombre', $columns)) {
                $table->dropColumn('conductor_nombre');
            }
            if (in_array('apellidos', $columns)) {
                $table->dropColumn('apellidos');
            }
            if (in_array('nombres', $columns)) {
                $table->dropColumn('nombres');
            }
        });
        
        // Asegurar columnas correctas
        Schema::table('actas', function (Blueprint $table) {
            $columns = Schema::getColumnListing('actas');
            
            if (!in_array('apellidos_conductor', $columns)) {
                $table->string('apellidos_conductor', 100)->nullable();
            }
            if (!in_array('nombres_conductor', $columns)) {
                $table->string('nombres_conductor', 100)->nullable();
            }
            if (!in_array('motivo_anulacion', $columns)) {
                $table->text('motivo_anulacion')->nullable();
            }
            if (!in_array('fecha_anulacion', $columns)) {
                $table->timestamp('fecha_anulacion')->nullable();
            }
            if (!in_array('anulado_por', $columns)) {
                $table->unsignedBigInteger('anulado_por')->nullable();
            }
        });
        
        // Convertir estado a TINYINT si es ENUM
        DB::statement('ALTER TABLE actas MODIFY COLUMN estado TINYINT DEFAULT 0');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No revertir cambios de limpieza
    }
};
