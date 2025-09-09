<?php
$db = __DIR__ . '/../database/database.sqlite';
if (!file_exists($db)) {
    echo "DB missing\n";
    exit(1);
}
try {
    $pdo = new PDO('sqlite:' . $db);
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
    echo "TABLES:\n";
    echo implode("\n", $tables) . "\n";
    if (in_array('usuarios', $tables)) {
        echo "\nUSUARIOS SAMPLE:\n";
        $rows = $pdo->query('SELECT * FROM usuarios LIMIT 50')->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $r) {
            echo json_encode($r, JSON_UNESCAPED_UNICODE) . "\n";
        }
    } else {
        echo "\nusuarios table NOT found\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
