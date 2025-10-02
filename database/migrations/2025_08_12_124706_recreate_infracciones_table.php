<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Deshabilitar verificación de claves foráneas temporalmente
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Eliminar tabla existente  
        Schema::dropIfExists('infracciones');
        
        // Crear nueva tabla con estructura completa y final
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
            $table->index('clase_pago');
            $table->index('estado');
        });
        
        // Restaurar verificación de claves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Esta migración no es reversible
        throw new Exception('Esta migración no se puede revertir.');
    }
};
