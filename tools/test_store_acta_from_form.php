<?php
// Script de prueba que llama internamente a ActaController::store() simulando la request del formulario
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\ActaController;

// Construir request similar al formulario
$requestData = [
    'inspector' => 'Inspector Test CLI',
    'inspector_principal' => 'Inspector Test CLI',
    'fecha_intervencion' => date('Y-m-d'),
    'hora_intervencion' => date('H:i:s'),
    'lugar_intervencion' => 'Av. Prueba 123',
    'direccion_especifica' => 'Av. Prueba 123',
    'tipo_servicio' => 'Interprovincial',
    'tipo_agente' => 'Transportista',
    'placa' => 'ABC-999',
    'razon_social' => 'Transportes CLI SAC',
    'ruc_dni' => '20456789012',
    'nombre_conductor' => 'Juan CLI',
    'licencia_conductor' => 'L000111',
    'clase_licencia' => 'A II',
    'descripcion_hechos' => 'Prueba de inserción desde CLI',
    'codigo_infraccion' => 'INF-CLI',
    'gravedad' => 'leve',
    'monto_multa' => '0',
];

$request = Request::create('/fiscalizador/actas', 'POST', $requestData);

$controller = new ActaController();
$response = $controller->store($request);

if (is_object($response)) {
    if (method_exists($response, 'getContent')) {
        echo $response->getContent() . "\n";
    } else {
        print_r($response);
    }
} else {
    print_r($response);
}

// Mostrar última fila en actas
try {
    $row = Illuminate\Support\Facades\DB::table('actas')->orderBy('id', 'desc')->first();
    echo "Última acta: \n";
    print_r($row);
} catch (Throwable $e) {
    echo "Error al leer actas: " . $e->getMessage() . "\n";
}
