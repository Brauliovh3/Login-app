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
        if (! Schema::hasColumn('usuarios', 'blocked_at')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->timestamp('blocked_at')->nullable()->after('approved_by');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('usuarios', 'blocked_at')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropColumn('blocked_at');
            });
        }
    }
};
