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
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_categoria', 10)->unique(); // M1, M2, M3, N1, N2, N3, O1, O2, etc.
            $table->string('nombre');
            $table->string('descripcion');
            $table->enum('tipo_vehiculo', ['particular', 'publico', 'carga', 'especial']);
            $table->integer('capacidad_max_pasajeros')->nullable();
            $table->decimal('peso_bruto_max', 8, 2)->nullable(); // en toneladas
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->string('observaciones', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};
