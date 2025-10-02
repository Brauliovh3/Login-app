<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    

    public function up(): void
    {
        Schema::create('detalle_infraccion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('infraccion_id')->constrained('infracciones')->onDelete('cascade');
            $table->string('subcategoria')->nullable(); // a), b), c), etc.
            $table->text('descripcion_detallada'); // Descripción específica del sub-ítem
            $table->text('condiciones_especiales')->nullable(); // Condiciones específicas
            $table->string('observaciones')->nullable(); // Observaciones adicionales
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index('infraccion_id');
            $table->index('subcategoria');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('detalle_infraccion');
    }
};
