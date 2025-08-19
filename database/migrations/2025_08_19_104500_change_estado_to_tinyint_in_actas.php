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
            // Change estado to tinyint to accept numeric statuses used by seeders
            DB::statement("ALTER TABLE `actas` MODIFY `estado` TINYINT(1) NOT NULL DEFAULT 0");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('actas')) {
            // Revert to original enum definition
            DB::statement("ALTER TABLE `actas` MODIFY `estado` ENUM('pendiente','procesada','anulada','pagada') NOT NULL DEFAULT 'pendiente'");
        }
    }
};
