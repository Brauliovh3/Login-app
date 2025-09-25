<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class SuperAdminHelper
{
    /**
     * Reiniciar contadores de AUTO_INCREMENT de todas las tablas importantes
     */
    public static function resetAllAutoIncrements($force = false)
    {
        $results = [];
        $tables = ['actas', 'usuarios', 'inspectores', 'vehiculos', 'conductores'];
        
        foreach ($tables as $table) {
            try {
                $count = DB::table($table)->count();
                
                if ($count === 0 || $force) {
                    $maxId = DB::table($table)->max('id') ?: 0;
                    $nextId = $maxId + 1;
                    
                    DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = {$nextId}");
                    
                    $results[$table] = [
                        'success' => true,
                        'message' => "AUTO_INCREMENT reseteado a {$nextId}",
                        'next_id' => $nextId
                    ];
                    
                    Log::info("SuperAdmin reset AUTO_INCREMENT for {$table}", [
                        'table' => $table,
                        'next_id' => $nextId,
                        'user' => auth()->user()->id ?? 'system'
                    ]);
                } else {
                    $results[$table] = [
                        'success' => false,
                        'message' => "Tabla no vacía ({$count} registros). Use force=true para forzar.",
                        'count' => $count
                    ];
                }
            } catch (Exception $e) {
                $results[$table] = [
                    'success' => false,
                    'message' => "Error: " . $e->getMessage()
                ];
                
                Log::error("Error resetting AUTO_INCREMENT for {$table}", [
                    'error' => $e->getMessage(),
                    'user' => auth()->user()->id ?? 'system'
                ]);
            }
        }
        
        return $results;
    }
    
    /**
     * Obtener logs del sistema con filtros
     */
    public static function getSystemLogs($type = 'all', $limit = 100)
    {
        $logPath = storage_path('logs/laravel.log');
        
        if (!file_exists($logPath)) {
            return ['error' => 'Archivo de log no encontrado'];
        }
        
        try {
            $content = file_get_contents($logPath);
            $lines = explode("\n", $content);
            $lines = array_reverse($lines); // Más recientes primero
            
            $filtered = [];
            $count = 0;
            
            foreach ($lines as $line) {
                if ($count >= $limit) break;
                
                if (empty(trim($line))) continue;
                
                // Filtrar por tipo si se especifica
                if ($type !== 'all') {
                    $typeUpper = strtoupper($type);
                    if (strpos($line, ".{$typeUpper}:") === false) {
                        continue;
                    }
                }
                
                $filtered[] = $line;
                $count++;
            }
            
            return [
                'success' => true,
                'logs' => $filtered,
                'total_lines' => count($lines),
                'shown' => count($filtered)
            ];
        } catch (Exception $e) {
            return [
                'error' => 'Error leyendo logs: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtener información completa del sistema
     */
    public static function getSystemInfo()
    {
        return [
            'php' => [
                'version' => PHP_VERSION,
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size')
            ],
            'laravel' => [
                'version' => app()->version(),
                'environment' => app()->environment(),
                'debug' => config('app.debug'),
                'timezone' => config('app.timezone'),
                'url' => config('app.url')
            ],
            'database' => [
                'connection' => config('database.default'),
                'host' => config('database.connections.' . config('database.default') . '.host'),
                'database' => config('database.connections.' . config('database.default') . '.database'),
                'port' => config('database.connections.' . config('database.default') . '.port')
            ],
            'server' => [
                'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'php_sapi' => PHP_SAPI,
                'os' => PHP_OS,
                'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'
            ],
            'storage' => [
                'disk_free_space' => self::formatBytes(disk_free_space('.')),
                'disk_total_space' => self::formatBytes(disk_total_space('.')),
                'app_size' => self::formatBytes(self::getDirectorySize(base_path()))
            ]
        ];
    }
    
    /**
     * Obtener tamaño de directorio recursivamente
     */
    private static function getDirectorySize($directory)
    {
        $size = 0;
        
        if (is_dir($directory)) {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory)) as $file) {
                $size += $file->getSize();
            }
        }
        
        return $size;
    }
    
    /**
     * Formatear bytes a formato legible
     */
    private static function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Limpiar archivos temporales del sistema
     */
    public static function cleanupTempFiles()
    {
        $results = [];
        
        // Limpiar caché de Laravel
        try {
            Cache::flush();
            $results['cache'] = ['success' => true, 'message' => 'Caché de Laravel limpiado'];
        } catch (Exception $e) {
            $results['cache'] = ['success' => false, 'message' => $e->getMessage()];
        }
        
        // Limpiar archivos temporales de consultas
        $tempDir = base_path('temp_consultas');
        if (is_dir($tempDir)) {
            try {
                $files = glob($tempDir . '/*');
                $deleted = 0;
                
                foreach ($files as $file) {
                    if (is_file($file) && filemtime($file) < strtotime('-1 day')) {
                        unlink($file);
                        $deleted++;
                    }
                }
                
                $results['temp_files'] = [
                    'success' => true,
                    'message' => "Eliminados {$deleted} archivos temporales"
                ];
            } catch (Exception $e) {
                $results['temp_files'] = [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }
        
        // Limpiar logs antiguos (mantener solo últimos 7 días)
        $logDir = storage_path('logs');
        try {
            $logs = glob($logDir . '/laravel-*.log');
            $deleted = 0;
            
            foreach ($logs as $log) {
                if (filemtime($log) < strtotime('-7 days')) {
                    unlink($log);
                    $deleted++;
                }
            }
            
            $results['old_logs'] = [
                'success' => true,
                'message' => "Eliminados {$deleted} logs antiguos"
            ];
        } catch (Exception $e) {
            $results['old_logs'] = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        
        return $results;
    }
    
    /**
     * Generar reporte de actividad del sistema
     */
    public static function generateActivityReport()
    {
        try {
            $report = [
                'generated_at' => now()->toDateTimeString(),
                'stats' => [
                    'total_users' => DB::table('usuarios')->count(),
                    'active_users' => DB::table('usuarios')->where('status', 'approved')->count(),
                    'pending_users' => DB::table('usuarios')->where('status', 'pending')->count(),
                    'total_actas' => DB::table('actas')->count(),
                    'actas_today' => DB::table('actas')->whereDate('created_at', today())->count(),
                    'actas_this_week' => DB::table('actas')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                    'actas_this_month' => DB::table('actas')->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count()
                ],
                'users_by_role' => DB::table('usuarios')
                    ->select('role', DB::raw('count(*) as count'))
                    ->groupBy('role')
                    ->get()
                    ->pluck('count', 'role')
                    ->toArray(),
                'actas_by_status' => DB::table('actas')
                    ->select('estado', DB::raw('count(*) as count'))
                    ->groupBy('estado')
                    ->get()
                    ->pluck('count', 'estado')
                    ->toArray(),
                'recent_activity' => [
                    'new_users' => DB::table('usuarios')
                        ->select('username', 'created_at', 'role')
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get()
                        ->toArray(),
                    'recent_actas' => DB::table('actas')
                        ->select('numero_acta', 'placa', 'created_at', 'estado')
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get()
                        ->toArray()
                ]
            ];
            
            return ['success' => true, 'report' => $report];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Error generando reporte: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Verificar salud del sistema
     */
    public static function checkSystemHealth()
    {
        $checks = [];
        
        // Verificar conexión a base de datos
        try {
            DB::connection()->getPdo();
            $checks['database'] = ['status' => 'ok', 'message' => 'Conexión exitosa'];
        } catch (Exception $e) {
            $checks['database'] = ['status' => 'error', 'message' => $e->getMessage()];
        }
        
        // Verificar directorio de escritura
        $storageWritable = is_writable(storage_path());
        $checks['storage'] = [
            'status' => $storageWritable ? 'ok' : 'error',
            'message' => $storageWritable ? 'Directorio escribible' : 'Sin permisos de escritura'
        ];
        
        // Verificar espacio en disco (alerta si menos de 1GB libre)
        $freeSpace = disk_free_space('.');
        $checks['disk_space'] = [
            'status' => $freeSpace > 1073741824 ? 'ok' : 'warning', // 1GB
            'message' => 'Espacio libre: ' . self::formatBytes($freeSpace)
        ];
        
        // Verificar configuración de PHP
        $memoryLimit = ini_get('memory_limit');
        $checks['php_memory'] = [
            'status' => 'info',
            'message' => "Límite de memoria: {$memoryLimit}"
        ];
        
        return $checks;
    }
}