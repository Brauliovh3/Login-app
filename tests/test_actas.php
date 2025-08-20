<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== PROBANDO GUARDADO DE ACTA ===\n\n";

try {
    // Datos de prueba para una nueva acta
    $datosActa = [
    'numero_acta' => 'TEST-' . date('YmdHis'),
    'inspector_id' => 1,
    'vehiculo_id' => 1,
    'conductor_id' => 1,
    'infraccion_id' => 1,
    'placa_vehiculo' => 'TEST-123',
    'ubicacion' => 'Prueba de ubicación',
    'descripcion_hechos' => 'ACTA DE PRUEBA - Descripción de prueba',
    'monto_multa' => 500.00,
    'estado' => 'registrada',
    'fecha_intervencion' => date('Y-m-d'),
    'hora_intervencion' => date('H:i:s'),
    'user_id' => 1,
    'created_at' => now(),
    'updated_at' => now(),
    ];
    
    echo "1. Insertando acta de prueba...\n";
    $actaId = DB::table('actas')->insertGetId($datosActa);
    echo "   ✅ Acta insertada con ID: $actaId\n";
    
    echo "\n2. Verificando total de actas:\n";
    $totalActas = DB::table('actas')->count();
    echo "   Total de actas en la base de datos: $totalActas\n";
    
    echo "\n3. Últimas 3 actas:\n";
    $ultimasActas = DB::table('actas')->latest('created_at')->take(3)->get(['id', 'numero_acta', 'created_at']);
    foreach ($ultimasActas as $acta) {
        echo "   - ID: {$acta->id} | Número: {$acta->numero_acta} | Fecha: {$acta->created_at}\n";
    }
    
    echo "\n4. Probando consulta por documento:\n";
    $actasRUC = DB::table('actas')
        ->where('descripcion', 'LIKE', '%20123456789%')
        ->get(['numero_acta', 'placa_vehiculo']);
    
    echo "   Actas encontradas con RUC 20123456789: " . $actasRUC->count() . "\n";
    foreach ($actasRUC as $acta) {
        echo "   - {$acta->numero_acta} | Placa: {$acta->placa_vehiculo}\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DE PRUEBA ===\n";
