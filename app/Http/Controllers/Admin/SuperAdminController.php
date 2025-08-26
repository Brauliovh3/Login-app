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

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('actas')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        DB::statement('ALTER TABLE actas AUTO_INCREMENT = 1');

        return response()->json(['ok' => true, 'message' => 'Actas truncated and AUTO_INCREMENT reset.']);
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
}
