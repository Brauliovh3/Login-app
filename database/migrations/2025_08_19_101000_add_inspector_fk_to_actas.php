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
        if (Schema::hasTable('actas') && Schema::hasTable('inspectores')) {
            Schema::table('actas', function (Blueprint $table) {
                $table->foreign('inspector_id')->references('id')->on('inspectores')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('actas')) {
            Schema::table('actas', function (Blueprint $table) {
                $table->dropForeign(['inspector_id']);
            });
        }
    }
};
