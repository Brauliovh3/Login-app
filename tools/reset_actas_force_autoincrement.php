<?php
// Destructive script to truncate `actas` table and reset AUTO_INCREMENT to 1.
// WARNING: This deletes all rows. Use only when you have confirmed backups.
// Usage: php tools/reset_actas_force_autoincrement.php

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
    echo "actas row count before truncate: {$count}\n";

    echo "Disabling foreign key checks...\n";
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');

    echo "Truncating table actas...\n";
    $pdo->exec('TRUNCATE TABLE actas');

    echo "Re-enabling foreign key checks...\n";
    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');

    echo "Setting AUTO_INCREMENT = 1...\n";
    $pdo->exec('ALTER TABLE actas AUTO_INCREMENT = 1');

    $stmt2 = $pdo->query('SELECT COUNT(*) AS c FROM actas');
    $count2 = (int) $stmt2->fetchColumn();
    echo "actas row count after truncate: {$count2}\n";
    echo "Done. AUTO_INCREMENT reset to 1.\n";
    exit(0);

} catch (PDOException $e) {
    fwrite(STDERR, "Database error: " . $e->getMessage() . "\n");
    exit(1);
}
