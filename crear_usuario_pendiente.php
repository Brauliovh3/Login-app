<?php
// Conexión directa a la base de datos
$host = 'localhost';
$dbname = 'login_app';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== CREANDO USUARIO PENDIENTE ===" . PHP_EOL;
    
    // Crear usuario pendiente de prueba
    $stmt = $pdo->prepare("
        INSERT INTO usuarios (name, username, email, password, role, status, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");
    
    $password_hash = password_hash('password123', PASSWORD_DEFAULT);
    
    $stmt->execute([
        'Carlos Mendoza',
        'cmendoza',
        'carlos.mendoza@ejemplo.com',
        $password_hash,
        'fiscalizador',
        'pending'
    ]);
    
    $userId = $pdo->lastInsertId();
    echo "✅ Usuario pendiente creado con ID: {$userId}" . PHP_EOL;
    
    // Verificar usuarios pendientes
    $stmt = $pdo->query("SELECT id, name, username, email, role, status FROM usuarios WHERE status = 'pending'");
    $pendingUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo PHP_EOL . "USUARIOS PENDIENTES ACTUALES:" . PHP_EOL;
    foreach ($pendingUsers as $user) {
        echo "- {$user['name']} ({$user['username']}) - {$user['email']} - {$user['role']}" . PHP_EOL;
    }
    
    echo PHP_EOL . "✅ Ahora puedes probar la funcionalidad 'Aprobar Usuarios'" . PHP_EOL;
    echo "URL: http://127.0.0.1:8000/dashboard.php" . PHP_EOL;
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . PHP_EOL;
}