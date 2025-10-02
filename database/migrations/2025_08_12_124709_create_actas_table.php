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
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('numero_acta')->unique();
            $table->string('codigo_ds')->default('017-2009-MTC');

            // Datos de la intervención
            // Permitir null para no requerir un valor por defecto durante seeders
            $table->string('lugar_intervencion')->nullable();
            $table->date('fecha_intervencion');
            $table->time('hora_intervencion');
            // Inspector responsable (puede ser nulo durante seeders)
            $table->string('inspector_responsable')->nullable();
            // Tipo de servicio (puede ser nulo durante seeders)
            $table->string('tipo_servicio')->nullable();

            // Datos del infractor
            $table->enum('tipo_agente', ['Transportista', 'Operador de Ruta', 'Conductor', 'Inspector']);
            $table->string('placa')->nullable(); // Hacer nullable como estaba en las migraciones eliminadas
            $table->string('placa_vehiculo')->nullable(); // Campo legacy
            $table->string('razon_social')->nullable(); // Hacer nullable como estaba en las migraciones eliminadas  
            $table->string('ruc_dni')->nullable(); // Hacer nullable como estaba en las migraciones eliminadas
            $table->string('nombre_conductor')->nullable();
            $table->string('licencia')->nullable();
            $table->string('clase_licencia')->nullable();
            $table->string('origen')->nullable();
            $table->string('destino')->nullable();
            $table->integer('numero_personas')->nullable();
            // Ubicación del hecho (legacy / usado por seeders)
            $table->string('ubicacion')->nullable();

            // Nueva columna para relación con conductores
            $table->unsignedBigInteger('conductor_id')->nullable();
            $table->foreign('conductor_id')->references('id')->on('conductores')->onDelete('set null');

            // Nueva columna para relacion con infracciones
            $table->unsignedBigInteger('infraccion_id')->nullable();
            $table->foreign('infraccion_id')->references('id')->on('infracciones')->onDelete('set null');

            // Relación con inspector (usada por seeders y controladores)
            // La columna se crea aquí; no añadimos una FK rígida para evitar
            // fallos en entornos con tablas `users`/`usuarios` distintas.
            $table->unsignedBigInteger('inspector_id')->nullable()->index();

            // Relación con vehículo - FK se creará después por orden de migraciones
            $table->unsignedBigInteger('vehiculo_id')->nullable();

            // Descripción de hechos e infracciones
            $table->text('descripcion_hechos');
            $table->text('medios_probatorios')->nullable();
            $table->enum('calificacion', ['Leve', 'Grave', 'Muy Grave']);
            $table->string('medida_administrativa')->nullable();
            $table->string('sancion')->nullable();
            $table->decimal('monto_multa', 10, 2)->nullable();
            $table->text('observaciones_intervenido')->nullable();
            $table->text('observaciones_inspector')->nullable();
            // Campo usado por seeders y scripts antiguos
            $table->text('observaciones')->nullable();

            // Estado y metadatos (cambiar a tinyint como estaba en migraciones eliminadas)
            $table->tinyInteger('estado')->default(0); // 0=pendiente, 1=procesada, 2=anulada, 3=pagada
            // Inspector que creó el acta. Usamos columna idx en lugar de FK para
            // evitar errores en instalaciones con tablas distintas.
            $table->unsignedBigInteger('user_id')->nullable()->index();

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
