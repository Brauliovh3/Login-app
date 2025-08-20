<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Añadir columna inspector_responsable si no existe
        if (Schema::hasTable('actas') && !Schema::hasColumn('actas', 'inspector_responsable')) {
            Schema::table('actas', function (Blueprint $table) {
                $table->string('inspector_responsable')->nullable()->after('hora_intervencion');
            });
        }

        // Crear una tabla minimal que contenga solo los campos usados por el formulario
        if (!Schema::hasTable('actas_minimal')) {
            Schema::create('actas_minimal', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('numero_acta')->nullable()->unique();
                $table->string('inspector_responsable')->nullable();
                $table->date('fecha_intervencion')->nullable();
                $table->time('hora_intervencion')->nullable();
                $table->string('lugar_intervencion')->nullable();
                $table->string('tipo_servicio')->nullable();
                $table->string('tipo_agente')->nullable();
                $table->string('placa')->nullable();
                $table->string('placa_vehiculo')->nullable();
                $table->string('razon_social')->nullable();
                $table->string('ruc_dni')->nullable();
                $table->string('nombre_conductor')->nullable();
                $table->string('licencia_conductor')->nullable();
                $table->string('clase_licencia')->nullable();
                $table->text('descripcion_hechos')->nullable();
                $table->string('codigo_infraccion')->nullable();
                $table->string('gravedad')->nullable();
                $table->decimal('monto_multa', 10, 2)->nullable();
                $table->tinyInteger('estado')->default(0);
                $table->tinyInteger('has_evidencias')->default(0);
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
            });
        }

        // Copiar datos desde actas a actas_minimal (si existe actas)
        try {
            $db = env('DB_DATABASE');
            if (Schema::hasTable('actas')) {
                // Insertar solo si actas_minimal está vacía
                $count = DB::selectOne("SELECT COUNT(*) as c FROM actas_minimal");
                if ($count && $count->c == 0) {
                    DB::statement(
                        "INSERT INTO actas_minimal (id, numero_acta, inspector_responsable, fecha_intervencion, hora_intervencion, lugar_intervencion, tipo_servicio, tipo_agente, placa, placa_vehiculo, razon_social, ruc_dni, nombre_conductor, licencia_conductor, clase_licencia, descripcion_hechos, codigo_infraccion, gravedad, monto_multa, estado, has_evidencias, user_id, created_at, updated_at)
                         SELECT a.id, a.numero_acta, COALESCE(a.inspector_responsable, i.nombre) as inspector_responsable, a.fecha_intervencion, a.hora_intervencion, COALESCE(a.lugar_intervencion, a.ubicacion) as lugar_intervencion, a.tipo_servicio, a.tipo_agente, COALESCE(a.placa, a.placa_vehiculo) as placa, a.placa_vehiculo, a.razon_social, a.ruc_dni, a.nombre_conductor, a.licencia_conductor, a.clase_licencia, a.descripcion_hechos, a.codigo_infraccion, a.gravedad, a.monto_multa, a.estado, a.has_evidencias, a.user_id, a.created_at, a.updated_at
                         FROM actas a LEFT JOIN inspectores i ON a.inspector_id = i.id"
                    );
                }
            }
        } catch (\Throwable $e) {
            logger()->warning('No se pudo copiar datos a actas_minimal: ' . $e->getMessage());
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('actas_minimal')) {
            Schema::dropIfExists('actas_minimal');
        }

        if (Schema::hasTable('actas') && Schema::hasColumn('actas', 'inspector_responsable')) {
            Schema::table('actas', function (Blueprint $table) {
                $table->dropColumn('inspector_responsable');
            });
        }
    }
};
