<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Intentamos eliminar cualquier vista o tabla llamada `users` que
        // esté duplicando la tabla canónica `usuarios`.
        try {
            // Eliminar vistas de compatibilidad si existen
            DB::statement("DROP VIEW IF EXISTS `users`");
            DB::statement("DROP VIEW IF EXISTS `users_compat`");

            // Si existe una tabla física `users`, eliminarla (solo si existe).
            if (Schema::hasTable('users')) {
                Schema::dropIfExists('users');
                logger()->info('Migración: tabla física `users` eliminada porque duplicaba `usuarios`.');
            } else {
                logger()->info('Migración: no se encontró tabla física `users`; vistas `users`/`users_compat` eliminadas si existían.');
            }
        } catch (\Exception $e) {
            logger()->error('Error al eliminar views/table users: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentamos restaurar una vista `users` que apunte a `usuarios` si
        // la tabla `usuarios` existe y no existe una tabla física `users`.
        try {
            $hasUsuarios = Schema::hasTable('usuarios');
            $hasUsersTable = Schema::hasTable('users');
            if ($hasUsuarios && ! $hasUsersTable) {
                DB::statement("CREATE OR REPLACE VIEW `users` AS SELECT * FROM `usuarios`");
                logger()->info('Rollback: recreada vista `users` apuntando a `usuarios`.');
            } else {
                logger()->info('Rollback: no se recreó la vista `users` (tabla `usuarios` ausente o tabla `users` física presente).');
            }
        } catch (\Exception $e) {
            logger()->error('Error rollback drop users view/table: ' . $e->getMessage());
        }
    }
};
