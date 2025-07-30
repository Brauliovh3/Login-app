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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('ruc', 11)->unique();
            $table->string('razon_social');
            $table->string('nombre_comercial')->nullable();
            $table->string('direccion');
            $table->string('distrito', 50);
            $table->string('provincia', 50);
            $table->string('departamento', 50);
            $table->string('telefono', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('representante_legal');
            $table->string('dni_representante', 8);
            $table->enum('tipo_servicio', ['personas', 'carga', 'mixto']);
            $table->enum('ambito', ['urbano', 'interprovincial', 'internacional']);
            $table->date('fecha_autorizacion')->nullable();
            $table->string('numero_autorizacion')->nullable();
            $table->enum('estado', ['activo', 'suspendido', 'cancelado'])->default('activo');
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
