<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('actas')) {
            DB::statement("ALTER TABLE `actas` MODIFY `razon_social` VARCHAR(255) NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('actas')) {
            DB::statement("ALTER TABLE `actas` MODIFY `razon_social` VARCHAR(255) NOT NULL");
        }
    }
};
