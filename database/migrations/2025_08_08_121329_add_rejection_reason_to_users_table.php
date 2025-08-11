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
        // Determinar qué tabla usar (para compatibilidad)
        $tableName = Schema::hasTable('usuarios') ? 'usuarios' : 'users';
        
        Schema::table($tableName, function (Blueprint $table) {
            // Esta migración está incluida en la de campos de aprobación
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Determinar qué tabla usar (para compatibilidad)
        $tableName = Schema::hasTable('usuarios') ? 'usuarios' : 'users';
        
        Schema::table($tableName, function (Blueprint $table) {
            // Esta migración está incluida en la de campos de aprobación
        });
    }
};
