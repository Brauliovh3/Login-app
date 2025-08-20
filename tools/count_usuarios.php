<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
try {
    $count = \DB::table('usuarios')->count();
    echo "USUARIOS: " . $count . PHP_EOL;
    $first = \DB::table('usuarios')->orderBy('id')->first();
    echo "PRIMER USUARIO: "; print_r($first);
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}
