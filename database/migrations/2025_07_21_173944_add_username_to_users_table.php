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
        // Determinar qué tabla usar (para compatibilidad)
        $tableName = Schema::hasTable('usuarios') ? 'usuarios' : 'users';
        
        // Solo agregar la columna si no existe
        if (!Schema::hasColumn($tableName, 'username')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('username')->nullable()->after('name');
            });
        }
        
        // Actualizar los usuarios existentes con username basado en email si están vacíos
        DB::table($tableName)->whereNull('username')->orWhere('username', '')->get()->each(function ($user) use ($tableName) {
            $username = explode('@', $user->email)[0];
            $counter = 1;
            $originalUsername = $username;
            
            // Verificar si el username ya existe y agregar número si es necesario
            while (DB::table($tableName)->where('username', $username)->where('id', '!=', $user->id)->exists()) {
                $username = $originalUsername . $counter;
                $counter++;
            }
            
            DB::table($tableName)->where('id', $user->id)->update(['username' => $username]);
        });
        
        // Hacer el campo único después de llenar los datos
        Schema::table($tableName, function (Blueprint $table) {
            $table->string('username')->nullable(false)->unique()->change();
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
            $table->dropColumn('username');
        });
    }
};
