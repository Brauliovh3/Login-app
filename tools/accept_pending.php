<?php
$db = __DIR__ . '/../database/database.sqlite';
$pdo = new PDO('sqlite:' . $db);
// listar pendientes
$pending = $pdo->query("SELECT id, username, email, status FROM usuarios WHERE status='pending'")->fetchAll(PDO::FETCH_ASSOC);
if (count($pending) === 0) {
    echo "No pending users found\n";
    exit(0);
}
echo "Pending users:\n";
foreach ($pending as $p) echo json_encode($p, JSON_UNESCAPED_UNICODE) . "\n";
// actualizar todos los pending
$now = date('Y-m-d H:i:s');
$approved_by = 1; // set to admin id; adjust if needed
$upd = $pdo->prepare("UPDATE usuarios SET status='approved', approval_status='accepted', approved_at=?, approved_by=? WHERE status='pending'");
$upd->execute([$now, $approved_by]);
// mostrar actualizados
$updated = $pdo->query("SELECT id, username, email, status, approval_status, approved_at, approved_by FROM usuarios WHERE status='approved'")->fetchAll(PDO::FETCH_ASSOC);
echo "\nUpdated users:\n";
foreach ($updated as $u) echo json_encode($u, JSON_UNESCAPED_UNICODE) . "\n";
