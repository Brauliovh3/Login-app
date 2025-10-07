<?php

// Crear usuario de prueba
require_once __DIR__ . '/vendor/autoload.php';

// Configurar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "=== CREANDO USUARIO DE PRUEBA PARA FISCALIZADOR ===\n";

try {
    // Verificar si ya existe un usuario fiscalizador
    $existeUsuario = DB::table('usuarios')->where('role', 'fiscalizador')->first();
    
    if ($existeUsuario) {
        echo "✅ Usuario fiscalizador ya existe: {$existeUsuario->username}\n";
        echo "📧 Email: {$existeUsuario->email}\n";
        echo "👤 Estado: {$existeUsuario->status}\n";
    } else {
        // Crear nuevo usuario fiscalizador
        $userId = DB::table('usuarios')->insertGetId([
            'name' => 'Fiscalizador Prueba',
            'username' => 'fiscalizador_test',
            'email' => 'fiscalizador@test.com',
            'password' => Hash::make('password123'),
            'role' => 'fiscalizador',
            'status' => 'approved',
            'email_verified_at' => now(),
            'approved_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "✅ Usuario fiscalizador creado exitosamente\n";
        echo "👤 ID: $userId\n";
        echo "👤 Usuario: fiscalizador_test\n";
        echo "🔑 Password: password123\n";
        echo "📧 Email: fiscalizador@test.com\n";
    }
    
    // Mostrar URL para probar
    echo "\n=== URLS DE PRUEBA ===\n";
    echo "🌐 Dashboard Fiscalizador: http://127.0.0.1:8001/fiscalizador/dashboard\n";
    echo "🌐 Login: http://127.0.0.1:8001/login\n";
    
    // Verificar estado del servidor
    echo "\n=== VERIFICACIÓN DEL SERVIDOR ===\n";
    echo "🚀 Servidor Laravel: http://127.0.0.1:8001\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== USUARIO LISTO PARA PRUEBAS ===\n";
