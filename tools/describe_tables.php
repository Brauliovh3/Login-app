<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

function describe($table) {
    $cols = DB::select("DESCRIBE `$table`");
    echo "--- $table ---\n";
    foreach ($cols as $c) {
        echo $c->Field . " | " . $c->Type . " | " . $c->Null . " | " . $c->Key . " | " . $c->Default . " | " . $c->Extra . "\n";
    }
}

try {
    if (DB::select("SHOW TABLES LIKE 'actas_evidencias'")) {
        describe('actas_evidencias');
    } else {
        echo "actas_evidencias table not found\n";
    }
    if (DB::select("SHOW TABLES LIKE 'actas'")) {
        describe('actas');
    } else {
        echo "actas table not found\n";
    }
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
