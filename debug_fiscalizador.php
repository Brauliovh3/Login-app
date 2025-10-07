<?php
session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'login_app');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DEBUG FISCALIZADOR ACTAS ===\n\n";
    
    // 1. Información de sesión actual
    echo "1. SESIÓN ACTUAL:\n";
    echo "User ID: " . ($_SESSION['user_id'] ?? 'No definido') . "\n";
    echo "User Name: " . ($_SESSION['user_name'] ?? 'No definido') . "\n";
    echo "User Role: " . ($_SESSION['user_role'] ?? 'No definido') . "\n\n";
    
    // 2. Todos los fiscalizadores en la BD
    echo "2. FISCALIZADORES EN BD:\n";
    $stmt = $pdo->query("SELECT id, name, role FROM usuarios WHERE role = 'fiscalizador'");
    $fiscalizadores = $stmt->fetchAll();
    foreach ($fiscalizadores as $f) {
        echo "  - ID: {$f['id']}, Nombre: {$f['name']}\n";
    }
    echo "\n";
    
    // 3. Todas las actas en la BD
    echo "3. TODAS LAS ACTAS EN BD:\n";
    $stmt = $pdo->query("
        SELECT a.id, a.numero_acta, a.user_id, a.estado, a.placa_vehiculo, a.conductor_nombre, u.name as fiscalizador_nombre
        FROM actas a 
        LEFT JOIN usuarios u ON a.user_id = u.id 
        ORDER BY a.id
    ");
    $actas = $stmt->fetchAll();
    foreach ($actas as $acta) {
        $estado_texto = match((int)$acta['estado']) {
            0 => 'pendiente',
            1 => 'procesada', 
            2 => 'anulada',
            3 => 'pagada',
            default => 'pendiente'
        };
        echo "  - Acta: {$acta['numero_acta']}, User ID: {$acta['user_id']}, Fiscalizador: {$acta['fiscalizador_nombre']}, Estado: {$estado_texto}\n";
    }
    echo "\n";
    
    // 4. Si hay sesión activa, mostrar actas del fiscalizador actual
    if (isset($_SESSION['user_id'])) {
        $current_user_id = $_SESSION['user_id'];
        echo "4. ACTAS DEL FISCALIZADOR ACTUAL (ID: $current_user_id):\n";
        
        $stmt = $pdo->prepare("
            SELECT a.*, u.name as fiscalizador_nombre 
            FROM actas a 
            LEFT JOIN usuarios u ON a.user_id = u.id 
            WHERE a.user_id = ? 
            ORDER BY a.created_at DESC
        ");
        $stmt->execute([$current_user_id]);
        $mis_actas = $stmt->fetchAll();
        
        if (empty($mis_actas)) {
            echo "  ¡NO HAY ACTAS PARA ESTE FISCALIZADOR!\n";
            echo "  Esto explica por qué 'Mi Historial' puede estar vacío o mostrar datos incorrectos.\n";
        } else {
            foreach ($mis_actas as $acta) {
                $estado_texto = match((int)$acta['estado']) {
                    0 => 'pendiente',
                    1 => 'procesada', 
                    2 => 'anulada',
                    3 => 'pagada',
                    default => 'pendiente'
                };
                echo "  - {$acta['numero_acta']}, Estado: {$estado_texto}, Placa: {$acta['placa_vehiculo']}\n";
            }
        }
    } else {
        echo "4. NO HAY SESIÓN ACTIVA - no se pueden verificar las actas del fiscalizador actual.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>