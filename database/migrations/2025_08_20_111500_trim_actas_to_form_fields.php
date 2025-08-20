<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Backup completo antes de cualquier eliminación
        try {
            if (!Schema::hasTable('actas_archive')) {
                DB::statement('CREATE TABLE actas_archive LIKE actas');
                DB::statement('INSERT INTO actas_archive SELECT * FROM actas');
            }
        } catch (\Throwable $e) {
            logger()->warning('No se pudo crear actas_archive: ' . $e->getMessage());
        }

        // Columnas que queremos mantener (según el formulario)
        $keep = [
            'id','numero_acta','inspector_responsable','fecha_intervencion','hora_intervencion','lugar_intervencion',
            'tipo_servicio','tipo_agente','placa','placa_vehiculo','razon_social','ruc_dni','nombre_conductor',
            'licencia_conductor','clase_licencia','descripcion_hechos','codigo_infraccion','gravedad','monto_multa',
            'estado','has_evidencias','user_id','created_at','updated_at'
        ];

        // Obtener columnas actuales de actas
        try {
            $cols = DB::select("SELECT COLUMN_NAME as col FROM information_schema.columns WHERE table_schema = ? AND table_name = 'actas'", [env('DB_DATABASE')]);
            $cols = array_map(function($c){ return $c->col; }, $cols);
        } catch (\Throwable $e) {
            logger()->warning('No se pudo leer esquema de actas: ' . $e->getMessage());
            $cols = [];
        }

        // Calcular columnas a eliminar
        $toDrop = array_diff($cols, $keep);

        if (!empty($toDrop)) {
            // Primero eliminar claves foráneas que puedan bloquear el drop
            foreach ($toDrop as $col) {
                try {
                    $fk = DB::selectOne("SELECT CONSTRAINT_NAME as cname FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'actas' AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL", [env('DB_DATABASE'), $col]);
                    if ($fk && isset($fk->cname)) {
                        $cname = $fk->cname;
                        try {
                            DB::statement("ALTER TABLE `actas` DROP FOREIGN KEY `" . $cname . "`");
                        } catch (\Throwable $e) {
                            logger()->warning("No se pudo eliminar FK {$cname} para columna {$col}: " . $e->getMessage());
                        }
                        // intentar eliminar índice si existe con el mismo nombre
                        try {
                            DB::statement("ALTER TABLE `actas` DROP INDEX `" . $cname . "`");
                        } catch (\Throwable $e) {
                            // ignore
                        }
                    }
                } catch (\Throwable $e) {
                    logger()->warning('Error buscando FK para columna ' . $col . ': ' . $e->getMessage());
                }
            }

            // Ahora sí intentar eliminar columnas
            foreach ($toDrop as $col) {
                try {
                    if (Schema::hasColumn('actas', $col)) {
                        Schema::table('actas', function (Blueprint $table) use ($col) {
                            $table->dropColumn($col);
                        });
                    }
                } catch (\Throwable $e) {
                    logger()->warning("No se pudo eliminar columna {$col}: " . $e->getMessage());
                }
            }
        }
    }

    public function down(): void
    {
        // No intentamos restaurar automáticamente columnas eliminadas (usar actas_archive para recuperación manual)
    }
};
