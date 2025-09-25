<?php
// Script para crear usuario de prueba administrador
require_once __DIR__ . '/dashboard.php';

try {
    $pdo = new PDO("mysql:host=localhost;dbname=login_app", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar si ya existe un usuario admin
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE username = 'admin' OR email = 'admin@test.com'");
    $stmt->execute();
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        // Crear usuario administrador de prueba
        $stmt = $pdo->prepare("INSERT INTO usuarios (name, username, email, password, role, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $result = $stmt->execute([
            'Administrador Test',
            'admin',
            'admin@test.com',
            password_hash('123456', PASSWORD_DEFAULT),
            'administrador',
            'approved'
        ]);
        
        if ($result) {
            echo "✅ Usuario administrador creado exitosamente\n";
            echo "📧 Usuario: admin\n";
            echo "🔑 Contraseña: 123456\n";
            echo "👤 Rol: administrador\n";
            echo "\nPuedes iniciar sesión con estas credenciales para probar el dashboard.\n";
        } else {
            echo "❌ Error al crear usuario\n";
        }
    } else {
        echo "⚠️ Ya existe un usuario admin\n";
        echo "📧 Puedes intentar usar: admin / 123456\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "\n";
    echo "Asegúrate de que:\n";
    echo "1. MySQL esté ejecutándose\n";
    echo "2. La base de datos 'login_app' exista\n";
    echo "3. La tabla 'usuarios' exista\n";
}
?>