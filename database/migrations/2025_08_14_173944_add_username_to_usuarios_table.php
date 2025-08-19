<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Solo agregar la columna si no existe en 'usuarios'
        if (!Schema::hasColumn('usuarios', 'username')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->string('username')->nullable()->after('name');
            });
            
            // Actualizar los usuarios existentes con username basado en email si están vacíos
            DB::table('usuarios')->whereNull('username')->orWhere('username', '')->get()->each(function ($user) {
                $username = explode('@', $user->email)[0];
                $counter = 1;
                $originalUsername = $username;
                
                // Verificar si el username ya existe y agregar número si es necesario
                while (DB::table('usuarios')->where('username', $username)->where('id', '!=', $user->id)->exists()) {
                    $username = $originalUsername . $counter;
                    $counter++;
                }
                
                DB::table('usuarios')->where('id', $user->id)->update(['username' => $username]);
            });
            
            // Hacer el campo único después de llenar los datos
            Schema::table('usuarios', function (Blueprint $table) {
                $table->string('username')->nullable(false)->unique()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar la columna solo de 'usuarios'
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
