<?php
// Script para insertar un acta llamando al controlador dentro del contexto de la app Laravel
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
// Bootstrap minimal
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;

// Datos tipo "acta libre" que el storeActaLibre aceptará
$datos = [
    'placa_1' => 'TEST-123',
    'nombre_conductor_1' => 'Prueba Insert Local',
    'licencia_conductor_1' => 'L000000',
    'razon_social' => 'Empresa Test S.A.C.',
    'ruc_dni' => '12345678',
    'lugar_intervencion' => 'Lugar de Prueba',
    'origen_viaje' => 'Origen Test',
    'destino_viaje' => 'Destino Test',
    'tipo_servicio' => 'Urbano',
    'descripcion_hechos' => 'Acta de prueba generada por script local.'
];

$request = Request::create('/api/test-actas', 'POST', $datos);

// Instanciar el controlador y llamar al método
$controller = $app->make(App\Http\Controllers\ActaController::class);
$response = $controller->store($request);

// Si la respuesta es JsonResponse
if (method_exists($response, 'getContent')) {
    $content = $response->getContent();
    echo "RESPONSE:\n" . $content . PHP_EOL;
} else {
    var_dump($response);
}

// Mostrar conteo actual de actas
try {
    $count = \DB::table('actas')->count();
    echo "ACTAS TOTAL: " . $count . PHP_EOL;
} catch (Exception $e) {
    echo "No se pudo contar actas: " . $e->getMessage() . PHP_EOL;
}

