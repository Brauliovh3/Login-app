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
        Schema::create('inspectores', function (Blueprint $table) {
            $table->id();
            $table->string('dni', 8)->unique();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('codigo_inspector', 20)->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('email')->nullable();
            $table->date('fecha_ingreso');
            $table->enum('estado', ['activo', 'inactivo', 'licencia', 'vacaciones'])->default('activo');
            $table->string('zona_asignada')->nullable();
            $table->string('observaciones', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspectores');
    }
};
