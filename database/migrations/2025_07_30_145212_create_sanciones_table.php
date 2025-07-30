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
        Schema::create('sanciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero_acta', 20)->unique();
            $table->datetime('fecha_hora_infraccion');
            $table->string('lugar_infraccion');
            $table->string('distrito', 50);
            $table->string('provincia', 50);
            $table->string('departamento', 50);
            $table->unsignedBigInteger('vehiculo_id');
            $table->unsignedBigInteger('conductor_id');
            $table->unsignedBigInteger('infraccion_id');
            $table->unsignedBigInteger('inspector_id')->nullable();
            $table->unsignedBigInteger('pnp_id')->nullable();
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['pendiente', 'pagada', 'anulada', 'prescrita', 'en_cobranza'])->default('pendiente');
            $table->decimal('monto_multa', 8, 2);
            $table->decimal('monto_pagado', 8, 2)->default(0);
            $table->date('fecha_vencimiento');
            $table->date('fecha_pago')->nullable();
            $table->string('numero_recibo')->nullable();
            $table->boolean('licencia_retenida')->default(false);
            $table->boolean('vehiculo_retenido')->default(false);
            $table->boolean('vehiculo_internado')->default(false);
            $table->string('deposito_vehiculo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sanciones');
    }
};
