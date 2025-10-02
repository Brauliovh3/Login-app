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
        Schema::create('carga_pasajeros', function (Blueprint $table) {
            $table->id();
            $table->string('informe')->nullable();
            $table->string('resolucion')->nullable();
            $table->string('conductor');
            $table->string('licencia_conductor')->nullable();
            $table->string('estado')->default('activo'); // Campo consolidado de migraciones eliminadas
            $table->timestamps(); // Timestamps consolidados de migraciones eliminadas
            
            // Índices para optimizar consultas
            $table->index('conductor');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carga_pasajeros');
    }
};
