<?php
require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Verificar si existe la tabla notifications
    $exists = DB::select("SHOW TABLES LIKE 'notifications'");
    
    if (count($exists) > 0) {
        echo "✅ La tabla notifications existe" . PHP_EOL;
        
        // Mostrar estructura
        echo "\nEstructura de la tabla notifications:" . PHP_EOL;
        $columns = DB::select("DESCRIBE notifications");
        foreach ($columns as $column) {
            printf("%-20s %-15s %-10s" . PHP_EOL, 
                $column->Field, $column->Type, $column->Null);
        }
        
        // Contar registros
        $count = DB::table('notifications')->count();
        echo "\nTotal de notificaciones: " . $count . PHP_EOL;
        
    } else {
        echo "❌ La tabla notifications NO existe" . PHP_EOL;
        echo "Necesitas ejecutar la migración para crearla." . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
?>
