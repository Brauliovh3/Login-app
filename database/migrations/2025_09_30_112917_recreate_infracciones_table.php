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
            $table->string('codigo_infraccion')->unique();
            $table->string('base_legal');
            $table->text('descripcion');
            $table->text('detalle_completo');
            $table->string('estado')->default('activo');
            $table->enum('gravedad', ['leve', 'grave', 'muy_grave']);
            $table->string('multa_soles');
            $table->string('multa_uit');
            $table->integer('puntos_licencia')->default(0);
            $table->boolean('retencion_licencia')->default(false);
            $table->boolean('retencion_vehiculo')->default(false);
            $table->boolean('internamiento_deposito')->default(false);
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index('codigo_infraccion');
            $table->index('estado');
            $table->index('gravedad');
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
