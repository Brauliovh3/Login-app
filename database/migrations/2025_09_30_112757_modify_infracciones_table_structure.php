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
        Schema::table('infracciones', function (Blueprint $table) {
            // Eliminar columnas existentes que no necesitamos
            $table->dropColumn([
                'aplica_sobre',
                'reglamento',
                'norma_modificatoria',
                'clase_pago',
                'tipo',
                'medida_preventiva',
                'otros_responsables__otros_beneficios'
            ]);
            
            // Renombrar columnas existentes
            $table->renameColumn('codigo', 'codigo_infraccion');
            $table->renameColumn('sancion', 'multa_soles');
            
            // Agregar nuevas columnas necesarias
            $table->string('base_legal')->after('codigo_infraccion');
            $table->text('descripcion')->after('base_legal');
            $table->text('detalle_completo')->after('descripcion');
            $table->string('estado')->default('activo')->after('detalle_completo');
            $table->string('multa_uit')->after('multa_soles');
            $table->integer('puntos_licencia')->default(0)->after('multa_uit');
            $table->boolean('retencion_licencia')->default(false)->after('puntos_licencia');
            $table->boolean('retencion_vehiculo')->default(false)->after('retencion_licencia');
            $table->boolean('internamiento_deposito')->default(false)->after('retencion_vehiculo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('infracciones', function (Blueprint $table) {
            // Revertir los cambios
            $table->dropColumn([
                'base_legal',
                'descripcion', 
                'detalle_completo',
                'estado',
                'multa_uit',
                'puntos_licencia',
                'retencion_licencia',
                'retencion_vehiculo',
                'internamiento_deposito'
            ]);
            
            // Restaurar nombres originales
            $table->renameColumn('codigo_infraccion', 'codigo');
            $table->renameColumn('multa_soles', 'sancion');
            
            // Restaurar columnas originales
            $table->string('aplica_sobre');
            $table->string('reglamento');
            $table->string('norma_modificatoria');
            $table->string('clase_pago');
            $table->string('tipo');
            $table->string('medida_preventiva');
            $table->text('otros_responsables__otros_beneficios');
        });
    }
};
