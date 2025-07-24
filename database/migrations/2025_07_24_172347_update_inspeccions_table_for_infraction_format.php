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
        Schema::table('inspeccions', function (Blueprint $table) {
            // Eliminar columnas del formato anterior
            $table->dropColumn([
                'tipo_establecimiento',
                'nombre_establecimiento', 
                'ruc_dni_establecimiento',
                'departamento',
                'provincia',
                'distrito',
                'direccion',
                'representante_legal',
                'dni_representante',
                'telefono',
                'email',
                'tipo_inspeccion',
                'area_inspeccion',
                'infraestructura',
                'saneamiento',
                'equipos_utensilios',
                'personal',
                'almacenamiento',
                'preparacion_alimentos',
                'documentacion',
                'control_plagas',
                'medida_aplicada',
                'plazo_cumplimiento_select',
                'observaciones_detalladas',
                'hora_fin',
                'inspector_acompanante'
            ]);

            // Agregar nuevas columnas del formato de infracciones
            $table->string('tipo_agente');
            $table->string('placa_1')->nullable();
            $table->string('razon_social');
            $table->string('ruc_dni');
            $table->datetime('fecha_hora_fin')->nullable();
            $table->string('nombre_conductor_1');
            $table->string('licencia_conductor_1');
            $table->string('clase_categoria')->nullable();
            $table->text('lugar_intervencion');
            $table->string('km_red_vial')->nullable();
            $table->string('origen_viaje');
            $table->string('destino_viaje');
            $table->string('tipo_servicio');
            $table->text('descripcion_hechos');
            $table->text('medios_probatorios')->nullable();
            $table->text('medidas_administrativas')->nullable();
            $table->text('sancion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspeccions', function (Blueprint $table) {
            // Revertir cambios
            $table->dropColumn([
                'tipo_agente',
                'placa_1',
                'razon_social',
                'ruc_dni',
                'fecha_hora_fin',
                'nombre_conductor_1',
                'licencia_conductor_1',
                'clase_categoria',
                'lugar_intervencion',
                'km_red_vial',
                'origen_viaje',
                'destino_viaje',
                'tipo_servicio',
                'descripcion_hechos',
                'medios_probatorios',
                'medidas_administrativas',
                'sancion'
            ]);
        });
    }
};
