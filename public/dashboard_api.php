<?php
session_start();
header('Content-Type: application/json');

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

// Configuración de la base de datos
$host = 'localhost';
$dbname = 'login_app';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión']);
    exit();
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'stats':
        try {
            // Obtener estadísticas básicas
            $stats = [];
            
            // Total de usuarios
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE deleted_at IS NULL");
            $stats['usuarios'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Total de trámites (si existe la tabla)
            try {
                $stmt = $pdo->query("SELECT COUNT(*) as total FROM tramites WHERE deleted_at IS NULL");
                $stats['tramites'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            } catch (PDOException $e) {
                $stats['tramites'] = 0;
            }
            
            // Total de actas (si existe la tabla)
            try {
                $stmt = $pdo->query("SELECT COUNT(*) as total FROM actas WHERE deleted_at IS NULL");
                $stats['actas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            } catch (PDOException $e) {
                $stats['actas'] = 0;
            }
            
            // Pendientes (ejemplo)
            $stats['pendientes'] = rand(5, 25); // Simulado
            
            echo json_encode(['success' => true, 'stats' => $stats]);
            
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Error al obtener estadísticas']);
        }
        break;
        
    case 'activity':
        try {
            // Actividad reciente (simulada por ahora)
            $activities = [
                ['action' => 'Nuevo usuario registrado', 'time' => '5 minutos', 'icon' => 'user-plus'],
                ['action' => 'Acta generada', 'time' => '15 minutos', 'icon' => 'file-alt'],
                ['action' => 'Trámite procesado', 'time' => '1 hora', 'icon' => 'check-circle'],
                ['action' => 'Sistema actualizado', 'time' => '2 horas', 'icon' => 'cog']
            ];
            
            echo json_encode(['success' => true, 'activities' => $activities]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Error al obtener actividad']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'error' => 'Acción no válida']);
        break;
}
?>