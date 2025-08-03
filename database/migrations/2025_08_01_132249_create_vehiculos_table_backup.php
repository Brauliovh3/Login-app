<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('placa', 10)->unique();
            $table->string('modelo', 100);
            $table->string('marca', 50);
            $table->year('año');
            $table->string('color', 30);
            $table->string('tipo', 30); // automovil, camioneta, bus, etc.
            $table->enum('estado', ['activo', 'inactivo', 'suspendido'])->default('activo');
            $table->string('numero_motor', 50)->nullable();
            $table->string('numero_chasis', 50)->nullable();
            $table->string('propietario_nombre', 100);
            $table->string('propietario_dni', 8);
            $table->date('fecha_registro');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
