<?php
$db = __DIR__ . '/../database/database.sqlite';
$pdo = new PDO('sqlite:' . $db);
$count = $pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
echo "COUNT: $count\n";
$rows = $pdo->query('SELECT * FROM usuarios LIMIT 30')->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo json_encode($r, JSON_UNESCAPED_UNICODE) . "\n";
}
