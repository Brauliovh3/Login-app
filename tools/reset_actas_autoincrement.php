<?php
// Safe script to reset AUTO_INCREMENT for `actas` when table is empty.
// Usage: php tools/reset_actas_autoincrement.php

$host = '127.0.0.1';
$port = 3306;
$db = 'login_app';
$user = 'root';
$pass = '';

try {
    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $stmt = $pdo->query('SELECT COUNT(*) AS c FROM actas');
    $count = (int) $stmt->fetchColumn();
    echo "actas row count: {$count}\n";

    if ($count === 0) {
        $pdo->exec('ALTER TABLE actas AUTO_INCREMENT = 1');
        echo "AUTO_INCREMENT reset to 1 (table empty).\n";
        exit(0);
    }

    echo "Table not empty. To force reset (destructive), run with manual confirmation in the app UI.\n";
    exit(2);

} catch (PDOException $e) {
    fwrite(STDERR, "Database error: " . $e->getMessage() . "\n");
    exit(3);
}
