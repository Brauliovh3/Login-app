<?php
$db = __DIR__ . '/../database/database.sqlite';
$pdo = new PDO('sqlite:' . $db);
$cols = $pdo->query("PRAGMA table_info('usuarios')")->fetchAll(PDO::FETCH_ASSOC);
echo "COLUMNS:\n";
echo json_encode($cols, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)."\n\n";
$rows = $pdo->query("SELECT * FROM usuarios LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
echo "SAMPLE ROWS (up to 50):\n";
foreach ($rows as $r) echo json_encode($r, JSON_UNESCAPED_UNICODE)."\n";
