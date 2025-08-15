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
        Schema::create('actas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_acta')->unique();
            $table->string('codigo_ds')->default('017-2009-MTC');
            
            // Datos de la intervención
            $table->string('lugar_intervencion');
            $table->date('fecha_intervencion');
            $table->time('hora_intervencion');
            $table->string('inspector_responsable');
            $table->string('tipo_servicio');
            
            // Datos del infractor
            $table->enum('tipo_agente', ['Transportista', 'Operador de Ruta', 'Conductor']);
            $table->string('placa');
            $table->string('razon_social');
            $table->string('ruc_dni');
            $table->string('nombre_conductor')->nullable();
            $table->string('licencia')->nullable();
            $table->string('clase_licencia')->nullable();
            $table->string('origen')->nullable();
            $table->string('destino')->nullable();
            $table->integer('numero_personas')->nullable();
            
            // Descripción de hechos e infracciones
            $table->text('descripcion_hechos');
            $table->text('medios_probatorios')->nullable();
            $table->enum('calificacion', ['Leve', 'Grave', 'Muy Grave']);
            $table->string('medida_administrativa')->nullable();
            $table->string('sancion')->nullable();
            $table->text('observaciones_intervenido')->nullable();
            $table->text('observaciones_inspector')->nullable();
            
            // Estado y metadatos
            $table->enum('estado', ['pendiente', 'procesada', 'anulada', 'pagada'])->default('pendiente');
            $table->foreignId('user_id')->constrained('usuarios')->onDelete('cascade'); // Inspector que creó el acta
            
            $table->timestamps();
            
            // Índices
            $table->index(['fecha_intervencion', 'estado']);
            $table->index(['ruc_dni']);
            $table->index(['placa']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actas');
    }
};
