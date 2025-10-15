<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('actas')) return; // nada que hacer

        Schema::table('actas', function (Blueprint $table) {
            // Asegurar columnas necesarias (crear si faltan)
            if (!Schema::hasColumn('actas', 'razon_social')) $table->string('razon_social')->nullable()->after('placa_vehiculo');
            if (!Schema::hasColumn('actas', 'ruc_dni')) $table->string('ruc_dni')->nullable()->after('razon_social');
            if (!Schema::hasColumn('actas', 'nombre_conductor')) $table->string('nombre_conductor')->nullable()->after('ruc_dni');
            if (!Schema::hasColumn('actas', 'licencia')) $table->string('licencia')->nullable()->after('nombre_conductor');
            if (!Schema::hasColumn('actas', 'lugar_intervencion')) $table->string('lugar_intervencion')->nullable()->after('codigo_ds');
            if (!Schema::hasColumn('actas', 'fecha_intervencion')) $table->date('fecha_intervencion')->nullable()->after('lugar_intervencion');
            if (!Schema::hasColumn('actas', 'hora_intervencion')) $table->time('hora_intervencion')->nullable()->after('fecha_intervencion');
            if (!Schema::hasColumn('actas', 'hora_inicio_registro')) $table->dateTime('hora_inicio_registro')->nullable()->after('hora_intervencion');
            if (!Schema::hasColumn('actas', 'inspector_responsable')) $table->string('inspector_responsable')->nullable()->after('hora_inicio_registro');
            if (!Schema::hasColumn('actas', 'tipo_servicio')) $table->string('tipo_servicio')->nullable()->after('inspector_responsable');
            if (!Schema::hasColumn('actas', 'tipo_agente')) $table->string('tipo_agente')->nullable()->after('tipo_servicio');
            if (!Schema::hasColumn('actas', 'placa')) $table->string('placa')->nullable()->after('tipo_agente');
            if (!Schema::hasColumn('actas', 'codigo_infraccion')) $table->string('codigo_infraccion')->nullable()->after('tipo_agente');
            if (!Schema::hasColumn('actas', 'descripcion_hechos')) $table->text('descripcion_hechos')->nullable()->after('codigo_infraccion');
            if (!Schema::hasColumn('actas', 'monto_multa')) $table->decimal('monto_multa', 10, 2)->nullable()->after('descripcion_hechos');
            if (!Schema::hasColumn('actas', 'estado')) $table->tinyInteger('estado')->default(0)->after('monto_multa');
        });

        // Soltar llaves foráneas si existen antes de eliminar columnas
        try { DB::statement("ALTER TABLE `actas` DROP FOREIGN KEY `actas_vehiculo_id_foreign`"); } catch (\Throwable $e) { /* ignore */ }
        try { DB::statement("ALTER TABLE `actas` DROP FOREIGN KEY `actas_conductor_id_foreign`"); } catch (\Throwable $e) { /* ignore */ }
        try { DB::statement("ALTER TABLE `actas` DROP FOREIGN KEY `actas_infraccion_id_foreign`"); } catch (\Throwable $e) { /* ignore */ }

        // Remover columnas no necesarias del flujo actual (si existen)
        Schema::table('actas', function (Blueprint $table) {
            $drops = [
                'vehiculo_id', 'conductor_id', 'infraccion_id', 'inspector_id',
                'ubicacion', 'origen', 'destino', 'numero_personas',
                'medios_probatorios', 'calificacion', 'medida_administrativa', 'sancion',
                'observaciones_intervenido', 'observaciones_inspector', 'observaciones'
            ];
            foreach ($drops as $col) {
                if (Schema::hasColumn('actas', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('actas')) return;
        Schema::table('actas', function (Blueprint $table) {
            // No recreamos columnas eliminadas en down para evitar inconsistencias.
            // Solo dejamos el esqueleto mínimo.
            // Opcional: podría recrear, pero no es necesario para este ajuste.
        });
    }
};
