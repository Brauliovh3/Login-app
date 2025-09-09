<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasTable('actas_eliminadas')) {
            Schema::create('actas_eliminadas', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('original_acta_id')->nullable();
                // Campos principales (permitir null para máxima compatibilidad)
                $table->string('numero_acta')->nullable();
                $table->text('descripcion_hechos')->nullable();
                $table->text('descripcion')->nullable();
                $table->string('razon_social')->nullable();
                $table->string('ruc_dni')->nullable();
                $table->string('nombre_conductor')->nullable();
                $table->string('licencia')->nullable();
                $table->string('licencia_conductor')->nullable();
                $table->string('placa')->nullable();
                $table->string('placa_vehiculo')->nullable();
                $table->decimal('monto_multa', 12, 2)->nullable();
                $table->string('lugar_intervencion')->nullable();
                $table->date('fecha_intervencion')->nullable();
                $table->string('hora_intervencion')->nullable();
                $table->string('inspector_responsable')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();

                // Metadatos de eliminación
                $table->text('motivo_eliminacion')->nullable();
                $table->text('observaciones_eliminacion')->nullable();
                $table->string('supervisor_eliminante')->nullable();
                $table->timestamp('deleted_at')->nullable();
                $table->unsignedBigInteger('deleted_by')->nullable();

                $table->index('original_acta_id');
                $table->index('numero_acta');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('actas_eliminadas');
    }
};
