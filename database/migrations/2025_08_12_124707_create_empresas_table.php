<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social');
            $table->string('ruc')->unique();
            $table->string('nombre_comercial')->nullable();
            $table->string('direccion')->nullable();
            $table->string('distrito')->nullable();
            $table->string('provincia')->nullable();
            $table->string('departamento')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('representante_legal')->nullable();
            $table->string('dni_representante')->nullable();
            $table->string('tipo_servicio')->nullable();
            $table->string('ambito')->nullable();
            $table->date('fecha_autorizacion')->nullable();
            $table->string('numero_autorizacion')->nullable();
            $table->string('estado')->default('activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
