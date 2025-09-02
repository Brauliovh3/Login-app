<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

try {
    $data = [
        'nombres' => 'Juan',
        'apellidos' => 'Perez',
        'dni' => '12345678',
        'telefono' => '999888777',
        'codigo_inspector' => 'INSP_TEST_'.time(),
    ];

    // Simulate validation and insert like controller
    $id = DB::table('inspectores')->insertGetId(array_merge($data, [
        'estado' => 'activo',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]));

    echo "Inserted inspector id: $id\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
