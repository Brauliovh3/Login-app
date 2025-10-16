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
            if (!Schema::hasColumn('actas', 'fiscalizador_id')) {
                $table->unsignedBigInteger('fiscalizador_id')->nullable()->after('inspector_responsable');
                $table->index('fiscalizador_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actas', function (Blueprint $table) {
            if (Schema::hasColumn('actas', 'fiscalizador_id')) {
                $table->dropIndex(['fiscalizador_id']);
                $table->dropColumn('fiscalizador_id');
            }
        });
    }
};
