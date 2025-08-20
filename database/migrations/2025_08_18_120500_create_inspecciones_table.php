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
        Schema::create('inspecciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero_inspeccion')->unique();
            $table->unsignedBigInteger('vehiculo_id');
            $table->unsignedBigInteger('inspector_id');
            $table->date('fecha_inspeccion');
            $table->string('tipo_inspeccion')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('estado_vehiculo')->nullable();
            $table->string('estado')->default('completada');
            $table->timestamps();
            $table->foreign('vehiculo_id')->references('id')->on('vehiculos')->onDelete('cascade');
            $table->foreign('inspector_id')->references('id')->on('inspectores')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspecciones');
    }
};
