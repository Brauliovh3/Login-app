<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SuperAdminController extends Controller
{
    public function index()
    {
        // View is hidden; only accessible by role:superadmin
        return view('administrador.super.index');
    }

    public function cacheClear(Request $request)
    {
        if (! auth()->check() || auth()->user()->role !== 'superadmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        return response()->json(['ok' => true, 'message' => 'Cache cleared.']);
    }

    public function configCache(Request $request)
    {
        if (! auth()->check() || auth()->user()->role !== 'superadmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        Artisan::call('config:cache');
        return response()->json(['ok' => true, 'message' => 'Config cached.']);
    }

    public function resetActas(Request $request)
    {
        if (! auth()->check() || auth()->user()->role !== 'superadmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $force = $request->boolean('force', false);
        $count = DB::table('actas')->count();

        if ($count === 0) {
            DB::statement('ALTER TABLE actas AUTO_INCREMENT = 1');
            return response()->json(['ok' => true, 'message' => 'AUTO_INCREMENT reset to 1 (table empty).']);
        }

        if (! $force) {
            return response()->json(['ok' => false, 'message' => 'Table not empty. Set force=true to truncate. Rows: '.$count], 400);
        }

        // Use DELETE instead of TRUNCATE to respect foreign key constraints.
        try {
            DB::beginTransaction();
            // This will cascade deletes if FKs are defined with onDelete('cascade')
            DB::table('actas')->delete();
            DB::commit();

            // Run DDL outside the transaction to avoid implicit commit/transaction mismatch
            DB::statement('ALTER TABLE actas AUTO_INCREMENT = 1');
            Log::warning('Superadmin performed destructive reset of actas table (DELETE + reset AUTO_INCREMENT)', ['user' => auth()->user()->id]);
            return response()->json(['ok' => true, 'message' => 'Actas deleted and AUTO_INCREMENT reset.']);
        } catch (\Exception $e) {
            // Only attempt rollback if a transaction is active
            try {
                if (DB::getPdo() && DB::getPdo()->inTransaction()) {
                    DB::rollBack();
                }
            } catch (\Throwable $__t) {
                // ignore rollback errors
            }

            Log::error('Failed to reset actas auto-increment', ['err' => $e->getMessage(), 'user' => auth()->user()->id]);
            return response()->json(['ok' => false, 'message' => 'Failed to truncate actas: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Return minimal app/server info for the superadmin panel.
     */
    public function appInfo()
    {
        if (! auth()->check() || auth()->user()->role !== 'superadmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $info = [
            'app_env' => env('APP_ENV'),
            'app_url' => env('APP_URL'),
            'php_version' => phpversion(),
            'db_connection' => config('database.default'),
            'db_database' => env('DB_DATABASE'),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? null,
        ];

        return response()->json(['ok' => true, 'info' => $info]);
    }

    /**
     * Return simple stats: counts for actas and usuarios.
     */
    public function stats()
    {
        if (! auth()->check() || auth()->user()->role !== 'superadmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = [
            'total_actas' => DB::table('actas')->count(),
            'total_usuarios' => DB::table('usuarios')->count(),
            'actas_recientes' => DB::table('actas')->latest('created_at')->take(5)->get(['id','numero_acta','created_at'])
        ];

        return response()->json(['ok' => true, 'stats' => $data]);
    }

    /**
     * Return a paginated users list for management.
     */
    public function usersList(Request $request)
    {
        if (! auth()->check() || auth()->user()->role !== 'superadmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $users = DB::table('usuarios')->select('id','username','email','role','status','created_at')->orderBy('id','desc')->limit(50)->get();
        return response()->json(['ok' => true, 'users' => $users]);
    }

    public function toggleUserStatus(Request $request, $id)
    {
        if (! auth()->check() || auth()->user()->role !== 'superadmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = DB::table('usuarios')->where('id', $id)->first();
        if (! $user) return response()->json(['ok'=>false,'message'=>'Usuario no encontrado'],404);

        $new = ($user->status === 'active') ? 'blocked' : 'active';
        DB::table('usuarios')->where('id', $id)->update(['status' => $new, 'updated_at' => now()]);
        return response()->json(['ok' => true, 'message' => 'Status actualizado', 'status' => $new]);
    }

    public function approveUser(Request $request, $id)
    {
        if (! auth()->check() || auth()->user()->role !== 'superadmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = DB::table('usuarios')->where('id', $id)->first();
        if (! $user) return response()->json(['ok'=>false,'message'=>'Usuario no encontrado'],404);

        DB::table('usuarios')->where('id', $id)->update(['status' => 'approved', 'updated_at' => now()]);
        return response()->json(['ok' => true, 'message' => 'Usuario aprobado']);
    }

    /**
     * Run a whitelisted artisan command requested from the superadmin panel.
     * Accepts JSON: { command: 'cache:clear' }
     */
    public function runCommand(Request $request)
    {
        if (! auth()->check() || auth()->user()->role !== 'superadmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $allowed = [
            'cache:clear',
            'config:clear',
            'config:cache',
            'route:clear',
            'view:clear'
        ];

        $cmd = $request->input('command');
        if (! in_array($cmd, $allowed)) {
            return response()->json(['ok' => false, 'message' => 'Comando no permitido.'], 400);
        }

        try {
            Artisan::call($cmd);
            $output = trim(Artisan::output());
            Log::info("Superadmin ran command: {$cmd}", ['user' => auth()->user()->id]);
            return response()->json(['ok' => true, 'command' => $cmd, 'output' => $output]);
        } catch (\Exception $e) {
            Log::error('Error running artisan command', ['cmd' => $cmd, 'err' => $e->getMessage(), 'user' => auth()->user()->id]);
            return response()->json(['ok' => false, 'message' => 'Error ejecutando comando.'], 500);
        }
    }

    /**
     * Get comprehensive actas management data
     */
    public function actasManagement(Request $request)
    {
        if (! auth()->check() || auth()->user()->role !== 'superadmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $limit = min($request->input('limit', 50), 200);
        $offset = max($request->input('offset', 0), 0);
        
        $actas = DB::table('actas')
            ->select('id', 'numero_acta', 'placa', 'razon_social', 'estado', 'created_at')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        $stats = [
            'total' => DB::table('actas')->count(),
            'pendientes' => DB::table('actas')->where('estado', 'pendiente')->count(),
            'procesadas' => DB::table('actas')->where('estado', 'procesada')->count(),
            'anuladas' => DB::table('actas')->where('estado', 'anulada')->count(),
        ];

        return response()->json(['ok' => true, 'actas' => $actas, 'stats' => $stats]);
    }

    /**
     * Delete acta by ID
     */
    public function deleteActa(Request $request, $id)
    {
        if (! auth()->check() || auth()->user()->role !== 'superadmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $acta = DB::table('actas')->where('id', $id)->first();
        if (! $acta) {
            return response()->json(['ok' => false, 'message' => 'Acta no encontrada'], 404);
        }

        DB::table('actas')->where('id', $id)->delete();
        Log::warning("Superadmin deleted acta ID: {$id}", ['user' => auth()->user()->id, 'acta' => $acta]);
        
        return response()->json(['ok' => true, 'message' => 'Acta eliminada']);
    }

    /**
     * Update acta status
     */
    public function updateActaStatus(Request $request, $id)
    {
        if (! auth()->check() || auth()->user()->role !== 'superadmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $status = $request->input('status');
        $validStatuses = ['pendiente', 'procesada', 'anulada', 'pagada'];
        
        if (! in_array($status, $validStatuses)) {
            return response()->json(['ok' => false, 'message' => 'Estado inválido'], 400);
        }

        $updated = DB::table('actas')->where('id', $id)->update(['estado' => $status, 'updated_at' => now()]);
        
        if (! $updated) {
            return response()->json(['ok' => false, 'message' => 'Acta no encontrada'], 404);
        }

        Log::info("Superadmin updated acta {$id} status to {$status}", ['user' => auth()->user()->id]);
        
        return response()->json(['ok' => true, 'message' => 'Estado actualizado']);
    }

    /**
     * Delete user by ID
     */
    public function deleteUser(Request $request, $id)
    {
        if (! auth()->check() || auth()->user()->role !== 'superadmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = DB::table('usuarios')->where('id', $id)->first();
        if (! $user) {
            return response()->json(['ok' => false, 'message' => 'Usuario no encontrado'], 404);
        }

        // Prevent self-deletion
        if ($user->id == auth()->user()->id) {
            return response()->json(['ok' => false, 'message' => 'No puedes eliminarte a ti mismo'], 400);
        }

        DB::table('usuarios')->where('id', $id)->delete();
        Log::warning("Superadmin deleted user ID: {$id}", ['user' => auth()->user()->id, 'deleted_user' => $user]);
        
        return response()->json(['ok' => true, 'message' => 'Usuario eliminado']);
    }

    /**
     * Update user role
     */
    public function updateUserRole(Request $request, $id)
    {
        if (! auth()->check() || auth()->user()->role !== 'superadmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $role = $request->input('role');
        $validRoles = ['administrador', 'fiscalizador', 'ventanilla', 'superadmin'];
        
        if (! in_array($role, $validRoles)) {
            return response()->json(['ok' => false, 'message' => 'Rol inválido'], 400);
        }

        $user = DB::table('usuarios')->where('id', $id)->first();
        if (! $user) {
            return response()->json(['ok' => false, 'message' => 'Usuario no encontrado'], 404);
        }

        // Prevent changing own role
        if ($user->id == auth()->user()->id) {
            return response()->json(['ok' => false, 'message' => 'No puedes cambiar tu propio rol'], 400);
        }

        DB::table('usuarios')->where('id', $id)->update(['role' => $role, 'updated_at' => now()]);
        Log::info("Superadmin updated user {$id} role to {$role}", ['user' => auth()->user()->id]);
        
        return response()->json(['ok' => true, 'message' => 'Rol actualizado']);
    }

    /**
     * System maintenance operations
     */
    public function systemMaintenance(Request $request)
    {
        if (! auth()->check() || auth()->user()->role !== 'superadmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $action = $request->input('action');
        
        switch ($action) {
            case 'optimize':
                Artisan::call('optimize');
                $output = Artisan::output();
                Log::info("Superadmin ran optimize", ['user' => auth()->user()->id]);
                return response()->json(['ok' => true, 'message' => 'Sistema optimizado', 'output' => $output]);
                
            case 'migrate':
                Artisan::call('migrate', ['--force' => true]);
                $output = Artisan::output();
                Log::warning("Superadmin ran migrate", ['user' => auth()->user()->id]);
                return response()->json(['ok' => true, 'message' => 'Migraciones ejecutadas', 'output' => $output]);
                
            case 'storage_link':
                Artisan::call('storage:link');
                $output = Artisan::output();
                Log::info("Superadmin ran storage:link", ['user' => auth()->user()->id]);
                return response()->json(['ok' => true, 'message' => 'Storage link creado', 'output' => $output]);
                
            default:
                return response()->json(['ok' => false, 'message' => 'Acción no válida'], 400);
        }
    }

    /**
     * Database maintenance operations
     */
    public function databaseMaintenance(Request $request)
    {
        if (! auth()->check() || auth()->user()->role !== 'superadmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $action = $request->input('action');
        
        switch ($action) {
            case 'vacuum':
                // MySQL doesn't have VACUUM, but we can optimize tables
                $tables = ['actas', 'usuarios', 'notifications', 'sesiones'];
                foreach ($tables as $table) {
                    try {
                        DB::statement("OPTIMIZE TABLE {$table}");
                    } catch (\Exception $e) {
                        Log::warning("Failed to optimize table {$table}: " . $e->getMessage());
                    }
                }
                Log::info("Superadmin optimized database tables", ['user' => auth()->user()->id]);
                return response()->json(['ok' => true, 'message' => 'Tablas optimizadas']);
                
            case 'cleanup_sessions':
                $deleted = DB::table('sesiones')->where('last_activity', '<', now()->subDays(30)->timestamp)->delete();
                Log::info("Superadmin cleaned up {$deleted} old sessions", ['user' => auth()->user()->id]);
                return response()->json(['ok' => true, 'message' => "Eliminadas {$deleted} sesiones antiguas"]);
                
            case 'cleanup_notifications':
                $deleted = DB::table('notifications')->whereNotNull('read_at')->where('read_at', '<', now()->subDays(7))->delete();
                Log::info("Superadmin cleaned up {$deleted} old notifications", ['user' => auth()->user()->id]);
                return response()->json(['ok' => true, 'message' => "Eliminadas {$deleted} notificaciones antiguas"]);
                
            default:
                return response()->json(['ok' => false, 'message' => 'Acción no válida'], 400);
        }
    }
}
