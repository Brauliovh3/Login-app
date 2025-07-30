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
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('placa', 10)->unique();
            $table->string('marca', 50);
            $table->string('modelo', 50);
            $table->string('color', 30);
            $table->year('año');
            $table->string('numero_motor', 50)->nullable();
            $table->string('numero_chasis', 50)->nullable();
            $table->string('clase', 30); // A, B, C, etc.
            $table->string('categoria', 50); // M1, M2, N1, etc.
            $table->enum('combustible', ['gasolina', 'diesel', 'gas', 'electrico', 'hibrido']);
            $table->integer('asientos')->nullable();
            $table->decimal('peso_bruto', 8, 2)->nullable();
            $table->decimal('carga_util', 8, 2)->nullable();
            $table->unsignedBigInteger('empresa_id')->nullable();
            $table->unsignedBigInteger('conductor_id')->nullable();
            $table->enum('estado', ['activo', 'inactivo', 'suspendido'])->default('activo');
            $table->date('fecha_soat')->nullable();
            $table->date('fecha_revision_tecnica')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
