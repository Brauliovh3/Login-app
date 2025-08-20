<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $row = DB::selectOne('SELECT COUNT(*) as c FROM actas_minimal');
    echo "actas_minimal count: " . ($row->c ?? 0) . "\n";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
