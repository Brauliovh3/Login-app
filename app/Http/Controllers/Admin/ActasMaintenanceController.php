<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActasMaintenanceController extends Controller
{
    /**
     * Reset AUTO_INCREMENT for actas table.
     * - If table is empty: sets AUTO_INCREMENT = 1
     * - If table is not empty: requires force=true to TRUNCATE (destructive)
     */
    public function resetAutoIncrement(Request $request)
    {
        // Only allow admins (route already protected, but double-check)
        if (! auth()->check() || auth()->user()->role !== 'administrador') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $force = $request->boolean('force', false);

        // Count rows
        $count = DB::table('actas')->count();

        if ($count === 0) {
            // Safe: table empty, set next AUTO_INCREMENT to 1
            $table = DB::getTablePrefix() . 'actas';
            DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = 1");
            return response()->json(['ok' => true, 'message' => 'AUTO_INCREMENT restablecido a 1 (tabla vacía).']);
        }

        if (! $force) {
            return response()->json(['ok' => false, 'message' => 'La tabla contiene registros. Envíe {force:true} para truncar (operación destructiva).', 'rows' => $count], 400);
        }

        // Force: destructive reset. Use DELETE (respects FK constraints) then reset AUTO_INCREMENT.
        try {
            DB::beginTransaction();
            DB::table('actas')->delete();
            DB::commit();

            $table = DB::getTablePrefix() . 'actas';
            DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = 1");
            return response()->json(['ok' => true, 'message' => 'Tabla actas eliminada y AUTO_INCREMENT restablecido.']);
        } catch (\Exception $e) {
            try {
                if (DB::getPdo() && DB::getPdo()->inTransaction()) {
                    DB::rollBack();
                }
            } catch (\Throwable $__t) {
                // ignore
            }
            return response()->json(['ok' => false, 'message' => 'Error al reiniciar: ' . $e->getMessage()], 500);
        }
    }
}
