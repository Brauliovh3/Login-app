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
            $table->string('placa')->unique();
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->integer('año')->nullable();
            $table->string('color')->nullable();
            $table->string('numero_motor')->nullable();
            $table->string('numero_chasis')->nullable();
            $table->string('clase')->nullable();
            $table->string('categoria')->nullable();
            $table->string('combustible')->nullable();
            $table->integer('asientos')->nullable();
            $table->float('peso_bruto')->nullable();
            $table->float('carga_util')->nullable();
            $table->unsignedBigInteger('empresa_id')->nullable();
            $table->unsignedBigInteger('conductor_id')->nullable();
            $table->string('estado')->default('activo');
            $table->date('fecha_soat')->nullable();
            $table->date('fecha_revision_tecnica')->nullable();
            $table->timestamps();
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('set null');
            $table->foreign('conductor_id')->references('id')->on('conductores')->onDelete('set null');
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
