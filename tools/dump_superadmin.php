<?php
require __DIR__ . '/../vendor/autoload.php';
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=login_app','root','', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdo->prepare('SELECT id,username,email,role,status,password FROM usuarios WHERE username = ?');
    $stmt->execute(['Brauliovh3']);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        echo json_encode($row, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo "No user found\n";
    }
} catch (PDOException $e) {
    echo "DB error: " . $e->getMessage() . "\n";
}
