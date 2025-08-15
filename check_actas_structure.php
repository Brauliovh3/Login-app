<?php
require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Estructura real de la tabla actas:" . PHP_EOL;
    $columns = DB::select("DESCRIBE actas");
    foreach ($columns as $column) {
        echo sprintf("%-30s %-20s %-10s %-10s" . PHP_EOL, 
            $column->Field, $column->Type, $column->Null, $column->Key);
    }
    
    echo "\nPrimeras 3 actas (todas las columnas):" . PHP_EOL;
    $actas = DB::table('actas')->limit(3)->get();
    foreach ($actas as $acta) {
        echo "ID: " . $acta->id . PHP_EOL;
        foreach ($acta as $key => $value) {
            echo "  $key: " . ($value ?? 'NULL') . PHP_EOL;
        }
        echo "---" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
?>
