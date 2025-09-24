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
        // Evitar crear la tabla si ya existe (previene migraciones duplicadas en entornos
        // donde exista una versión más reciente de esta migración).
        if (Schema::hasTable('infracciones')) {
            // Ya existe: no hacer nada para evitar errores por duplicado.
            return;
        }

        Schema::create('infracciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_infraccion')->unique();
            $table->string('aplica_sobre')->nullable();
            $table->text('reglamento')->nullable();
            $table->string('gravedad')->nullable();
            $table->float('Norma_modificatoria')->nullable();
            $table->float('clase_pago')->nullable();
            $table->integer('Sancion')->nullable();
            $table->boolean('Tipo')->default(false);
            $table->boolean('Medida_preventiva')->default(false);
            $table->boolean('gravedad')->default(false);
            $table->string('Otros_responsables_Otros_beneficios')->default('activo');
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
