<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "MIGRATIONS RUN:\n";
    $migrations = \DB::table('migrations')->orderBy('id')->get();
    foreach ($migrations as $m) {
        echo $m->migration . PHP_EOL;
    }
} catch (Exception $e) {
    echo "No se pudo leer migrations: " . $e->getMessage() . PHP_EOL;
}

try {
    echo PHP_EOL . "DESCRIBE actas:\n";
    $cols = \DB::select('DESCRIBE actas');
    foreach ($cols as $c) {
        echo $c->Field . ' | ' . $c->Type . ' | ' . $c->Null . ' | ' . $c->Key . PHP_EOL;
    }
} catch (Exception $e) {
    echo "No se pudo describir actas: " . $e->getMessage() . PHP_EOL;
}
