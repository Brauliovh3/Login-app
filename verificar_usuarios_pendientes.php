<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->boot();

use Illuminate\Support\Facades\DB;

echo "=== VERIFICACIÓN DE USUARIOS PENDIENTES ===" . PHP_EOL;

// Obtener usuarios pendientes
$pendingUsers = DB::table('usuarios')->where('status', 'pending')->get();

echo "Usuarios pendientes encontrados: " . $pendingUsers->count() . PHP_EOL;

if ($pendingUsers->count() > 0) {
    echo PHP_EOL . "LISTA DE USUARIOS PENDIENTES:" . PHP_EOL;
    foreach ($pendingUsers as $user) {
        echo "- ID: {$user->id}, Nombre: {$user->name}, Username: {$user->username}, Email: {$user->email}, Rol: {$user->role}" . PHP_EOL;
    }
} else {
    echo PHP_EOL . "No hay usuarios pendientes en la base de datos." . PHP_EOL;
    echo "Creando un usuario de prueba pendiente..." . PHP_EOL;
    
    // Crear un usuario pendiente de prueba
    $userId = DB::table('usuarios')->insertGetId([
        'name' => 'Usuario Prueba',
        'username' => 'uprueba',
        'email' => 'usuario@prueba.com',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'fiscalizador',
        'status' => 'pending',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "✅ Usuario de prueba creado con ID: {$userId}" . PHP_EOL;
}

echo PHP_EOL . "=== PRUEBA DE API ===" . PHP_EOL;
echo "Ahora prueba la URL: http://127.0.0.1:8000/dashboard.php?api=pending-users" . PHP_EOL;