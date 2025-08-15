<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('actas')) {
            Schema::table('actas', function (Blueprint $table) {
                if (!Schema::hasColumn('actas', 'nombre_conductor')) {
                    $table->string('nombre_conductor')->nullable()->after('placa_vehiculo');
                }
                if (!Schema::hasColumn('actas', 'ruc_dni')) {
                    $table->string('ruc_dni', 30)->nullable()->after('nombre_conductor');
                }
                if (!Schema::hasColumn('actas', 'licencia_conductor')) {
                    $table->string('licencia_conductor')->nullable()->after('ruc_dni');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('actas')) {
            Schema::table('actas', function (Blueprint $table) {
                if (Schema::hasColumn('actas', 'licencia_conductor')) {
                    $table->dropColumn('licencia_conductor');
                }
                if (Schema::hasColumn('actas', 'ruc_dni')) {
                    $table->dropColumn('ruc_dni');
                }
                if (Schema::hasColumn('actas', 'nombre_conductor')) {
                    $table->dropColumn('nombre_conductor');
                }
            });
        }
    }
};
