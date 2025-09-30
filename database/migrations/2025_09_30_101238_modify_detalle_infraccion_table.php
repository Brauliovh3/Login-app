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
        Schema::table('detalle_infraccion', function (Blueprint $table) {
            // Cambiar descripcion de string a text para textos largos
            $table->text('descripcion')->nullable()->change();
            
            // Agregar campos adicionales para los detalles
            $table->string('subcategoria')->nullable()->after('descripcion'); // a), b), c), etc.
            $table->text('descripcion_detallada')->nullable()->after('subcategoria');
            $table->text('condiciones_especiales')->nullable()->after('descripcion_detallada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalle_infraccion', function (Blueprint $table) {
            // Revertir cambios
            $table->string('descripcion')->nullable()->change();
            $table->dropColumn(['subcategoria', 'descripcion_detallada', 'condiciones_especiales']);
        });
    }
};
