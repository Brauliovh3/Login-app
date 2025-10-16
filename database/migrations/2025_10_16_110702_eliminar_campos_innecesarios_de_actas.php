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
        Schema::table('actas', function (Blueprint $table) {
            // Eliminar campos que no se usan
            if (Schema::hasColumn('actas', 'clase_licencia')) {
                $table->dropColumn('clase_licencia');
            }
            if (Schema::hasColumn('actas', 'monto_multa')) {
                $table->dropColumn('monto_multa');
            }
            if (Schema::hasColumn('actas', 'user_id')) {
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('actas', 'has_evidencias')) {
                $table->dropColumn('has_evidencias');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actas', function (Blueprint $table) {
            $table->string('clase_licencia')->nullable();
            $table->decimal('monto_multa', 10, 2)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('has_evidencias')->default(false);
        });
    }
};
