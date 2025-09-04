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

    echo "Attempting DELETE from actas inside transaction...\n";
    try {
        $pdo->beginTransaction();
        $pdo->exec('DELETE FROM actas');
        $pdo->commit();

        // ALTER outside transaction
        $pdo->exec('ALTER TABLE actas AUTO_INCREMENT = 1');
    } catch (PDOException $e) {
        // Some MySQL setups may still block DELETE due to FK constraints. As a last resort,
        // try disabling foreign key checks and run TRUNCATE (destructive). Log and continue.
        try {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
        } catch (PDOException $__rb) {
            // ignore
        }

        fwrite(STDERR, "DELETE failed: " . $e->getMessage() . "\nTrying TRUNCATE with FK checks disabled...\n");
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
        $pdo->exec('TRUNCATE TABLE actas');
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
        $pdo->exec('ALTER TABLE actas AUTO_INCREMENT = 1');
    }

    $stmt2 = $pdo->query('SELECT COUNT(*) AS c FROM actas');
    $count2 = (int) $stmt2->fetchColumn();
    echo "actas row count after truncate: {$count2}\n";
    echo "Done. AUTO_INCREMENT reset to 1.\n";
    exit(0);

} catch (PDOException $e) {
    fwrite(STDERR, "Database error: " . $e->getMessage() . "\n");
    exit(1);
}
