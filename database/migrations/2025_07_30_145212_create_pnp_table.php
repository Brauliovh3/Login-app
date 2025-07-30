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
        Schema::create('pnp', function (Blueprint $table) {
            $table->id();
            $table->string('dni', 8)->unique();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('numero_placa', 15)->unique();
            $table->string('grado');
            $table->string('unidad_asignada');
            $table->string('telefono', 20)->nullable();
            $table->string('email')->nullable();
            $table->enum('estado', ['activo', 'inactivo', 'comision', 'licencia'])->default('activo');
            $table->date('fecha_ingreso');
            $table->string('observaciones', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pnp');
    }
};
