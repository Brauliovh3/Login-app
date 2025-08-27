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
        // Evitar crear la tabla si ya existe (previene migraciones duplicadas en entornos
        // donde exista una versión más reciente de esta migración).
        if (Schema::hasTable('infracciones')) {
            // Ya existe: no hacer nada para evitar errores por duplicado.
            return;
        }

        Schema::create('infracciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_infraccion')->unique();
            $table->string('descripcion');
            $table->text('detalle_completo')->nullable();
            $table->string('gravedad')->nullable();
            $table->float('multa_uit')->nullable();
            $table->float('multa_soles')->nullable();
            $table->integer('puntos_licencia')->nullable();
            $table->boolean('retencion_licencia')->default(false);
            $table->boolean('retencion_vehiculo')->default(false);
            $table->boolean('internamiento_deposito')->default(false);
            $table->string('estado')->default('activo');
            $table->string('base_legal')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infracciones');
    }
};
