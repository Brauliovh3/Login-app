<?php
// Ejecuta una migración concreta usando el bootstrap de Laravel.
// Uso: php tools/run_migration_file.php [ruta_relativa_a_migration]

$arg = $argv[1] ?? null;
if ($arg) {
    $path = $arg;
    // si se pasó una ruta relativa desde la carpeta raíz del proyecto, normalizar
    if (!file_exists($path)) {
        $path = __DIR__ . '/../' . ltrim($arg, '/\\');
    }
} else {
    $path = __DIR__ . '/../database/migrations/2025_08_20_100000_create_actas_evidencias.php';
}
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Bootstrap the framework to make facades and DB available
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $migration = include $path;
    if (is_object($migration) && method_exists($migration, 'up')) {
        $migration->up();
        echo "Migration executed: $path\n";
        exit(0);
    } else {
        echo "Migration file did not return a migration object.\n";
        exit(2);
    }
} catch (Throwable $e) {
    echo "Error executing migration: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
