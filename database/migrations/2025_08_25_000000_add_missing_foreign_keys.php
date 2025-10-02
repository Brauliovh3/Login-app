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
        // Añadir foreign key de actas -> vehiculos
        Schema::table('actas', function (Blueprint $table) {
            $table->foreign('vehiculo_id')->references('id')->on('vehiculos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actas', function (Blueprint $table) {
            $table->dropForeign(['vehiculo_id']);
        });
    }
};