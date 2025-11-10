<?php
session_start();
require_once 'dashboard.php';

// Solo admin puede ejecutar esto
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'administrador' && $_SESSION['user_role'] !== 'admin')) {
    die(json_encode(['success' => false, 'message' => 'Acceso denegado']));
}

$dashboard = new DashboardApp();

try {
    $pdo = $dashboard->connectDatabase();
    
    // Eliminar actas con datos vacíos o inválidos
    $sql = "DELETE FROM actas WHERE 
            (numero_acta IS NULL OR numero_acta = '') 
            AND (ruc_dni IS NULL OR ruc_dni = '')
            AND (placa IS NULL OR placa = '' OR placa_vehiculo IS NULL OR placa_vehiculo = '')";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $eliminadas = $stmt->rowCount();
    
    echo json_encode([
        'success' => true, 
        'message' => "Se eliminaron $eliminadas actas erróneas",
        'eliminadas' => $eliminadas
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
