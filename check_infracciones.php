<?php
require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Verificar si la tabla existe
    $tables = DB::select("SHOW TABLES LIKE 'infracciones'");
    echo "Tabla 'infracciones' existe: " . (count($tables) > 0 ? "SÍ" : "NO") . "\n";
    
    if (count($tables) > 0) {
        // Verificar columnas
        $columns = DB::select("SHOW COLUMNS FROM infracciones");
        echo "Columnas en tabla 'infracciones':\n";
        foreach ($columns as $col) {
            echo "  - {$col->Field} ({$col->Type})\n";
        }
        
        // Contar registros
        $total = DB::table('infracciones')->count();
        echo "\nTotal de registros: {$total}\n";
        
        if ($total > 0) {
            // Verificar campo estado
            $hasEstado = DB::select("SHOW COLUMNS FROM infracciones LIKE 'estado'");
            echo "Campo 'estado' existe: " . (count($hasEstado) > 0 ? "SÍ" : "NO") . "\n";
            
            if (count($hasEstado) > 0) {
                $activos = DB::table('infracciones')->where('estado', 'activo')->count();
                echo "Registros con estado='activo': {$activos}\n";
            }
            
            // Mostrar primeros 3 registros
            echo "\nPrimeros 3 registros:\n";
            $infracciones = DB::table('infracciones')->limit(3)->get();
            foreach ($infracciones as $inf) {
                echo "  - Código: {$inf->codigo_infraccion}, Estado: " . ($inf->estado ?? 'NULL') . ", Descripción: " . substr($inf->infraccion ?? 'NULL', 0, 50) . "...\n";
            }
        }
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
