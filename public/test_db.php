<?php
// Test de conexión a la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'login_app');
define('DB_USER', 'root');
define('DB_PASS', '');

echo "<h2>🔍 Test de Conexión a Base de Datos</h2>";

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ <strong>Conexión exitosa a la base de datos!</strong><br><br>";
    
    // Verificar tablas existentes
    echo "<h3>📋 Tablas en la base de datos:</h3>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "❌ No se encontraron tablas en la base de datos<br>";
    } else {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
    }
    
    // Verificar datos de usuarios
    if (in_array('usuarios', $tables)) {
        echo "<h3>👥 Datos de Usuarios:</h3>";
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
        $totalUsers = $stmt->fetch()['total'];
        
        $stmt = $pdo->query("SELECT COUNT(*) as active FROM usuarios WHERE status = 'approved'");
        $activeUsers = $stmt->fetch()['active'];
        
        $stmt = $pdo->query("SELECT COUNT(*) as pending FROM usuarios WHERE status = 'pending'");
        $pendingUsers = $stmt->fetch()['pending'];
        
        echo "<ul>";
        echo "<li><strong>Total Usuarios:</strong> $totalUsers</li>";
        echo "<li><strong>Usuarios Activos:</strong> $activeUsers</li>";
        echo "<li><strong>Usuarios Pendientes:</strong> $pendingUsers</li>";
        echo "</ul>";
        
        // Mostrar algunos usuarios
        echo "<h4>📝 Usuarios en la base de datos:</h4>";
        $stmt = $pdo->query("SELECT id, name, username, email, role, status FROM usuarios LIMIT 10");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($users)) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>ID</th><th>Nombre</th><th>Usuario</th><th>Email</th><th>Rol</th><th>Estado</th></tr>";
            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                echo "<td>" . htmlspecialchars($user['name'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                echo "<td>" . htmlspecialchars($user['email'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                echo "<td>" . htmlspecialchars($user['status'] ?? 'N/A') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "❌ No hay usuarios en la base de datos<br>";
        }
    } else {
        echo "❌ La tabla 'usuarios' no existe<br>";
    }
    
} catch(PDOException $e) {
    echo "❌ <strong>Error de conexión:</strong> " . $e->getMessage() . "<br>";
    echo "<br><h3>🔧 Posibles soluciones:</h3>";
    echo "<ul>";
    echo "<li>Verificar que XAMPP esté ejecutándose</li>";
    echo "<li>Verificar que MySQL esté iniciado</li>";
    echo "<li>Verificar que la base de datos 'login_app' exista</li>";
    echo "<li>Verificar las credenciales de la base de datos</li>";
    echo "</ul>";
}

echo "<br><hr>";
echo "<a href='dashboard.php'>🔙 Volver al Dashboard</a>";
?>