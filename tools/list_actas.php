<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
try {
    $rows = \DB::table('actas')->latest('created_at')->take(5)->get();
    echo "ULTIMAS ACTAS:\n";
    foreach ($rows as $r) {
        print_r($r);
        echo "-----------------\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}
