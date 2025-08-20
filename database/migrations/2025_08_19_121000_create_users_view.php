<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Crear o reemplazar una vista llamada `users` que lea de la tabla `usuarios`
        // Esto no modifica los datos ni la tabla `usuarios`, solo provee compatibilidad
        // para código o queries que accidentalmente usen la tabla `users`.
        try {
            DB::statement("CREATE OR REPLACE VIEW `users` AS SELECT * FROM `usuarios`");
        } catch (\Exception $e) {
            // Registrar el error para diagnóstico si la creación de la vista falla
            logger()->error('Error creando view users -> usuarios: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement("DROP VIEW IF EXISTS `users`");
        } catch (\Exception $e) {
            logger()->error('Error eliminando view users: ' . $e->getMessage());
        }
    }
};
