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
        // This migration reconciles environments where a physical `users` table
        // exists alongside the canonical `usuarios` table. It attempts a safe
        // non-destructive resolution:
        // - If `users` exists and `usuarios` does not: rename `users` -> `usuarios`.
        // - If both exist: do nothing and create a view `users_compat` pointing to
        //   `usuarios` for compatibility, and log a warning for manual intervention.

        $schema = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE()");
        $tables = array_map(fn($r) => $r->table_name, $schema);

        $hasUsers = in_array('users', $tables);
        $hasUsuarios = in_array('usuarios', $tables);

        if ($hasUsers && ! $hasUsuarios) {
            // Safe rename: wrap in transaction if available
            try {
                DB::beginTransaction();
                DB::statement("RENAME TABLE `users` TO `usuarios`");
                logger()->info('Reconciled tables: renamed `users` -> `usuarios`.');
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                logger()->error('Failed to rename `users` to `usuarios`: ' . $e->getMessage());
            }
            return;
        }

        if ($hasUsers && $hasUsuarios) {
            // Both exist: create a compatibility view only if it doesn't already exist.
            try {
                $exists = DB::select("SELECT COUNT(*) as c FROM information_schema.views WHERE table_schema = DATABASE() AND table_name = 'users_compat'");
                $c = intval($exists[0]->c ?? 0);
                if ($c === 0) {
                    DB::statement("CREATE OR REPLACE VIEW `users_compat` AS SELECT * FROM `usuarios`");
                    logger()->warning('Both `users` and `usuarios` tables exist. Created view `users_compat` for compatibility. Manual review recommended.');
                }
            } catch (\Exception $e) {
                logger()->error('Error creating users_compat view: ' . $e->getMessage());
            }
            return;
        }

        // If neither exists, nothing to do.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement("DROP VIEW IF EXISTS `users_compat`");
        } catch (\Exception $e) {
            logger()->error('Error dropping users_compat view: ' . $e->getMessage());
        }
    }
};
