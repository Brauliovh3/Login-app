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
        if (!Schema::hasTable('actas')) {
            // Nada que hacer
            return;
        }

        // 1) Crear una copia de seguridad entera de la tabla actas
        try {
            if (!Schema::hasTable('actas_backup')) {
                // Crear tabla backup con la misma estructura
                DB::statement('CREATE TABLE `actas_backup` LIKE `actas`');
            }
            // Copiar datos actuales
            DB::statement('INSERT INTO `actas_backup` SELECT * FROM `actas`');
        } catch (\Exception $e) {
            logger()->error('No se pudo crear/copiar actas_backup: ' . $e->getMessage());
            // No abortamos; intentaremos seguir con precaución
        }

        // 2) Eliminar columnas pesadas que suelen contener textos largos y no son necesarias
        //    (ajusta la lista si quieres conservar alguna)
        $colsToDrop = [
            'descripcion_hechos',
            'medios_probatorios',
            'observaciones_intervenido',
            'observaciones_inspector',
            'observaciones'
        ];

        foreach ($colsToDrop as $col) {
            if (Schema::hasColumn('actas', $col)) {
                try {
                    Schema::table('actas', function (Blueprint $table) use ($col) {
                        $table->dropColumn($col);
                    });
                } catch (\Exception $e) {
                    // En algunos entornos dropColumn falla si falta doctrine/dbal
                    try {
                        DB::statement("ALTER TABLE `actas` DROP COLUMN `{$col}`");
                    } catch (\Exception $ex) {
                        logger()->warning('No se pudo eliminar columna ' . $col . ': ' . $ex->getMessage());
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('actas')) {
            return;
        }

        // Volver a crear las columnas vacías (no restaura datos; los datos están en actas_backup)
        Schema::table('actas', function (Blueprint $table) {
            if (!Schema::hasColumn('actas', 'descripcion_hechos')) {
                $table->text('descripcion_hechos')->nullable();
            }
            if (!Schema::hasColumn('actas', 'medios_probatorios')) {
                $table->text('medios_probatorios')->nullable();
            }
            if (!Schema::hasColumn('actas', 'observaciones_intervenido')) {
                $table->text('observaciones_intervenido')->nullable();
            }
            if (!Schema::hasColumn('actas', 'observaciones_inspector')) {
                $table->text('observaciones_inspector')->nullable();
            }
            if (!Schema::hasColumn('actas', 'observaciones')) {
                $table->text('observaciones')->nullable();
            }
        });
    }
};
