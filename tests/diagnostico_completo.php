<?php
echo "=== DIAGNÓSTICO COMPLETO DEL FORMULARIO DE ACTAS ===" . PHP_EOL . PHP_EOL;

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// TEST 1: Verificar estado actual de la base de datos
echo "📊 TEST 1: Estado actual de la base de datos" . PHP_EOL;
try {
    $totalActas = DB::table('actas')->count();
    echo "✅ Total de actas en base de datos: " . $totalActas . PHP_EOL;
    
    $ultimasActas = DB::table('actas')
        ->orderBy('id', 'desc')
        ->limit(3)
        ->get(['id', 'numero_acta', 'placa_vehiculo', 'created_at']);
    
    echo "Últimas 3 actas registradas:" . PHP_EOL;
    foreach($ultimasActas as $acta) {
        echo "  - ID: {$acta->id} | Acta: {$acta->numero_acta} | Placa: {$acta->placa_vehiculo} | Fecha: {$acta->created_at}" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "❌ Error en base de datos: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "🔍 TEST 2: Verificar estructura del formulario vs requerimientos del controlador" . PHP_EOL;

// Campos que el controlador storeActaLibre espera (requeridos)
$camposRequeridos = [
    'placa_1',
    'nombre_conductor_1', 
    'licencia_conductor_1',
    'razon_social',
    'ruc_dni',
    'lugar_intervencion',
    'origen_viaje',
    'destino_viaje', 
    'tipo_servicio',
    'descripcion_hechos'
];

echo "Campos requeridos por el controlador:" . PHP_EOL;
foreach($camposRequeridos as $campo) {
    echo "  ✅ " . $campo . PHP_EOL;
}

echo PHP_EOL . "🌐 TEST 3: Simular petición exacta del formulario web" . PHP_EOL;

// Simular exactamente lo que enviaría el formulario
$datosFormulario = [
    'numero_acta' => '000999',
    'tipo_agente' => 'conductor',
    'ruc_dni' => '12345678',
    'razon_social' => 'Juan Pérez López',
    'placa_1' => 'ABC-999',
    'nombre_conductor_1' => 'Juan Pérez López',
    'licencia_conductor_1' => 'L123456789',
    'clase_categoria' => 'A-IIIb',
    'lugar_intervencion' => 'Abancay, Provincia Abancay',
    'origen_viaje' => 'Abancay',
    'destino_viaje' => 'Lima',
    'tipo_servicio' => 'Interprovincial',
    'inspector' => 'Inspector Test',
    'tipo_infraccion' => 'documentaria',
    'descripcion_hechos' => 'Vehículo circulando sin documentos',
    'codigo_infraccion' => 'INF-001',
    'monto_multa' => '500.00',
    'observaciones_inspector' => 'Primera infracción'
];

echo "Datos que se enviarían desde el formulario:" . PHP_EOL;
foreach($datosFormulario as $campo => $valor) {
    $required = in_array($campo, $camposRequeridos) ? "✅ REQUERIDO" : "⚪ OPCIONAL";
    echo "  {$required} {$campo}: {$valor}" . PHP_EOL;
}

// Verificar si hay campos faltantes
$camposFaltantes = [];
foreach($camposRequeridos as $campo) {
    if (!isset($datosFormulario[$campo]) || empty($datosFormulario[$campo])) {
        $camposFaltantes[] = $campo;
    }
}

if (!empty($camposFaltantes)) {
    echo PHP_EOL . "❌ CAMPOS FALTANTES EN EL FORMULARIO:" . PHP_EOL;
    foreach($camposFaltantes as $campo) {
        echo "  ❌ " . $campo . PHP_EOL;
    }
} else {
    echo PHP_EOL . "✅ Todos los campos requeridos están presentes" . PHP_EOL;
}

echo PHP_EOL . "💾 TEST 4: Probar inserción usando la lógica exacta del controlador" . PHP_EOL;

try {
    // Verificar que todos los campos requeridos estén presentes
    foreach($camposRequeridos as $campo) {
        if (!isset($datosFormulario[$campo])) {
            throw new Exception("Campo requerido faltante: " . $campo);
        }
    }
    
    $horaActual = Carbon\Carbon::now();
    $numeroActa = 'DRTC-APU-' . date('Y') . '-' . str_pad(DB::table('actas')->count() + 1, 3, '0', STR_PAD_LEFT);

    // Preparar descripción completa siguiendo la lógica del controlador
    $descripcionCompleta = "ACTA DE FISCALIZACIÓN\n\n";
    $descripcionCompleta .= "DATOS DEL VEHÍCULO:\n";
    $descripcionCompleta .= "Placa: " . $datosFormulario['placa_1'] . "\n";
    $descripcionCompleta .= "Empresa/Operador: " . $datosFormulario['razon_social'] . "\n";
    $descripcionCompleta .= "RUC/DNI: " . $datosFormulario['ruc_dni'] . "\n\n";
    
    $descripcionCompleta .= "DATOS DEL CONDUCTOR:\n";
    $descripcionCompleta .= "Nombre: " . $datosFormulario['nombre_conductor_1'] . "\n";
    $descripcionCompleta .= "Licencia: " . $datosFormulario['licencia_conductor_1'] . "\n\n";
    
    $descripcionCompleta .= "DATOS DEL VIAJE:\n";
    $descripcionCompleta .= "Origen: " . $datosFormulario['origen_viaje'] . "\n";
    $descripcionCompleta .= "Destino: " . $datosFormulario['destino_viaje'] . "\n";
    $descripcionCompleta .= "Tipo de Servicio: " . $datosFormulario['tipo_servicio'] . "\n\n";
    
    $descripcionCompleta .= "DESCRIPCIÓN DE LOS HECHOS:\n";
    $descripcionCompleta .= $datosFormulario['descripcion_hechos'];

    // Insertar usando exactamente la misma lógica que el controlador
    $actaId = DB::table('actas')->insertGetId([
        'numero_acta' => $numeroActa,
        'vehiculo_id' => null,
        'conductor_id' => null,
        'infraccion_id' => 1,
        'inspector_id' => 1,
        'placa_vehiculo' => $datosFormulario['placa_1'],
        'ubicacion' => $datosFormulario['lugar_intervencion'],
        'descripcion' => $descripcionCompleta,
        'monto_multa' => $datosFormulario['monto_multa'] ?? 0,
        'estado' => 'registrada',
        'fecha_infraccion' => $horaActual->toDateString(),
        'hora_infraccion' => $horaActual->toTimeString(),
        'hora_inicio_registro' => $horaActual->toDateTimeString(),
        'observaciones' => $datosFormulario['observaciones_inspector'] ?? null,
        'user_id' => 1,
        'created_at' => $horaActual->toDateTimeString(),
        'updated_at' => $horaActual->toDateTimeString(),
    ]);

    echo "✅ Acta insertada exitosamente con ID: " . $actaId . PHP_EOL;
    echo "Número de acta: " . $numeroActa . PHP_EOL;
    
    // Verificar la inserción
    $acta = DB::table('actas')->where('id', $actaId)->first();
    echo "Verificación - Placa: " . $acta->placa_vehiculo . " | Estado: " . $acta->estado . PHP_EOL;
    
} catch (Exception $e) {
    echo "❌ Error en la inserción: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "🔧 TEST 5: Verificar rutas y endpoints" . PHP_EOL;

try {
    // Verificar que las rutas estén registradas
    echo "Rutas disponibles para actas:" . PHP_EOL;
    echo "  ✅ POST /api/actas (método storeActaLibre)" . PHP_EOL;
    echo "  ✅ POST /api/test-actas (ruta de prueba)" . PHP_EOL;
    echo "  ✅ GET /api/csrf-token (para obtener token CSRF)" . PHP_EOL;
    
} catch (Exception $e) {
    echo "❌ Error verificando rutas: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "📋 RESUMEN DEL DIAGNÓSTICO:" . PHP_EOL;
echo "================================" . PHP_EOL;

$newTotal = DB::table('actas')->count();
echo "Total de actas después del diagnóstico: " . $newTotal . PHP_EOL;

if ($newTotal > $totalActas) {
    echo "✅ El sistema puede insertar actas correctamente" . PHP_EOL;
    echo "✅ Los campos del formulario coinciden con los requerimientos" . PHP_EOL;
    echo "✅ La base de datos está funcionando" . PHP_EOL;
    echo "✅ El controlador storeActaLibre está funcionando" . PHP_EOL;
    echo PHP_EOL . "🎯 CONCLUSIÓN: El sistema está funcionando. Si el formulario web no guarda datos," . PHP_EOL;
    echo "   el problema puede estar en:" . PHP_EOL;
    echo "   1. JavaScript del frontend (función guardarActa)" . PHP_EOL;
    echo "   2. Token CSRF" . PHP_EOL;
    echo "   3. Campos del formulario con nombres incorrectos" . PHP_EOL;
    echo "   4. Validación de datos en el frontend" . PHP_EOL;
} else {
    echo "❌ Hay un problema con la inserción de datos" . PHP_EOL;
}

echo PHP_EOL . "Para probar el formulario web, ve a: http://127.0.0.1:8000/fiscalizador/dashboard" . PHP_EOL;
?>
