<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== VERIFICANDO ESTADO DE LA BASE DE DATOS ===\n\n";

try {
    echo "1. Tablas en la base de datos:\n";
    $tables = DB::select('SHOW TABLES');
    foreach ($tables as $table) {
        $tableName = array_values((array) $table)[0];
        echo "   - $tableName\n";
    }
    
    echo "\n2. Verificando tabla 'actas':\n";
    $actasCount = DB::table('actas')->count();
    echo "   Total actas: $actasCount\n";
    
    if ($actasCount > 0) {
        $ultimaActa = DB::table('actas')->latest('created_at')->first();
        echo "   Última acta: {$ultimaActa->numero_acta}\n";
        echo "   Fecha: {$ultimaActa->created_at}\n";
    }
    
    echo "\n3. Verificando tabla 'notifications':\n";
    try {
        $notificationsCount = DB::table('notifications')->count();
        echo "   Total notificaciones: $notificationsCount\n";
    } catch (Exception $e) {
        echo "   ERROR: Tabla 'notifications' no existe - " . $e->getMessage() . "\n";
    }
    
    echo "\n4. Verificando estructura de tabla actas:\n";
    $columns = DB::select('DESCRIBE actas');
    foreach ($columns as $column) {
        echo "   - {$column->Field} ({$column->Type})\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DE VERIFICACIÓN ===\n";
