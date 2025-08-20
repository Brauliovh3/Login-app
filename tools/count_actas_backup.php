<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
try {
    $count = \DB::table('actas_backup')->count();
    echo "ACTAS_BACKUP COUNT: " . $count . PHP_EOL;
    $row = \DB::table('actas_backup')->orderBy('id')->first();
    echo "EJEMPLO PRIMER REGISTRO:\n";
    print_r($row);
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}
