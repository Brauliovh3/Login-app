<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\InspectorController;

$request = Request::create('/', 'POST', [
    'nombres' => 'DirectCall',
    'apellidos' => 'Test',
    'dni' => 'DNI_DIRECT_'.time(),
    'telefono' => '999111222',
    'codigo_inspector' => 'DIRECT_'.time(),
]);

$controller = new InspectorController();
$response = $controller->store($request);

if (method_exists($response, 'getStatusCode')) {
    echo "Status: " . $response->getStatusCode() . PHP_EOL;
}

echo (string) $response->getContent() . PHP_EOL;
