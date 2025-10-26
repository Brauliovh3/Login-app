<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crear sistema completo de infracciones
     * Esta migración reemplaza todas las migraciones problemáticas anteriores
     */
    public function up(): void
    {
        // Crear tabla infracciones con estructura EXACTA de la migración original
        Schema::create('infracciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_infraccion')->unique(); // Código (F.1, F.2, etc.)
            $table->string('aplica_sobre'); // Aplica sobre (Transportista, Conductor, etc.)
            $table->string('reglamento'); // Reglamento Nacional de Administración de Transportes - RENAT
            $table->string('norma_modificatoria'); // D.S. N° 017-2009-MTC, etc.
            $table->text('infraccion'); // Descripción completa de la infracción
            $table->enum('clase_pago', ['Pecuniaria', 'No pecuniaria']); // Tipo de pago
            $table->string('sancion'); // Sanción específica (1 UIT, Suspensión, etc.)
            $table->enum('tipo', ['Infracción'])->default('Infracción'); // Tipo (siempre Infracción)
            $table->text('medida_preventiva')->nullable(); // Medidas preventivas
            $table->enum('gravedad', ['Leve', 'Grave', 'Muy grave']); // Gravedad
            $table->text('otros_responsables_otros_beneficios')->nullable(); // Responsabilidad solidaria y descuentos
            $table->string('estado')->default('activo'); // Estado de la infracción
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index('codigo_infraccion');
            $table->index('aplica_sobre');
            $table->index('gravedad');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infracciones');
    }
};