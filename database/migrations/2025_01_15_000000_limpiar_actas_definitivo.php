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
        // Verificar si la tabla existe
        if (!Schema::hasTable('actas')) {
            return;
        }
        
        $columns = Schema::getColumnListing('actas');
        
        // Eliminar columnas duplicadas una por una
        if (in_array('nombre_conductor', $columns) && in_array('nombres_conductor', $columns)) {
            Schema::table('actas', function (Blueprint $table) {
                $table->dropColumn('nombre_conductor');
            });
        }
        
        if (in_array('conductor_nombre', $columns)) {
            Schema::table('actas', function (Blueprint $table) {
                $table->dropColumn('conductor_nombre');
            });
        }
        
        if (in_array('apellidos', $columns) && in_array('apellidos_conductor', $columns)) {
            Schema::table('actas', function (Blueprint $table) {
                $table->dropColumn('apellidos');
            });
        }
        
        if (in_array('nombres', $columns) && in_array('nombres_conductor', $columns)) {
            Schema::table('actas', function (Blueprint $table) {
                $table->dropColumn('nombres');
            });
        }
        
        // Refrescar lista de columnas
        $columns = Schema::getColumnListing('actas');
        
        // Agregar columnas faltantes
        Schema::table('actas', function (Blueprint $table) use ($columns) {
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
        
        // Convertir estado a TINYINT si es necesario
        try {
            DB::statement('ALTER TABLE actas MODIFY COLUMN estado TINYINT DEFAULT 0');
        } catch (\Exception $e) {
            // Si falla, la columna ya está en el formato correcto
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No revertir cambios de limpieza
    }
};
