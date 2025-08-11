<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Aprobando todos los usuarios existentes...\n";

$updated = DB::table('usuarios')->update([
    'status' => 'approved',
    'approved_at' => now(),
    'updated_at' => now()
]);

echo "Se aprobaron {$updated} usuarios.\n";

echo "\nEstado actual de usuarios:\n";
echo "=========================\n";

$users = DB::table('usuarios')->get();

foreach($users as $user) {
    echo "Usuario: {$user->username} | Rol: {$user->role} | Estado: {$user->status}\n";
}
