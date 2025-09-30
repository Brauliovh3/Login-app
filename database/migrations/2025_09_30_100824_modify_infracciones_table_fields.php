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
        Schema::table('infracciones', function (Blueprint $table) {
            // Modificar el campo sancion de decimal a string
            $table->string('sancion')->nullable()->change();
            
            // Modificar el campo tipo de boolean a string
            $table->string('tipo')->nullable()->change();
            
            // Modificar el campo medida_preventiva de boolean a text
            $table->text('medida_preventiva')->nullable()->change();
            
            // Modificar el campo otros_responsables__otros_beneficios de string a text
            $table->text('otros_responsables__otros_beneficios')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('infracciones', function (Blueprint $table) {
            // Revertir los cambios
            $table->decimal('sancion', 10, 2)->nullable()->change();
            $table->boolean('tipo')->default(false)->change();
            $table->boolean('medida_preventiva')->default(false)->change();
            $table->string('otros_responsables__otros_beneficios')->nullable()->change();
        });
    }
};
