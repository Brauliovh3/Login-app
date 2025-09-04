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
        Schema::create('carga_pasajeros', function (Blueprint $table) {
            $table->id();
            $table->string('informe')->nullable();
            $table->string('resolucion')->nullable();
            $table->string('conductor');
            $table->string('licencia_conductor')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carga_pasajeros');
    }
};
