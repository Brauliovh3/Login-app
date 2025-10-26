<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agregar foreign keys importantes después de crear todas las tablas
     * Esta migración asegura que no haya conflictos de orden en las migraciones
     */
    public function up(): void
    {
        // Agregar foreign keys en el orden correcto para evitar conflictos
        
        // 1. Foreign key de actas -> vehiculos (si la columna existe)
        if (Schema::hasColumn('actas', 'vehiculo_id') && Schema::hasTable('vehiculos')) {
            Schema::table('actas', function (Blueprint $table) {
                try {
                    $table->foreign('vehiculo_id')->references('id')->on('vehiculos')->onDelete('set null');
                } catch (\Exception $e) {
                    // Si ya existe, continuar
                }
            });
        }

        // 2. Foreign key de actas -> empresas (si la columna existe)
        if (Schema::hasColumn('actas', 'empresa_id') && Schema::hasTable('empresas')) {
            Schema::table('actas', function (Blueprint $table) {
                try {
                    $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('set null');
                } catch (\Exception $e) {
                    // Si ya existe, continuar
                }
            });
        }

        // 3. Foreign key de actas -> inspectores (si la columna existe)
        if (Schema::hasColumn('actas', 'inspector_id') && Schema::hasTable('inspectores')) {
            Schema::table('actas', function (Blueprint $table) {
                try {
                    $table->foreign('inspector_id')->references('id')->on('inspectores')->onDelete('set null');
                } catch (\Exception $e) {
                    // Si ya existe, continuar
                }
            });
        }

        // 4. Foreign key de actas -> infracciones (si la columna existe)
        if (Schema::hasColumn('actas', 'infraccion_id') && Schema::hasTable('infracciones')) {
            Schema::table('actas', function (Blueprint $table) {
                try {
                    $table->foreign('infraccion_id')->references('id')->on('infracciones')->onDelete('set null');
                } catch (\Exception $e) {
                    // Si ya existe, continuar
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actas', function (Blueprint $table) {
            // Eliminar foreign keys si existen
            try { $table->dropForeign(['vehiculo_id']); } catch (\Exception $e) {}
            try { $table->dropForeign(['empresa_id']); } catch (\Exception $e) {}
            try { $table->dropForeign(['inspector_id']); } catch (\Exception $e) {}
            try { $table->dropForeign(['infraccion_id']); } catch (\Exception $e) {}
        });
    }
};