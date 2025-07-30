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
        // Agregar claves foráneas a la tabla vehiculos
        Schema::table('vehiculos', function (Blueprint $table) {
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('set null');
            $table->foreign('conductor_id')->references('id')->on('conductores')->onDelete('set null');
        });

        // Agregar claves foráneas a la tabla conductores
        Schema::table('conductores', function (Blueprint $table) {
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('set null');
        });

        // Agregar claves foráneas a la tabla sanciones
        Schema::table('sanciones', function (Blueprint $table) {
            $table->foreign('vehiculo_id')->references('id')->on('vehiculos')->onDelete('cascade');
            $table->foreign('conductor_id')->references('id')->on('conductores')->onDelete('cascade');
            $table->foreign('infraccion_id')->references('id')->on('infracciones')->onDelete('cascade');
            $table->foreign('inspector_id')->references('id')->on('inspectores')->onDelete('set null');
            $table->foreign('pnp_id')->references('id')->on('pnp')->onDelete('set null');
        });

        // Agregar claves foráneas a la tabla detalle_empresas
        Schema::table('detalle_empresas', function (Blueprint $table) {
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
        });

        // Agregar claves foráneas a la tabla detalle_actas
        Schema::table('detalle_actas', function (Blueprint $table) {
            $table->foreign('sancion_id')->references('id')->on('sanciones')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar claves foráneas
        Schema::table('detalle_actas', function (Blueprint $table) {
            $table->dropForeign(['sancion_id']);
        });

        Schema::table('detalle_empresas', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
        });

        Schema::table('sanciones', function (Blueprint $table) {
            $table->dropForeign(['vehiculo_id']);
            $table->dropForeign(['conductor_id']);
            $table->dropForeign(['infraccion_id']);
            $table->dropForeign(['inspector_id']);
            $table->dropForeign(['pnp_id']);
        });

        Schema::table('conductores', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
        });

        Schema::table('vehiculos', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
            $table->dropForeign(['conductor_id']);
        });
    }
};
