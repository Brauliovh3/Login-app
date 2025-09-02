<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App as AppFacade;
use Illuminate\Http\Request;

// Build a request to POST /admin/inspectores
$request = Request::create('/admin/inspectores', 'POST', [
    'nombres' => 'PruebaHTTP',
    'apellidos' => 'Test',
    'dni' => 'DNI_HTTP_'.time(),
    'telefono' => '999000111',
    'codigo_inspector' => 'HTTP_CODE_'.time(),
]);

// Set the request into the container and dispatch
$response = AppFacade::handle($request);

echo "Status: " . $response->getStatusCode() . PHP_EOL;
echo (string) $response->getContent() . PHP_EOL;
