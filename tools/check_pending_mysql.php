<?php
// Conectar a MySQL usando las credenciales por defecto (ajusta si es necesario)
$host = '127.0.0.1';
$db = 'login_app';
$user = 'root';
$pass = '';
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdo->query("SELECT id, username, email, status, approval_status, approved_at FROM usuarios WHERE status = 'pending'");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($rows)) {
        echo "NO_PENDING\n";
        exit(0);
    }
    echo "PENDING_USERS:\n";
    foreach ($rows as $r) {
        echo json_encode($r, JSON_UNESCAPED_UNICODE) . "\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
