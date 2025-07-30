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
        Schema::create('infracciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_infraccion', 20)->unique(); // G.01, G.02, etc.
            $table->string('descripcion');
            $table->text('detalle_completo');
            $table->enum('gravedad', ['muy_grave', 'grave', 'leve']);
            $table->decimal('multa_uit', 4, 2); // Multa en UIT
            $table->decimal('multa_soles', 8, 2); // Multa en soles
            $table->integer('puntos_licencia')->default(0);
            $table->boolean('retencion_licencia')->default(false);
            $table->boolean('retencion_vehiculo')->default(false);
            $table->boolean('internamiento_deposito')->default(false);
            $table->enum('estado', ['activo', 'inactivo', 'derogado'])->default('activo');
            $table->string('base_legal', 200)->nullable();
            $table->timestamps();
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
