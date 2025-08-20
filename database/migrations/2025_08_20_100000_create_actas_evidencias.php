<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Crear tabla para guardar referencias a imágenes/evidencias asociadas a actas
        if (!Schema::hasTable('actas_evidencias')) {
            Schema::create('actas_evidencias', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('acta_id')->nullable()->index();
                $table->string('filename')->nullable();
                $table->string('path')->nullable();
                $table->string('mime')->nullable();
                $table->bigInteger('size')->nullable();
                $table->timestamps();

                // FK si existe tabla actas
                if (Schema::hasTable('actas')) {
                    $table->foreign('acta_id')->references('id')->on('actas')->onDelete('cascade');
                }
            });
        }

        // Añadir un flag en actas para indicar que tiene evidencias (opcional, conveniente)
        if (Schema::hasTable('actas') && !Schema::hasColumn('actas', 'has_evidencias')) {
            Schema::table('actas', function (Blueprint $table) {
                $table->boolean('has_evidencias')->default(false)->after('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('actas')) {
            if (Schema::hasColumn('actas', 'has_evidencias')) {
                Schema::table('actas', function (Blueprint $table) {
                    $table->dropColumn('has_evidencias');
                });
            }
        }

        Schema::dropIfExists('actas_evidencias');
    }
};
