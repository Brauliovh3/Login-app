<?php
// Usage: php tools/set_superadmin_password.php
$username = 'Brauliovh3';
$newPassword = '1Leucemia1';

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=login_app','root','', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $hash = password_hash($newPassword, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('UPDATE usuarios SET password = ? WHERE username = ?');
    $stmt->execute([$hash, $username]);

    $stmt2 = $pdo->prepare('SELECT password FROM usuarios WHERE username = ?');
    $stmt2->execute([$username]);
    $row = $stmt2->fetch(PDO::FETCH_ASSOC);
    if ($row && password_verify($newPassword, $row['password'])) {
        echo "Password updated and verified for {$username}\n";
        exit(0);
    }

    echo "Failed to verify updated password\n";
    exit(2);

} catch (PDOException $e) {
    echo "DB error: " . $e->getMessage() . "\n";
    exit(1);
}
