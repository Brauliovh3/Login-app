<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    

    public function up(): void
    {
        Schema::create('detalle_infraccion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('infraccion_id')->constrained('infracciones')->onDelete('cascade');
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('detalle_infraccion');
    }
};
