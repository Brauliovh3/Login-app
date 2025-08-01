<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inspecciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero_inspeccion')->unique();
            $table->foreignId('vehiculo_id')->constrained('vehiculos');
            $table->foreignId('inspector_id')->constrained('inspectores');
            $table->datetime('fecha_inspeccion');
            $table->enum('tipo_inspeccion', ['rutina', 'especial', 'emergencia']);
            $table->text('observaciones');
            $table->enum('estado_vehiculo', ['optimo', 'bueno', 'regular', 'deficiente']);
            $table->enum('estado', ['pendiente', 'completada', 'cancelada'])->default('pendiente');
            $table->timestamps();
        });

        Schema::create('inspeccion_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspeccion_id')->constrained('inspecciones')->onDelete('cascade');
            $table->string('item');
            $table->enum('estado', ['conforme', 'no_conforme']);
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inspeccion_items');
        Schema::dropIfExists('inspecciones');
    }
};
