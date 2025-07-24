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
        Schema::create('inspeccions', function (Blueprint $table) {
            $table->id();
            
            // Información automática
            $table->date('fecha_inspeccion');
            $table->time('hora_inicio');
            $table->time('hora_fin')->nullable();
            $table->string('inspector_principal');
            $table->string('inspector_acompanante')->nullable();
            
            // Información del establecimiento
            $table->string('tipo_establecimiento');
            $table->string('nombre_establecimiento');
            $table->string('ruc_dni_establecimiento');
            $table->string('departamento');
            $table->string('provincia');
            $table->string('distrito');
            $table->text('direccion');
            
            // Información del representante
            $table->string('representante_legal');
            $table->string('dni_representante');
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            
            // Tipo de inspección
            $table->string('tipo_inspeccion');
            $table->string('area_inspeccion');
            
            // Lista de verificación sanitaria
            $table->string('infraestructura');
            $table->string('saneamiento');
            $table->string('equipos_utensilios');
            $table->string('personal');
            $table->string('almacenamiento');
            $table->string('preparacion_alimentos');
            $table->string('documentacion');
            $table->string('control_plagas');
            
            // Medidas administrativas
            $table->string('medida_aplicada');
            $table->string('calificacion_infraccion')->nullable();
            $table->string('plazo_cumplimiento_select')->nullable();
            
            // Observaciones
            $table->text('observaciones_detalladas');
            $table->text('observaciones_intervenido')->nullable();
            $table->text('observaciones_inspector')->nullable();
            
            // Usuario que creó el registro
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspeccions');
    }
};
