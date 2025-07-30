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
        Schema::create('conductores', function (Blueprint $table) {
            $table->id();
            $table->string('dni', 8)->unique();
            $table->string('nombres');
            $table->string('apellidos');
            $table->date('fecha_nacimiento');
            $table->string('direccion');
            $table->string('distrito', 50);
            $table->string('provincia', 50);
            $table->string('departamento', 50);
            $table->string('telefono', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('numero_licencia')->unique();
            $table->string('clase_categoria', 10); // A-I, A-IIa, A-IIb, A-IIIa, A-IIIb, A-IIIc
            $table->date('fecha_expedicion');
            $table->date('fecha_vencimiento');
            $table->enum('estado_licencia', ['vigente', 'vencida', 'suspendida', 'cancelada'])->default('vigente');
            $table->unsignedBigInteger('empresa_id')->nullable();
            $table->enum('estado', ['activo', 'inactivo', 'suspendido'])->default('activo');
            $table->integer('puntos_acumulados')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conductores');
    }
};
