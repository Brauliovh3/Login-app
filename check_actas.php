<?php
require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Verificar tabla actas
    $actasCount = DB::table('actas')->count();
    echo "Total de actas en la base de datos: " . $actasCount . PHP_EOL;
    
    // Mostrar últimas actas
    $recent = DB::table('actas')
        ->select('id', 'numero_acta', 'fecha_acta', 'numero_documento', 'nombre_conductor', 'placa_vehiculo', 'created_at')
        ->orderBy('id', 'desc')
        ->limit(5)
        ->get();
        
    echo "\nÚltimas actas registradas:" . PHP_EOL;
    foreach ($recent as $acta) {
        echo sprintf("ID: %d | Acta: %s | Fecha: %s | Doc: %s | Conductor: %s | Placa: %s" . PHP_EOL,
            $acta->id, 
            $acta->numero_acta ?? 'N/A', 
            $acta->fecha_acta ?? 'N/A',
            $acta->numero_documento ?? 'N/A', 
            $acta->nombre_conductor ?? 'N/A', 
            $acta->placa_vehiculo ?? 'N/A'
        );
    }
    
    // Verificar estructura de tabla
    echo "\nEstructura de la tabla actas:" . PHP_EOL;
    $columns = DB::select("DESCRIBE actas");
    foreach ($columns as $column) {
        echo sprintf("%-25s %-15s %-10s" . PHP_EOL, 
            $column->Field, $column->Type, $column->Null);
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
?>
