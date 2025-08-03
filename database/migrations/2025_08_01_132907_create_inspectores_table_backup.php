<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspectores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('apellidos', 100);
            $table->string('dni', 8)->unique();
            $table->string('codigo_inspector', 10)->unique();
            $table->enum('estado', ['activo', 'inactivo', 'suspendido'])->default('activo');
            $table->string('telefono', 15)->nullable();
            $table->string('email', 100)->nullable();
            $table->date('fecha_ingreso');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspectores');
    }
};
