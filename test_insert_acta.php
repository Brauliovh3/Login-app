<?php
require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simular datos del formulario
$testData = [
    'numero_acta' => '000452',
    'placa_1' => 'TEST-001',
    'nombre_conductor_1' => 'Juan Carlos Pérez López',
    'licencia_conductor_1' => 'L123456789',
    'razon_social' => 'Transportes Test S.A.C.',
    'ruc_dni' => '20123456789',
    'lugar_intervencion' => 'Abancay, Provincia Abancay',
    'origen_viaje' => 'Abancay',
    'destino_viaje' => 'Cusco',
    'tipo_servicio' => 'Interprovincial',
    'descripcion_hechos' => 'Prueba de registro de acta desde formulario corregido',
    'monto_multa' => 500.00,
    'observaciones_inspector' => 'Acta de prueba después de correcciones'
];

try {
    // Usar la misma lógica que el controlador
    $horaActual = Carbon\Carbon::now();
    
    // Generar número de acta único
    $numeroActa = 'DRTC-APU-' . date('Y') . '-' . str_pad(DB::table('actas')->count() + 1, 3, '0', STR_PAD_LEFT);

    // Preparar descripción completa
    $descripcionCompleta = "ACTA DE FISCALIZACIÓN\n\n";
    $descripcionCompleta .= "DATOS DEL VEHÍCULO:\n";
    $descripcionCompleta .= "Placa: " . $testData['placa_1'] . "\n";
    $descripcionCompleta .= "Empresa/Operador: " . $testData['razon_social'] . "\n";
    $descripcionCompleta .= "RUC/DNI: " . $testData['ruc_dni'] . "\n\n";
    
    $descripcionCompleta .= "DATOS DEL CONDUCTOR:\n";
    $descripcionCompleta .= "Nombre: " . $testData['nombre_conductor_1'] . "\n";
    $descripcionCompleta .= "Licencia: " . $testData['licencia_conductor_1'] . "\n\n";
    
    $descripcionCompleta .= "DATOS DEL VIAJE:\n";
    $descripcionCompleta .= "Origen: " . $testData['origen_viaje'] . "\n";
    $descripcionCompleta .= "Destino: " . $testData['destino_viaje'] . "\n";
    $descripcionCompleta .= "Tipo de Servicio: " . $testData['tipo_servicio'] . "\n\n";
    
    $descripcionCompleta .= "DESCRIPCIÓN DE LOS HECHOS:\n";
    $descripcionCompleta .= $testData['descripcion_hechos'];

    $actaId = DB::table('actas')->insertGetId([
        'numero_acta' => $numeroActa,
        'vehiculo_id' => null,
        'conductor_id' => null,
        'infraccion_id' => 1,
        'inspector_id' => 1,
        'placa_vehiculo' => $testData['placa_1'],
        'ubicacion' => $testData['lugar_intervencion'],
        'descripcion' => $descripcionCompleta,
        'monto_multa' => $testData['monto_multa'],
        'estado' => 'registrada',
        'fecha_infraccion' => $horaActual->toDateString(),
        'hora_infraccion' => $horaActual->toTimeString(),
        'hora_inicio_registro' => $horaActual->toDateTimeString(),
        'observaciones' => $testData['observaciones_inspector'],
        'user_id' => 1,
        'created_at' => $horaActual->toDateTimeString(),
        'updated_at' => $horaActual->toDateTimeString(),
    ]);

    echo "✅ Acta insertada exitosamente!" . PHP_EOL;
    echo "ID del acta: " . $actaId . PHP_EOL;
    echo "Número de acta: " . $numeroActa . PHP_EOL;
    echo "Hora de registro: " . $horaActual->format('d/m/Y H:i:s') . PHP_EOL;
    
    // Verificar la inserción
    $acta = DB::table('actas')->where('id', $actaId)->first();
    echo "\nDatos insertados:" . PHP_EOL;
    echo "Placa: " . $acta->placa_vehiculo . PHP_EOL;
    echo "Ubicación: " . $acta->ubicacion . PHP_EOL;
    echo "Estado: " . $acta->estado . PHP_EOL;
    echo "Fecha: " . $acta->fecha_infraccion . PHP_EOL;
    echo "Hora: " . $acta->hora_infraccion . PHP_EOL;
    
    // Total de actas
    $total = DB::table('actas')->count();
    echo "\nTotal de actas en la base de datos: " . $total . PHP_EOL;
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . PHP_EOL;
}
?>
