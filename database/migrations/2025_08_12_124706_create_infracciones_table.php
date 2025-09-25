<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{


    public function up(): void
    {

        Schema::create('infracciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('aplica_sobre')->nullable();
            $table->text('reglamento')->nullable();
            $table->string('norma_modificatoria')->nullable();
            $table->string('clase_pago')->nullable();
            $table->decimal('sancion', 10, 2)->nullable();
            $table->boolean('tipo')->default(false);
            $table->boolean('medida_preventiva')->default(false);
            $table->string('gravedad')->nullable();
            $table->string('otros_responsables__otros_beneficios')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infracciones');
    }
};
