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
            if (!Schema::hasColumn('actas', 'anio_acta')) {
                $table->string('anio_acta', 4)->nullable()->after('numero_acta');
                $table->index('anio_acta');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actas', function (Blueprint $table) {
            if (Schema::hasColumn('actas', 'anio_acta')) {
                $table->dropIndex(['anio_acta']);
                $table->dropColumn('anio_acta');
            }
        });
    }
};
