<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conductores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('apellidos', 100);
            $table->string('dni', 8)->unique();
            $table->string('licencia', 15)->unique();
            $table->enum('categoria', ['A1', 'A2a', 'A2b', 'A3a', 'A3b', 'A3c', 'B1', 'B2a', 'B2b', 'B2c']);
            $table->date('fecha_vencimiento');
            $table->enum('estado', ['vigente', 'vencida', 'suspendida', 'anulada'])->default('vigente');
            $table->string('telefono', 15)->nullable();
            $table->string('direccion', 200)->nullable();
            $table->date('fecha_expedicion');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conductores');
    }
};
