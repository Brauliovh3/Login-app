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
        Schema::create('detalle_empresas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->string('tipo_documento');
            $table->string('numero_documento');
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento')->nullable();
            $table->string('entidad_emisora');
            $table->enum('estado_documento', ['vigente', 'vencido', 'suspendido', 'cancelado'])->default('vigente');
            $table->string('ruta_archivo')->nullable(); // Para almacenar documentos escaneados
            $table->string('observaciones', 500)->nullable();
            $table->timestamps();
            
            $table->index(['empresa_id', 'tipo_documento']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_empresas');
    }
};
