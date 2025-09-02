<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// set blocked_at for id=10
DB::table('usuarios')->where('id', 10)->update(['blocked_at' => date('Y-m-d H:i:s')]);
$u = DB::table('usuarios')->where('id', 10)->first();
echo json_encode($u, JSON_PRETTY_PRINT) . PHP_EOL;
