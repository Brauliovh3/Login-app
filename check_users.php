<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Usuarios en el sistema:\n";
echo "=====================\n";

$users = DB::table('usuarios')->get();

foreach($users as $user) {
    echo "ID: {$user->id}\n";
    echo "Usuario: {$user->username}\n";
    echo "Email: {$user->email}\n";
    echo "Rol: {$user->role}\n";
    echo "Estado: " . ($user->status ?? 'NULL') . "\n";
    echo "Aprobado: " . ($user->approved_at ?? 'NULL') . "\n";
    echo "-------------------\n";
}
