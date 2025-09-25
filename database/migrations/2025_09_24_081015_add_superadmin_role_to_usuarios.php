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
        // Modificar la columna role para permitir 'superadmin' si usa ENUM
        // Como no podemos modificar ENUM directamente, mejor cambiar a string si es necesario
        
        // Verificar si ya existe un usuario superadmin
        $superAdminExists = DB::table('usuarios')->where('role', 'superadmin')->exists();
        
        if (!$superAdminExists) {
            // Si la columna es string, no hay problema
            // Si es ENUM, podríamos necesitar cambiarla
            
            // Añadir comentario para documentar que superadmin está soportado
            DB::statement('ALTER TABLE usuarios MODIFY COLUMN role VARCHAR(50) DEFAULT "fiscalizador"');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No hacer nada en el rollback para mantener compatibilidad
        // En caso de necesidad crítica, se puede eliminar usuarios superadmin manualmente
    }
};
