<?php
session_start();

// Verificar autenticación y permisos
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'No autenticado']));
}

// Solo admin puede ejecutar esto
if ($_SESSION['user_role'] !== 'administrador' && $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    die(json_encode(['success' => false, 'message' => 'Acceso denegado. Solo administradores pueden limpiar actas.']));
}

// Configuración de base de datos
$config = require __DIR__ . '/../config/database.php';

try {
    $dsn = "mysql:host={$config['host']};dbname={$config['name']}";
    $pdo = new PDO($dsn, $config['user'], $config['pass'], $config['options']);
    
    // Iniciar transacción
    $pdo->beginTransaction();
    
    // 1. Eliminar actas completamente vacías o con datos mínimos inválidos
    $sqlVacias = "DELETE FROM actas WHERE 
        (numero_acta IS NULL OR numero_acta = '' OR numero_acta = '0') 
        AND (ruc_dni IS NULL OR ruc_dni = '')
        AND (placa IS NULL OR placa = '')
        AND (placa_vehiculo IS NULL OR placa_vehiculo = '')
        AND (nombre_conductor IS NULL OR nombre_conductor = '')
        AND (nombres_conductor IS NULL OR nombres_conductor = '')
        AND (apellidos_conductor IS NULL OR apellidos_conductor = '')";
    
    $stmt = $pdo->prepare($sqlVacias);
    $stmt->execute();
    $eliminadasVacias = $stmt->rowCount();
    
    // 2. Eliminar actas duplicadas (mismo número de acta y año)
    $sqlDuplicadas = "DELETE a1 FROM actas a1
        INNER JOIN actas a2 
        WHERE a1.id > a2.id 
        AND a1.numero_acta = a2.numero_acta 
        AND a1.anio_acta = a2.anio_acta
        AND a1.numero_acta IS NOT NULL 
        AND a1.numero_acta != ''";
    
    $stmt = $pdo->prepare($sqlDuplicadas);
    $stmt->execute();
    $eliminadasDuplicadas = $stmt->rowCount();
    
    // 3. Limpiar actas con fechas inválidas
    $sqlFechasInvalidas = "UPDATE actas 
        SET fecha_intervencion = CURDATE() 
        WHERE fecha_intervencion IS NULL 
        OR fecha_intervencion = '0000-00-00'
        OR fecha_intervencion > CURDATE()";
    
    $stmt = $pdo->prepare($sqlFechasInvalidas);
    $stmt->execute();
    $corregidas = $stmt->rowCount();
    
    // 4. Normalizar estados (convertir a números si están como texto)
    $sqlNormalizarEstados = "UPDATE actas 
        SET estado = CASE 
            WHEN LOWER(estado) = 'pendiente' OR estado = '0' THEN 0
            WHEN LOWER(estado) = 'procesada' OR LOWER(estado) = 'procesado' OR estado = '1' THEN 1
            WHEN LOWER(estado) = 'anulada' OR LOWER(estado) = 'anulado' OR estado = '2' THEN 2
            WHEN LOWER(estado) = 'pagada' OR LOWER(estado) = 'pagado' OR estado = '3' THEN 3
            ELSE 0
        END
        WHERE estado IS NOT NULL";
    
    $stmt = $pdo->prepare($sqlNormalizarEstados);
    $stmt->execute();
    $normalizadas = $stmt->rowCount();
    
    // Confirmar transacción
    $pdo->commit();
    
    // Obtener estadísticas finales
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM actas");
    $totalActas = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo json_encode([
        'success' => true, 
        'message' => 'Limpieza completada exitosamente',
        'detalles' => [
            'actas_vacias_eliminadas' => $eliminadasVacias,
            'actas_duplicadas_eliminadas' => $eliminadasDuplicadas,
            'fechas_corregidas' => $corregidas,
            'estados_normalizados' => $normalizadas,
            'total_actas_restantes' => $totalActas
        ]
    ]);
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Error al limpiar actas: ' . $e->getMessage()
    ]);
}
