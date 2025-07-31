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
    {   //Agregue el campo inspector al Enum de roles
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['administrador', 'fiscalizador', 'ventanilla', 'inspector'])->default('ventanilla');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
