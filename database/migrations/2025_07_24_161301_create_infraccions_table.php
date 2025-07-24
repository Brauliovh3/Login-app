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
        Schema::create('infraccions', function (Blueprint $table) {
            $table->id();
            $table->enum('agente_infractor', ['transportista', 'operador_ruta', 'conductor']);
            $table->string('placa', 10);
            $table->string('razon_social')->nullable();
            $table->string('ruc_dni', 20);
            $table->date('fecha_inicio');
            $table->time('hora_inicio');
            $table->date('fecha_fin')->nullable();
            $table->time('hora_fin')->nullable();
            $table->string('nombre_conductor1');
            $table->string('licencia_conductor1', 20);
            $table->string('clase_categoria', 10);
            $table->text('lugar_intervencion');
            $table->string('km_via_nacional', 50)->nullable();
            $table->string('origen_viaje');
            $table->string('destino_viaje');
            $table->enum('tipo_servicio', ['personas', 'mercancia']);
            $table->string('inspector');
            $table->text('descripcion_hechos');
            $table->text('medios_probatorios')->nullable();
            $table->string('calificacion_infraccion');
            $table->text('medidas_administrativas')->nullable();
            $table->text('sancion')->nullable();
            $table->text('observaciones_intervenido')->nullable();
            $table->text('observaciones_inspector')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Usuario que registró la infracción
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infraccions');
    }
};
