<?php
// Test directo del método buscarActaParaEditar

require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Crear una request falsa
$request = Illuminate\Http\Request::create('/buscar-acta-editar/60015091', 'GET');

// Simular el contexto de la aplicación
$app->instance('request', $request);

// Crear el controlador
$controller = new App\Http\Controllers\ActaController();

try {
    echo "=== TEST DIRECTO DEL CONTROLADOR ===\n";
    echo "Buscando acta con criterio: 60015091\n\n";
    
    $response = $controller->buscarActaParaEditar('60015091');
    
    echo "Response Status: " . $response->getStatusCode() . "\n";
    echo "Content: " . $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
