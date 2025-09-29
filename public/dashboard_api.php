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

$action = $_GET['action'] ?? $_POST['action'] ?? '';

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

    case 'get_users':
        try {
            $stmt = $pdo->query("
                SELECT id, username, name, email, phone, role, status, 
                       last_login, created_at, updated_at 
                FROM usuarios 
                WHERE deleted_at IS NULL 
                ORDER BY created_at DESC
            ");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'users' => $users]);
            
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error al obtener usuarios']);
        }
        break;

    case 'get_pending_users':
        try {
            $stmt = $pdo->query("
                SELECT id, username, name, email, phone, role, status, created_at 
                FROM usuarios 
                WHERE status = 'pending' AND deleted_at IS NULL 
                ORDER BY created_at ASC
            ");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'users' => $users]);
            
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error al obtener usuarios pendientes']);
        }
        break;

    case 'update_user':
        try {
            $user_id = $_POST['user_id'];
            $name = $_POST['name'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $role = $_POST['role'];
            $status = $_POST['status'];

            $stmt = $pdo->prepare("
                UPDATE usuarios 
                SET name = ?, username = ?, email = ?, phone = ?, role = ?, status = ?, updated_at = NOW()
                WHERE id = ? AND deleted_at IS NULL
            ");
            $stmt->execute([$name, $username, $email, $phone, $role, $status, $user_id]);
            
            echo json_encode(['success' => true, 'message' => 'Usuario actualizado correctamente']);
            
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar usuario']);
        }
        break;

    case 'change_password':
        try {
            $user_id = $_POST['user_id'];
            $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                UPDATE usuarios 
                SET password = ?, updated_at = NOW()
                WHERE id = ? AND deleted_at IS NULL
            ");
            $stmt->execute([$new_password, $user_id]);
            
            echo json_encode(['success' => true, 'message' => 'Contraseña cambiada correctamente']);
            
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error al cambiar contraseña']);
        }
        break;

    case 'update_user_status':
        try {
            $user_id = $_POST['user_id'];
            $status = $_POST['status'];

            $stmt = $pdo->prepare("
                UPDATE usuarios 
                SET status = ?, updated_at = NOW()
                WHERE id = ? AND deleted_at IS NULL
            ");
            $stmt->execute([$status, $user_id]);
            
            echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente']);
            
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar estado']);
        }
        break;

    case 'delete_user':
        try {
            $user_id = $_POST['user_id'];

            // Soft delete - marcar como eliminado
            $stmt = $pdo->prepare("
                UPDATE usuarios 
                SET deleted_at = NOW()
                WHERE id = ? AND deleted_at IS NULL
            ");
            $stmt->execute([$user_id]);
            
            echo json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente']);
            
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar usuario']);
        }
        break;

    case 'approve_user':
        try {
            $user_id = $_POST['user_id'];

            $stmt = $pdo->prepare("
                UPDATE usuarios 
                SET status = 'approved', updated_at = NOW()
                WHERE id = ? AND status = 'pending' AND deleted_at IS NULL
            ");
            $stmt->execute([$user_id]);
            
            echo json_encode(['success' => true, 'message' => 'Usuario aprobado correctamente']);
            
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error al aprobar usuario']);
        }
        break;

    case 'reject_user':
        try {
            $user_id = $_POST['user_id'];

            $stmt = $pdo->prepare("
                UPDATE usuarios 
                SET status = 'rejected', updated_at = NOW()
                WHERE id = ? AND status = 'pending' AND deleted_at IS NULL
            ");
            $stmt->execute([$user_id]);
            
            echo json_encode(['success' => true, 'message' => 'Usuario rechazado']);
            
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error al rechazar usuario']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'error' => 'Acción no válida']);
        break;
}
?>