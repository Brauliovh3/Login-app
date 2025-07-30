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
        Schema::create('detalle_actas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sancion_id');
            $table->string('tipo_evidencia'); // foto, video, testimonio, medicion, etc.
            $table->string('descripcion_evidencia');
            $table->string('ruta_archivo')->nullable(); // Para fotos, videos, documentos
            $table->json('metadata')->nullable(); // Para almacenar datos adicionales como GPS, timestamp, etc.
            $table->datetime('fecha_hora_evidencia');
            $table->string('responsable_evidencia'); // Quien tomó la evidencia
            $table->enum('estado_evidencia', ['valida', 'invalida', 'en_revision'])->default('valida');
            $table->string('observaciones', 500)->nullable();
            $table->timestamps();
            
            $table->index(['sancion_id', 'tipo_evidencia']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_actas');
    }
};
