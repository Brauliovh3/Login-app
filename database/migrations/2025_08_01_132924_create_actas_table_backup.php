<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_acta', 20)->unique();
            $table->foreignId('vehiculo_id')->constrained('vehiculos');
            $table->foreignId('conductor_id')->constrained('conductores');
            $table->foreignId('infraccion_id')->constrained('infracciones');
            $table->foreignId('inspector_id')->constrained('inspectores');
            $table->string('ubicacion', 200);
            $table->text('descripcion');
            $table->decimal('monto_multa', 10, 2);
            $table->enum('estado', ['pendiente', 'procesada', 'pagada', 'anulada'])->default('pendiente');
            $table->timestamp('fecha_infraccion');
            $table->timestamp('fecha_procesamiento')->nullable();
            $table->foreignId('procesado_por')->nullable()->constrained('users');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actas');
    }
};
