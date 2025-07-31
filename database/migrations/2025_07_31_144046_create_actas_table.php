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
        Schema::create('actas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_acta', 20)->unique();
            $table->unsignedBigInteger('inspector_id');
            $table->unsignedBigInteger('vehiculo_id')->nullable();
            $table->unsignedBigInteger('conductor_id')->nullable();
            $table->unsignedBigInteger('infraccion_id');
            $table->string('placa_vehiculo', 8)->nullable();
            $table->string('ubicacion', 200);
            $table->text('descripcion');
            $table->decimal('monto_multa', 10, 2);
            $table->enum('estado', ['registrada', 'procesada', 'pendiente', 'anulada'])->default('registrada');
            $table->date('fecha_infraccion');
            $table->time('hora_infraccion');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('inspector_id')->references('id')->on('inspectores');
            $table->foreign('vehiculo_id')->references('id')->on('vehiculos');
            $table->foreign('conductor_id')->references('id')->on('conductores');
            $table->foreign('infraccion_id')->references('id')->on('infracciones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actas');
    }
};
