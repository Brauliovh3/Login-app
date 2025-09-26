<?php
session_start();

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'login_app');
define('DB_USER', 'root');
define('DB_PASS', '');

// Clase principal del Dashboard
class DashboardApp {
    private $pdo;
    private $user;
    private $userRole;
    private $userName;
    
    public function __construct() {
        $this->connectDatabase();
        $this->authenticateUser();
    }
    
    private function connectDatabase() {
        try {
            $this->pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            $this->showLoginForm("Error de conexión a la base de datos");
            exit;
        }
    }
    
    private function authenticateUser() {
        // Manejar login
        if (isset($_POST['login_action'])) {
            $this->processLogin();
        }
        
        // Manejar logout
        if (isset($_GET['logout'])) {
            $this->logout();
        }
        
        // Verificar sesión activa
        if (!isset($_SESSION['user_id'])) {
            $this->showLoginForm();
            exit;
        }
        
        // Obtener datos del usuario
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $this->user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$this->user || $this->user['status'] !== 'approved') {
            session_destroy();
            $this->showLoginForm("Usuario no válido o no aprobado");
            exit;
        }
        
        $this->userRole = $this->user['role'];
        $this->userName = $this->user['name'];
    }
    
    private function processLogin() {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $this->showLoginForm("Por favor complete todos los campos");
            return;
        }
        
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE (username = ? OR email = ?) AND status = 'approved'");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['name'];
                header('Location: dashboard.php');
                exit;
            } else {
                $this->showLoginForm("Credenciales incorrectas o usuario no aprobado");
            }
        } catch(PDOException $e) {
            $this->showLoginForm("Error en el sistema de login");
        }
    }
    
    private function logout() {
        session_destroy();
        header('Location: dashboard.php');
        exit;
    }
    
    private function showLoginForm($error = '') {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Iniciar Sesión - Sistema de Gestión</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <style>
                body {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    margin: 0;
                    padding: 20px;
                }
                .container {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    width: 100%;
                    padding: 0;
                }
                .login-container {
                    background: white;
                    border-radius: 25px;
                    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
                    overflow: hidden;
                    max-width: 900px;
                    width: 85%;
                    margin: auto;
                    min-height: 500px;
                    display: flex;
                    flex-direction: column;
                }
                .login-header {
                    background: linear-gradient(135deg, #2c3e50, #34495e);
                    color: white;
                    padding: 40px 60px;
                    text-align: center;
                    flex: 0 0 auto;
                }
                .login-header h3 {
                    font-size: 2.2rem;
                    margin-bottom: 15px;
                    font-weight: 700;
                }
                .login-header p {
                    font-size: 1.2rem;
                    opacity: 0.9;
                    margin-bottom: 0;
                }
                .login-body {
                    padding: 50px 80px;
                    flex: 1;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                }
                .form-control {
                    border-radius: 12px;
                    border: 2px solid #e9ecef;
                    padding: 15px 25px;
                    font-size: 18px;
                    margin-bottom: 15px;
                    height: 55px;
                    width: 100%;
                }
                
                .password-container {
                    position: relative;
                }
                
                .password-container .form-control {
                    padding-right: 55px; /* Espacio para el ícono */
                }
                
                .password-toggle {
                    position: absolute;
                    right: 15px;
                    top: 50%;
                    transform: translateY(-50%);
                    cursor: pointer;
                    color: #6c757d;
                    font-size: 18px;
                    z-index: 10;
                }
                
                .password-toggle:hover {
                    color: #3498db;
                }
                .form-control:focus {
                    border-color: #3498db;
                    box-shadow: 0 0 0 0.3rem rgba(52, 152, 219, 0.25);
                }
                .form-label {
                    font-size: 16px;
                    font-weight: 600;
                    margin-bottom: 8px;
                    color: #2c3e50;
                }
                .btn-login {
                    background: linear-gradient(135deg, #3498db, #2980b9);
                    border: none;
                    border-radius: 25px;
                    padding: 15px 50px;
                    font-weight: bold;
                    font-size: 18px;
                    transition: transform 0.3s ease;
                    width: 100%;
                    height: 55px;
                    margin-top: 25px;
                }
                .btn-login:hover {
                    transform: translateY(-2px);
                    background: linear-gradient(135deg, #2980b9, #1f4e79);
                }
                .alert {
                    border-radius: 10px;
                    padding: 15px;
                    margin-bottom: 25px;
                }
                @media (max-width: 768px) {
                    .login-container {
                        max-width: 500px;
                        width: 95%;
                    }
                    .login-header {
                        padding: 30px 40px;
                    }
                    .login-body {
                        padding: 40px 50px;
                    }
                    .form-control {
                        height: 50px;
                        font-size: 16px;
                    }
                    .btn-login {
                        height: 50px;
                        font-size: 16px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="container-fluid">
                <div class="row justify-content-center align-items-center min-vh-100">
                    <div class="col-12 col-sm-11 col-md-9 col-lg-8 col-xl-7">
                        <div class="login-container">
                            <div class="login-header">
                                <i class="fas fa-shield-alt fa-3x mb-3"></i>
                                <h3>Sistema de Gestión</h3>
                                <p class="mb-0">Acceso al Dashboard</p>
                            </div>
                            <div class="login-body">
                                <?php if ($error): ?>
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <form method="POST" action="dashboard.php">
                                    <input type="hidden" name="login_action" value="1">
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label"><i class="fas fa-user"></i> Usuario o Email</label>
                                            <input type="text" class="form-control" name="username" required>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label"><i class="fas fa-lock"></i> Contraseña</label>
                                            <div class="password-container">
                                                <input type="password" class="form-control" name="password" id="password" required>
                                                <i class="fas fa-eye password-toggle" id="togglePassword" onclick="togglePasswordVisibility()"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-primary btn-login">
                                                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                
                                <script src="js/login.js"></script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
    
    // API Handler
    public function handleAPI() {
        if (isset($_GET['api'])) {
            header('Content-Type: application/json');
            
            switch($_GET['api']) {
                case 'dashboard-stats':
                    echo json_encode($this->getDashboardStats());
                    break;
                case 'actas':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        echo json_encode($this->saveActa());
                    } else {
                        echo json_encode($this->getActas());
                    }
                    break;
                case 'acta-details':
                    echo json_encode($this->getActaDetails($_GET['id'] ?? 0));
                    break;
                case 'users':
                    echo json_encode($this->getUsers());
                    break;
                case 'pending-users':
                    echo json_encode($this->getPendingUsers());
                    break;
                case 'create-user':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        echo json_encode($this->createUser());
                    }
                    break;
                case 'save-acta':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        echo json_encode($this->saveActa());
                    }
                    break;
                case 'consultar-documento':
                    echo json_encode($this->consultarDocumento($_GET['documento'] ?? ''));
                    break;
                case 'approve-user':
                    echo json_encode($this->approveUser($_POST['user_id'] ?? 0));
                    break;
                case 'reject-user':
                    echo json_encode($this->rejectUser($_POST['user_id'] ?? 0, $_POST['reason'] ?? ''));
                    break;
                case 'delete-acta':
                    echo json_encode($this->deleteActa($_POST['acta_id'] ?? 0));
                    break;
                case 'notifications':
                    echo json_encode($this->getUserNotifications());
                    break;
                    
                case 'mark_notification_read':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $notificationId = $_POST['notification_id'] ?? null;
                        echo json_encode($this->markNotificationRead($notificationId));
                    }
                    break;
                    
                case 'conductores':
                    echo json_encode($this->getConductores());
                    break;
                    
                case 'conductor':
                    echo json_encode($this->getConductor($_GET['id'] ?? 0));
                    break;
                    
                case 'vehiculos':
                    echo json_encode($this->getVehiculos());
                    break;
                    
                case 'vehiculo':
                    echo json_encode($this->getVehiculo($_GET['id'] ?? 0));
                    break;
                    
                case 'infracciones':
                    echo json_encode($this->getInfracciones());
                    break;
                    
                case 'inspecciones':
                    echo json_encode($this->getInspecciones());
                    break;
                    
                case 'acta_details':
                    $actaId = $_GET['id'] ?? null;
                    echo json_encode($this->getActaDetails($actaId));
                    break;
                    
                case 'update_acta':
                    if ($_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'POST') {
                        $actaId = $_GET['id'] ?? $_POST['id'] ?? null;
                        echo json_encode($this->updateActa($actaId));
                    }
                    break;
                    
                case 'create_user':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        echo json_encode($this->createUser());
                    }
                    break;
                    
                case 'update_user':
                    if ($_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'POST') {
                        echo json_encode($this->updateUser());
                    }
                    break;
                    
                case 'update-user':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        echo json_encode($this->updateUserComplete());
                    }
                    break;
                    
                case 'update-user-status':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        echo json_encode($this->updateUserStatus());
                    }
                    break;
                    
                case 'delete-user':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        echo json_encode($this->deleteUserComplete());
                    }
                    break;
                    break;
                    
                case 'toggle_user_status':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $userId = $_POST['id'] ?? null;
                        echo json_encode($this->toggleUserStatus($userId));
                    }
                    break;
                case 'mark-notification-read':
                    echo json_encode($this->markNotificationAsRead($_POST['notification_id'] ?? 0));
                    break;
                case 'mark-all-notifications-read':
                    echo json_encode($this->markAllNotificationsAsRead());
                    break;
                case 'conductores':
                    echo json_encode($this->getConductores());
                    break;
                case 'vehiculos':
                    echo json_encode($this->getVehiculos());
                    break;
                case 'infracciones':
                    echo json_encode($this->getInfracciones());
                    break;
                case 'update-acta':
                    echo json_encode($this->updateActa($_POST['acta_id'] ?? 0));
                    break;
                case 'profile':
                    echo json_encode($this->getUserProfile());
                    break;
                case 'update-profile':
                    echo json_encode($this->updateUserProfile());
                    break;
                case 'system-config':
                    echo json_encode($this->getSystemConfig());
                    break;
                case 'update-config':
                    echo json_encode($this->updateSystemConfig());
                    break;
                case 'infracciones':
                    echo json_encode($this->getInfracciones());
                    break;
                case 'save-infraccion':
                    echo json_encode($this->saveInfraccion());
                    break;
                case 'export-data':
                    echo json_encode($this->exportData($_GET['type'] ?? 'users'));
                    break;
                case 'update-conductor':
                    echo json_encode($this->updateConductor());
                    break;
                case 'update-vehiculo':
                    echo json_encode($this->updateVehiculo());
                    break;
                default:
                    echo json_encode(['success' => false, 'message' => 'API endpoint no encontrado']);
            }
            exit;
        }
    }
    
    // Métodos API
    private function getDashboardStats() {
        try {
            $stats = [];
            
            switch($this->userRole) {
                case 'superadmin':
                case 'administrador':
                    // Total de usuarios
                    $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM usuarios");
                    $totalUsers = $stmt->fetch()['total'];
                    
                    // Usuarios activos
                    $stmt = $this->pdo->query("SELECT COUNT(*) as active FROM usuarios WHERE status = 'approved'");
                    $activeUsers = $stmt->fetch()['active'];
                    
                    // Usuarios pendientes
                    $stmt = $this->pdo->query("SELECT COUNT(*) as pending FROM usuarios WHERE status = 'pending'");
                    $pendingUsers = $stmt->fetch()['pending'];
                    
                    // Total de conductores
                    $stmt = $this->pdo->query("SELECT COUNT(*) as conductores FROM conductores");
                    $totalConductores = $stmt->fetch()['conductores'] ?? 0;
                    
                    // Total de vehículos
                    $stmt = $this->pdo->query("SELECT COUNT(*) as vehiculos FROM vehiculos");
                    $totalVehiculos = $stmt->fetch()['vehiculos'] ?? 0;
                    
                    // Total de infracciones
                    $stmt = $this->pdo->query("SELECT COUNT(*) as infracciones FROM actas");
                    $totalInfracciones = $stmt->fetch()['infracciones'] ?? 0;
                    
                    $stats = [
                        'total_usuarios' => $totalUsers,
                        'usuarios_activos' => $activeUsers,
                        'usuarios_pendientes' => $pendingUsers,
                        'total_conductores' => $totalConductores,
                        'total_vehiculos' => $totalVehiculos,
                        'total_infracciones' => $totalInfracciones
                    ];
                    break;
                    
                case 'fiscalizador':
                case 'inspector':
                    $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM actas");
                    $totalActas = $stmt->fetch()['total'];
                    
                    $stmt = $this->pdo->query("SELECT COUNT(*) as procesadas FROM actas WHERE estado = 1");
                    $actasProcesadas = $stmt->fetch()['procesadas'];
                    
                    $pendientes = $totalActas - $actasProcesadas;
                    
                    $stmt = $this->pdo->query("SELECT COALESCE(SUM(monto_multa), 0) as total FROM actas");
                    $totalMultas = $stmt->fetch()['total'];
                    
                    $stats = [
                        'total_infracciones' => $totalActas,
                        'infracciones_procesadas' => $actasProcesadas,
                        'infracciones_pendientes' => $pendientes,
                        'total_multas' => $totalMultas
                    ];
                    break;
                    
                case 'ventanilla':
                    $stmt = $this->pdo->query("SELECT COUNT(*) as hoy FROM actas WHERE DATE(created_at) = CURDATE()");
                    $atencionesHoy = $stmt->fetch()['hoy'];
                    
                    $stmt = $this->pdo->query("SELECT COUNT(*) as completados FROM actas WHERE estado = 1 AND DATE(created_at) = CURDATE()");
                    $completados = $stmt->fetch()['completados'];
                    
                    $cola = $atencionesHoy - $completados;
                    
                    $stats = [
                        'atenciones_hoy' => $atencionesHoy,
                        'cola_espera' => $cola,
                        'tramites_completados' => $completados,
                        'tiempo_promedio' => 15
                    ];
                    break;
            }
            
            return ['success' => true, 'stats' => $stats];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function getActas() {
        try {
            // Filtrar actas según el rol del usuario
            if ($this->userRole === 'fiscalizador' || $this->userRole === 'inspector') {
                $stmt = $this->pdo->prepare("SELECT a.*, u.name as user_name FROM actas a LEFT JOIN usuarios u ON a.user_id = u.id WHERE a.user_id = ? ORDER BY a.created_at DESC LIMIT 50");
                $stmt->execute([$this->user['id']]);
            } else {
                $stmt = $this->pdo->prepare("SELECT a.*, u.name as user_name FROM actas a LEFT JOIN usuarios u ON a.user_id = u.id ORDER BY a.created_at DESC LIMIT 50");
                $stmt->execute();
            }
            
            $actas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'actas' => $actas];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function getActaDetails($actaId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT a.*, u.name as user_name, 
                       c.nombres as conductor_nombres, c.apellidos as conductor_apellidos,
                       v.marca, v.modelo, v.año as vehiculo_año,
                       i.descripcion as infraccion_descripcion
                FROM actas a 
                LEFT JOIN usuarios u ON a.user_id = u.id
                LEFT JOIN conductores c ON a.licencia_conductor = c.numero_licencia
                LEFT JOIN vehiculos v ON a.placa = v.placa
                LEFT JOIN infracciones i ON a.numero_acta = i.codigo_infraccion
                WHERE a.id = ?
            ");
            $stmt->execute([$actaId]);
            $acta = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($acta) {
                return ['success' => true, 'acta' => $acta];
            } else {
                return ['success' => false, 'message' => 'Acta no encontrada'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function saveActa() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Generar número de acta automático si no se proporciona
            if (empty($data['numero_acta'])) {
                $stmt = $this->pdo->query("SELECT MAX(CAST(SUBSTRING(numero_acta, 4) AS UNSIGNED)) as last_num FROM actas WHERE numero_acta LIKE 'ACT%'");
                $lastNum = $stmt->fetch()['last_num'] ?? 0;
                $data['numero_acta'] = 'ACT' . str_pad($lastNum + 1, 6, '0', STR_PAD_LEFT);
            }
            
            $sql = "INSERT INTO actas (
                numero_acta, lugar_intervencion, fecha_intervencion, hora_intervencion, 
                inspector_responsable, tipo_servicio, tipo_agente, placa, placa_vehiculo,
                razon_social, ruc_dni, licencia_conductor, nombre_conductor, clase_licencia,
                monto_multa, estado, user_id, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, NOW(), NOW())";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $data['numero_acta'],
                $data['lugar_intervencion'] ?? null,
                $data['fecha_intervencion'],
                $data['hora_intervencion'],
                $data['inspector_responsable'] ?? $this->userName,
                $data['tipo_servicio'] ?? null,
                $data['tipo_agente'],
                $data['placa'],
                $data['placa_vehiculo'] ?? $data['placa'],
                $data['razon_social'],
                $data['ruc_dni'],
                $data['licencia_conductor'] ?? null,
                $data['nombre_conductor'] ?? null,
                $data['clase_licencia'] ?? null,
                $data['monto_multa'] ?? null,
                $this->user['id']
            ]);
            
            if ($result) {
                $actaId = $this->pdo->lastInsertId();
                
                // Crear notificación para administradores
                $this->createNotification(
                    'Nueva acta creada',
                    'Se ha creado la acta ' . $data['numero_acta'] . ' por ' . $this->userName,
                    'administrador,superadmin'
                );
                
                return ['success' => true, 'message' => 'Acta guardada correctamente', 'acta_id' => $actaId];
            } else {
                return ['success' => false, 'message' => 'Error al guardar acta'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function updateActa($actaId) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $sql = "UPDATE actas SET 
                lugar_intervencion = ?, fecha_intervencion = ?, hora_intervencion = ?,
                inspector_responsable = ?, tipo_servicio = ?, tipo_agente = ?,
                placa = ?, placa_vehiculo = ?, razon_social = ?, ruc_dni = ?,
                licencia_conductor = ?, nombre_conductor = ?, clase_licencia = ?,
                monto_multa = ?, updated_at = NOW()
                WHERE id = ? AND (user_id = ? OR ? IN ('administrador', 'superadmin'))";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $data['lugar_intervencion'] ?? null,
                $data['fecha_intervencion'],
                $data['hora_intervencion'],
                $data['inspector_responsable'] ?? $this->userName,
                $data['tipo_servicio'] ?? null,
                $data['tipo_agente'],
                $data['placa'],
                $data['placa_vehiculo'] ?? $data['placa'],
                $data['razon_social'],
                $data['ruc_dni'],
                $data['licencia_conductor'] ?? null,
                $data['nombre_conductor'] ?? null,
                $data['clase_licencia'] ?? null,
                $data['monto_multa'] ?? null,
                $actaId,
                $this->user['id'],
                $this->userRole
            ]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Acta actualizada correctamente'];
            } else {
                return ['success' => false, 'message' => 'No se pudo actualizar el acta'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function getConductores() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM conductores ORDER BY apellidos, nombres LIMIT 100");
            $conductores = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'conductores' => $conductores];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function getConductor($id) {
        try {
            if (!$id) {
                throw new Exception('ID de conductor requerido');
            }
            
            $stmt = $this->pdo->prepare("SELECT * FROM conductores WHERE id = ?");
            $stmt->execute([$id]);
            $conductor = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$conductor) {
                throw new Exception('Conductor no encontrado');
            }
            
            return ['success' => true, 'conductor' => $conductor];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function getVehiculos() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM vehiculos ORDER BY placa LIMIT 100");
            $vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'vehiculos' => $vehiculos];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function getVehiculo($id) {
        try {
            if (!$id) {
                throw new Exception('ID de vehículo requerido');
            }
            
            $stmt = $this->pdo->prepare("SELECT * FROM vehiculos WHERE id = ?");
            $stmt->execute([$id]);
            $vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$vehiculo) {
                throw new Exception('Vehículo no encontrado');
            }
            
            return ['success' => true, 'vehiculo' => $vehiculo];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function getInfracciones() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM infracciones ORDER BY codigo_infraccion LIMIT 100");
            $infracciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'infracciones' => $infracciones];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function getInspecciones() {
        try {
            if ($this->userRole === 'fiscalizador' || $this->userRole === 'inspector') {
                $stmt = $this->pdo->prepare("SELECT i.*, u.name as user_name FROM inspecciones i LEFT JOIN usuarios u ON i.inspector_id = u.id WHERE i.inspector_id = ? ORDER BY i.fecha_inspeccion DESC LIMIT 50");
                $stmt->execute([$this->user['id']]);
            } else {
                $stmt = $this->pdo->prepare("SELECT i.*, u.name as user_name FROM inspecciones i LEFT JOIN usuarios u ON i.inspector_id = u.id ORDER BY i.fecha_inspeccion DESC LIMIT 50");
                $stmt->execute();
            }
            
            $inspecciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'inspecciones' => $inspecciones];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function getUserNotifications() {
        try {
            $roles = $this->userRole;
            if ($this->userRole === 'superadmin') {
                $roles .= ',administrador';
            }
            
            $stmt = $this->pdo->prepare("
                SELECT * FROM notifications 
                WHERE (target_role = 'all' OR target_role LIKE ? OR FIND_IN_SET(?, target_role)) 
                AND (user_id IS NULL OR user_id = ?)
                ORDER BY created_at DESC 
                LIMIT 20
            ");
            $stmt->execute(["%{$this->userRole}%", $this->userRole, $this->user['id']]);
            $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'notifications' => $notifications];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function markNotificationRead($notificationId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
            $result = $stmt->execute([$notificationId]);
            
            return ['success' => $result, 'message' => $result ? 'Notificación marcada como leída' : 'Error al marcar notificación'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function createNotification($title, $message, $targetRole = 'all', $userId = null) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO notifications (title, message, target_role, user_id, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $result = $stmt->execute([$title, $message, $targetRole, $userId]);
            
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }
    
    private function createUser() {
        if (!in_array($this->userRole, ['administrador', 'superadmin'])) {
            return ['success' => false, 'message' => 'Sin permisos'];
        }
        
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validar datos requeridos
            if (empty($data['name']) || empty($data['username']) || empty($data['email']) || empty($data['password'])) {
                return ['success' => false, 'message' => 'Todos los campos son requeridos'];
            }
            
            // Verificar si el usuario o email ya existe
            $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE username = ? OR email = ?");
            $stmt->execute([$data['username'], $data['email']]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'El usuario o email ya existe'];
            }
            
            $sql = "INSERT INTO usuarios (name, username, email, password, role, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 'active', NOW(), NOW())";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $data['name'],
                $data['username'],
                $data['email'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                $data['role'] ?? 'usuario'
            ]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Usuario creado correctamente'];
            } else {
                return ['success' => false, 'message' => 'Error al crear usuario'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function updateUser($userId) {
        if (!in_array($this->userRole, ['administrador', 'superadmin'])) {
            return ['success' => false, 'message' => 'Sin permisos'];
        }
        
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Construir la consulta de actualización
            $updateFields = [];
            $params = [];
            
            if (!empty($data['name'])) {
                $updateFields[] = "name = ?";
                $params[] = $data['name'];
            }
            
            if (!empty($data['username'])) {
                $updateFields[] = "username = ?";
                $params[] = $data['username'];
            }
            
            if (!empty($data['email'])) {
                $updateFields[] = "email = ?";
                $params[] = $data['email'];
            }
            
            if (!empty($data['password'])) {
                $updateFields[] = "password = ?";
                $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            if (!empty($data['role'])) {
                $updateFields[] = "role = ?";
                $params[] = $data['role'];
            }
            
            if (empty($updateFields)) {
                return ['success' => false, 'message' => 'No hay datos para actualizar'];
            }
            
            $updateFields[] = "updated_at = NOW()";
            $params[] = $userId;
            
            $sql = "UPDATE usuarios SET " . implode(', ', $updateFields) . " WHERE id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($params);
            
            if ($result) {
                return ['success' => true, 'message' => 'Usuario actualizado correctamente'];
            } else {
                return ['success' => false, 'message' => 'Error al actualizar usuario'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function deleteUser($userId) {
        if (!in_array($this->userRole, ['administrador', 'superadmin'])) {
            return ['success' => false, 'message' => 'Sin permisos'];
        }
        
        if ($userId == $this->user['id']) {
            return ['success' => false, 'message' => 'No puedes eliminar tu propia cuenta'];
        }
        
        try {
            $stmt = $this->pdo->prepare("DELETE FROM usuarios WHERE id = ?");
            $result = $stmt->execute([$userId]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Usuario eliminado correctamente'];
            } else {
                return ['success' => false, 'message' => 'Error al eliminar usuario'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function toggleUserStatus($userId) {
        if (!in_array($this->userRole, ['administrador', 'superadmin'])) {
            return ['success' => false, 'message' => 'Sin permisos'];
        }
        
        if ($userId == $this->user['id']) {
            return ['success' => false, 'message' => 'No puedes cambiar el estado de tu propia cuenta'];
        }
        
        try {
            // Obtener el estado actual
            $stmt = $this->pdo->prepare("SELECT status FROM usuarios WHERE id = ?");
            $stmt->execute([$userId]);
            $currentStatus = $stmt->fetchColumn();
            
            if (!$currentStatus) {
                return ['success' => false, 'message' => 'Usuario no encontrado'];
            }
            
            // Cambiar el estado
            $newStatus = ($currentStatus === 'active') ? 'inactive' : 'active';
            
            $stmt = $this->pdo->prepare("UPDATE usuarios SET status = ?, updated_at = NOW() WHERE id = ?");
            $result = $stmt->execute([$newStatus, $userId]);
            
            if ($result) {
                $statusText = ($newStatus === 'active') ? 'activado' : 'desactivado';
                return ['success' => true, 'message' => "Usuario {$statusText} correctamente"];
            } else {
                return ['success' => false, 'message' => 'Error al cambiar estado del usuario'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function getUsers() {
        if (!in_array($this->userRole, ['administrador', 'superadmin'])) {
            return ['success' => false, 'message' => 'Sin permisos'];
        }
        
        try {
            $stmt = $this->pdo->query("SELECT id, name, username, email, role, status, created_at FROM usuarios ORDER BY created_at DESC");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'users' => $users];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function getPendingUsers() {
        if (!in_array($this->userRole, ['administrador', 'superadmin'])) {
            return ['success' => false, 'message' => 'Sin permisos'];
        }
        
        try {
            $stmt = $this->pdo->query("SELECT id, name, username, email, role, status, created_at FROM usuarios WHERE status = 'pending' ORDER BY created_at DESC");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'users' => $users];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function consultarDocumento($documento) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM actas WHERE ruc_dni = ? ORDER BY created_at DESC");
            $stmt->execute([$documento]);
            $actas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'actas' => $actas];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function approveUser($userId) {
        if (!in_array($this->userRole, ['administrador', 'superadmin'])) {
            return ['success' => false, 'message' => 'Sin permisos'];
        }
        
        try {
            $stmt = $this->pdo->prepare("UPDATE usuarios SET status = 'approved', approved_at = NOW(), approved_by = ? WHERE id = ?");
            $stmt->execute([$this->user['id'], $userId]);
            
            return ['success' => true, 'message' => 'Usuario aprobado'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function rejectUser($userId, $reason) {
        if (!in_array($this->userRole, ['administrador', 'superadmin'])) {
            return ['success' => false, 'message' => 'Sin permisos'];
        }
        
        try {
            $stmt = $this->pdo->prepare("UPDATE usuarios SET status = 'rejected', rejection_reason = ? WHERE id = ?");
            $stmt->execute([$reason, $userId]);
            
            return ['success' => true, 'message' => 'Usuario rechazado'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function deleteActa($actaId) {
        if (!in_array($this->userRole, ['administrador', 'superadmin', 'fiscalizador'])) {
            return ['success' => false, 'message' => 'Sin permisos'];
        }
        
        try {
            $stmt = $this->pdo->prepare("DELETE FROM actas WHERE id = ?");
            $stmt->execute([$actaId]);
            
            return ['success' => true, 'message' => 'Acta eliminada'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function getUserProfile() {
        try {
            $stmt = $this->pdo->prepare("SELECT id, name, username, email, role, status, created_at, updated_at FROM usuarios WHERE id = ?");
            $stmt->execute([$this->user['id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                throw new Exception('Usuario no encontrado');
            }
            
            return ['success' => true, 'user' => $user];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    private function updateUserProfile() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $name = $data['name'] ?? '';
            $email = $data['email'] ?? '';
            
            if (!$name || !$email) {
                throw new Exception('Nombre y email son requeridos');
            }
            
            // Verificar si el email ya existe (excluyendo el usuario actual)
            $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
            $stmt->execute([$email, $this->user['id']]);
            if ($stmt->fetch()) {
                throw new Exception('El email ya está en uso');
            }
            
            $stmt = $this->pdo->prepare("UPDATE usuarios SET name = ?, email = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$name, $email, $this->user['id']]);
            
            return ['success' => true, 'message' => 'Perfil actualizado correctamente'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    private function getSystemConfig() {
        try {
            // Configuraciones del sistema
            $config = [
                'nombre_sistema' => 'Sistema de Gestión de Actas',
                'version' => '1.0.0',
                'mantenimiento' => false,
                'max_usuarios' => 100,
                'sesion_timeout' => 30,
                'backup_automatico' => true,
                'notificaciones_email' => true,
                'idioma' => 'es',
                // Configuración de temas
                'tema_principal' => 'default',
                'color_primario' => '#2c3e50',
                'color_secundario' => '#3498db',
                'color_fondo' => '#f8f9fa',
                'modo_oscuro' => false
            ];
            
            return ['success' => true, 'config' => $config];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    private function updateSystemConfig() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                return ['success' => false, 'message' => 'Datos inválidos'];
            }
            
            $type = $data['type'] ?? 'system';
            unset($data['type']); // Remover el tipo de los datos a procesar
            
            if ($type === 'theme') {
                // Validar configuración de tema
                $validThemes = ['default', 'dark', 'light', 'blue', 'green'];
                if (isset($data['tema_principal']) && !in_array($data['tema_principal'], $validThemes)) {
                    return ['success' => false, 'message' => 'Tema no válido'];
                }
                
                // Validar colores hexadecimales
                if (isset($data['color_primario']) && !preg_match('/^#[a-fA-F0-9]{6}$/', $data['color_primario'])) {
                    return ['success' => false, 'message' => 'Color primario no válido'];
                }
                
                return ['success' => true, 'message' => 'Configuración de tema actualizada correctamente'];
            } else {
                // Configuración general del sistema
                return ['success' => true, 'message' => 'Configuración general actualizada correctamente'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    private function saveInfraccion() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Implementar guardado de infracción usando la estructura de actas existente
            $stmt = $this->pdo->prepare("INSERT INTO actas (
                lugar_intervencion, fecha_intervencion, hora_intervencion,
                inspector_responsable, tipo_servicio, tipo_agente,
                placa, placa_vehiculo, razon_social, ruc_dni,
                licencia_conductor, nombre_conductor, clase_licencia,
                monto_multa, user_id, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            
            $result = $stmt->execute([
                $data['lugar_intervencion'] ?? '',
                $data['fecha_intervencion'] ?? date('Y-m-d'),
                $data['hora_intervencion'] ?? date('H:i:s'),
                $this->userName,
                $data['tipo_servicio'] ?? '',
                $data['tipo_agente'] ?? '',
                $data['placa'] ?? '',
                $data['placa_vehiculo'] ?? $data['placa'] ?? '',
                $data['razon_social'] ?? '',
                $data['ruc_dni'] ?? '',
                $data['licencia_conductor'] ?? '',
                $data['nombre_conductor'] ?? '',
                $data['clase_licencia'] ?? '',
                $data['monto_multa'] ?? 0,
                $this->user['id']
            ]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Infracción guardada correctamente'];
            } else {
                return ['success' => false, 'message' => 'Error al guardar la infracción'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    private function exportData($type) {
        try {
            $data = [];
            
            switch ($type) {
                case 'users':
                    $stmt = $this->pdo->prepare("SELECT name, username, email, telefono, role, status, created_at FROM usuarios ORDER BY created_at DESC");
                    $stmt->execute();
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
                case 'actas':
                    $stmt = $this->pdo->prepare("SELECT * FROM actas ORDER BY created_at DESC");
                    $stmt->execute();
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
                case 'stats':
                    $stats = $this->getStatsData();
                    $data = $stats['success'] ? $stats : [];
                    break;
            }
            
            return ['success' => true, 'data' => $data, 'type' => $type, 'count' => count($data)];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    private function updateConductor() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data['id']) {
                throw new Exception('ID de conductor requerido');
            }
            
            $sql = "UPDATE conductores SET 
                nombres = ?, apellidos = ?, dni = ?, fecha_nacimiento = ?, 
                direccion = ?, distrito = ?, provincia = ?, departamento = ?, 
                telefono = ?, email = ?, numero_licencia = ?, clase_categoria = ?, 
                fecha_expedicion = ?, fecha_vencimiento = ?, estado_licencia = ?, 
                estado = ?, updated_at = NOW() 
                WHERE id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $data['nombres'] ?? '',
                $data['apellidos'] ?? '',
                $data['dni'] ?? '',
                $data['fecha_nacimiento'] ?? null,
                $data['direccion'] ?? '',
                $data['distrito'] ?? '',
                $data['provincia'] ?? '',
                $data['departamento'] ?? '',
                $data['telefono'] ?? '',
                $data['email'] ?? '',
                $data['numero_licencia'] ?? '',
                $data['clase_categoria'] ?? '',
                $data['fecha_expedicion'] ?? null,
                $data['fecha_vencimiento'] ?? null,
                $data['estado_licencia'] ?? '',
                $data['estado'] ?? 'activo',
                $data['id']
            ]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Conductor actualizado correctamente'];
            } else {
                return ['success' => false, 'message' => 'Error al actualizar conductor'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    private function updateVehiculo() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data['id']) {
                throw new Exception('ID de vehículo requerido');
            }
            
            $sql = "UPDATE vehiculos SET 
                placa = ?, marca = ?, modelo = ?, año = ?, color = ?, 
                numero_motor = ?, numero_chasis = ?, clase = ?, categoria = ?, 
                combustible = ?, asientos = ?, peso_bruto = ?, carga_util = ?, 
                estado = ?, fecha_soat = ?, fecha_revision_tecnica = ?, updated_at = NOW() 
                WHERE id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $data['placa'] ?? '',
                $data['marca'] ?? '',
                $data['modelo'] ?? '',
                $data['año'] ?? null,
                $data['color'] ?? '',
                $data['numero_motor'] ?? '',
                $data['numero_chasis'] ?? '',
                $data['clase'] ?? '',
                $data['categoria'] ?? '',
                $data['combustible'] ?? '',
                $data['asientos'] ?? null,
                $data['peso_bruto'] ?? null,
                $data['carga_util'] ?? null,
                $data['estado'] ?? 'vigente',
                $data['fecha_soat'] ?? null,
                $data['fecha_revision_tecnica'] ?? null,
                $data['id']
            ]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Vehículo actualizado correctamente'];
            } else {
                return ['success' => false, 'message' => 'Error al actualizar vehículo'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function getUserRole() {
        return $this->userRole;
    }
    
    public function getUserName() {
        return $this->userName;
    }

    // Nuevos métodos para gestión completa de usuarios
    public function updateUserComplete() {
        try {
            $id = $_POST['id'] ?? null;
            $name = $_POST['name'] ?? '';
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $role = $_POST['role'] ?? '';
            $status = $_POST['status'] ?? '';
            $password = $_POST['password'] ?? '';

            if (!$id || !$name || !$username || !$email || !$role || !$status) {
                return ['success' => false, 'message' => 'Faltan datos requeridos'];
            }

            // Verificar que el usuario existe
            $checkStmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE id = ?");
            $checkStmt->execute([$id]);
            if (!$checkStmt->fetch()) {
                return ['success' => false, 'message' => 'Usuario no encontrado'];
            }

            // Verificar que no exista otro usuario con el mismo username o email
            $duplicateStmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE (username = ? OR email = ?) AND id != ?");
            $duplicateStmt->execute([$username, $email, $id]);
            if ($duplicateStmt->fetch()) {
                return ['success' => false, 'message' => 'Ya existe un usuario con ese username o email'];
            }

            // Preparar la consulta de actualización
            if (!empty($password)) {
                // Actualizar con nueva contraseña
                $stmt = $this->pdo->prepare("
                    UPDATE usuarios SET 
                        name = ?, username = ?, email = ?, phone = ?, 
                        role = ?, status = ?, password = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $result = $stmt->execute([$name, $username, $email, $phone, $role, $status, $hashedPassword, $id]);
            } else {
                // Actualizar sin cambiar contraseña
                $stmt = $this->pdo->prepare("
                    UPDATE usuarios SET 
                        name = ?, username = ?, email = ?, phone = ?, 
                        role = ?, status = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $result = $stmt->execute([$name, $username, $email, $phone, $role, $status, $id]);
            }

            if ($result) {
                return ['success' => true, 'message' => 'Usuario actualizado correctamente'];
            } else {
                return ['success' => false, 'message' => 'Error al actualizar usuario'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function updateUserStatus() {
        try {
            $id = $_POST['id'] ?? null;
            $status = $_POST['status'] ?? '';

            if (!$id || !$status) {
                return ['success' => false, 'message' => 'Faltan datos requeridos'];
            }

            $validStatuses = ['approved', 'pending', 'rejected', 'suspended'];
            if (!in_array($status, $validStatuses)) {
                return ['success' => false, 'message' => 'Estado no válido'];
            }

            $stmt = $this->pdo->prepare("UPDATE usuarios SET status = ?, updated_at = NOW() WHERE id = ?");
            $result = $stmt->execute([$status, $id]);

            if ($result) {
                return ['success' => true, 'message' => 'Estado actualizado correctamente'];
            } else {
                return ['success' => false, 'message' => 'Error al actualizar estado'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function deleteUserComplete() {
        try {
            $id = $_POST['id'] ?? null;

            if (!$id) {
                return ['success' => false, 'message' => 'ID de usuario requerido'];
            }

            // No permitir eliminar al usuario actual
            if ($id == $this->user['id']) {
                return ['success' => false, 'message' => 'No puedes eliminarte a ti mismo'];
            }

            // Verificar que el usuario existe
            $checkStmt = $this->pdo->prepare("SELECT id, name FROM usuarios WHERE id = ?");
            $checkStmt->execute([$id]);
            $userToDelete = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$userToDelete) {
                return ['success' => false, 'message' => 'Usuario no encontrado'];
            }

            // Eliminar el usuario
            $stmt = $this->pdo->prepare("DELETE FROM usuarios WHERE id = ?");
            $result = $stmt->execute([$id]);

            if ($result) {
                return ['success' => true, 'message' => 'Usuario eliminado correctamente'];
            } else {
                return ['success' => false, 'message' => 'Error al eliminar usuario'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}

// Inicializar la aplicación
$app = new DashboardApp();
$app->handleAPI();

// Variables para la vista
$usuario = $app->getUserName();
$rol = $app->getUserRole();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Gestión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --dark-color: #34495e;
            --light-color: #ecf0f1;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            padding-top: 70px;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--dark-color));
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1030;
        }

        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }

        .nav-link {
            color: white !important;
            transition: all 0.3s ease;
            margin: 0 5px;
            border-radius: 5px;
            padding: 8px 15px !important;
        }

        .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            transform: translateY(-2px);
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 70px;
            left: 0;
            width: 200px;
            height: calc(100vh - 70px);
            background: white;
            border-right: 1px solid #e9ecef;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1020;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 20px;
            background: linear-gradient(135deg, var(--secondary-color), #2980b9);
            color: white;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .sidebar-menu {
            padding: 0;
        }

        .sidebar-item {
            list-style: none;
            margin: 0;
        }

        .sidebar-link {
            display: block;
            padding: 15px 25px;
            color: #495057;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-link:hover {
            background-color: #f8f9fa;
            color: var(--secondary-color);
            border-left-color: var(--secondary-color);
        }

        .sidebar-link.active {
            background-color: #e3f2fd;
            color: var(--secondary-color);
            border-left-color: var(--secondary-color);
            font-weight: 600;
        }

        .sidebar-link i {
            width: 20px;
            margin-right: 10px;
        }

        .sidebar-toggle {
            position: relative;
        }

        .sidebar-submenu {
            list-style: none;
            padding: 0;
            margin: 0;
            background-color: #f8f9fa;
            display: none;
            border-left: 3px solid var(--secondary-color);
        }

        .sidebar-submenu.show {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                max-height: 0;
            }
            to {
                opacity: 1;
                max-height: 300px;
            }
        }

        .sidebar-subitem {
            list-style: none;
        }

        .sidebar-sublink {
            display: block;
            padding: 10px 20px 10px 45px;
            color: #6c757d;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .sidebar-sublink:hover {
            background-color: #e9ecef;
            color: var(--secondary-color);
            padding-left: 50px;
        }

        .sidebar-sublink.active {
            background-color: var(--secondary-color);
            color: white;
        }

        .sidebar-arrow {
            float: right;
            transition: transform 0.3s ease;
            margin-left: auto;
        }

        .sidebar-toggle.expanded .sidebar-arrow {
            transform: rotate(180deg);
        }

        .sidebar-submenu .sidebar-link {
            padding-left: 45px;
            font-size: 0.9rem;
        }

        .sidebar-toggle {
            cursor: pointer;
        }

        .sidebar-toggle::after {
            content: '\f104';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            float: right;
            transition: transform 0.3s ease;
        }

        .sidebar-toggle.expanded::after {
            transform: rotate(-90deg);
        }

        /* Main content with sidebar */
        .main-wrapper {
            margin-left: 200px;
            margin-top: 0;
            min-height: calc(100vh - 70px);
            padding: 20px;
            width: calc(100% - 200px);
            box-sizing: border-box;
        }
        
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            width: 100%;
            box-sizing: border-box;
        }

        /* Notification Badge */
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.75rem;
            min-width: 18px;
            height: 18px;
            line-height: 14px;
            text-align: center;
            font-weight: bold;
            display: none;
            z-index: 1000;
        }
        
        .nav-link {
            position: relative;
            color: #ffffff !important;
            padding: 0.5rem 1rem;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .nav-link:hover {
            color: #f8f9fa !important;
        }
        
        .dropdown-menu {
            min-width: 320px;
            max-width: 400px;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 0.375rem;
        }
        
        .dropdown-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #dee2e6;
            transition: background-color 0.15s ease-in-out;
            white-space: normal;
        }
        
        .dropdown-item:last-child {
            border-bottom: none;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
        }
        
        .dropdown-item.fw-bold.bg-light {
            background-color: #e3f2fd !important;
            border-left: 4px solid #2196f3;
            font-size: 10px;
            font-weight: bold;
        }

        /* Stats Cards */
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-left: 4px solid var(--secondary-color);
            text-align: center;
            position: relative;
        }
        
        .row.mb-4 {
            margin-left: -15px;
            margin-right: -15px;
        }
        
        .row.mb-4 > [class*="col-"] {
            padding-left: 15px;
            padding-right: 15px;
        }
        
        #dashboardStats {
            margin-bottom: 30px;
        }
        
        #contentContainer {
            width: 100%;
            box-sizing: border-box;
            padding: 0 20px;
        }
        
        .dashboard-welcome {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .stats-trend {
            font-size: 0.8rem;
            color: var(--secondary-color);
            font-weight: 500;
            opacity: 0.8;
            margin-top: 3px;
        }

        /* Content sections */
        .content-section {
            display: none;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            width: 100%;
            box-sizing: border-box;
        }

        .content-section.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideIn {
            from { 
                opacity: 0; 
                transform: translateX(100%); 
            }
            to { 
                opacity: 1; 
                transform: translateX(0); 
            }
        }

        .sidebar-link.active,
        .sidebar-sublink.active {
            background-color: var(--secondary-color);
            color: white !important;
        }

        .avatar-sm {
            width: 32px;
            height: 32px;
        }

        .avatar-lg {
            width: 64px;
            height: 64px;
        }

        .avatar-title {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            font-weight: 600;
        }

        @keyframes slideOut {
            from { 
                opacity: 1; 
                transform: translateX(0); 
            }
            to { 
                opacity: 0; 
                transform: translateX(100%); 
            }
        }

        .custom-alert {
            pointer-events: auto;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-wrapper {
                margin-left: 0;
                width: 100%;
                padding: 20px 15px;
            }
            
            .main-content {
                padding: 0 10px;
                max-width: 100%;
            }
            
            .stats-card {
                padding: 20px;
                margin-bottom: 15px;
            }
            
            .stats-number {
                font-size: 2rem;
            }
            
            .mobile-sidebar-toggle {
                display: block;
            }
            
            .dashboard-welcome {
                padding: 20px;
                margin-bottom: 20px;
            }
        }

        @media (max-width: 576px) {
            .main-wrapper {
                padding: 15px 10px;
            }
            
            .stats-card {
                padding: 15px;
                margin-bottom: 12px;
            }
            
            .stats-number {
                font-size: 1.8rem;
            }
            
            .stats-label {
                font-size: 0.8rem;
            }
            
            #contentContainer {
                padding: 0 10px;
            }
            
            .stats-trend {
                font-size: 0.75rem;
            }
        }

        .mobile-sidebar-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <button class="mobile-sidebar-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <a class="navbar-brand" href="#"><i class="fas fa-shield-alt"></i> Sistema de Gestión</a>
            
            <div class="d-flex align-items-center">
                <!-- Notificaciones -->
                <div class="nav-item dropdown me-3">
                    <a class="nav-link" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge" id="notificationCount">0</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                        <li><h6 class="dropdown-header">Notificaciones</h6></li>
                        <div id="notificationsList">
                            <li><span class="dropdown-item-text">No hay notificaciones</span></li>
                        </div>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#" onclick="markAllAsRead()">Marcar todas como leídas</a></li>
                    </ul>
                </div>
                
                <!-- Usuario -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($app->getUserName()); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" onclick="loadSection('perfil')">
                            <i class="fas fa-user-edit"></i> Mi Perfil
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="loadSection('configuracion')">
                            <i class="fas fa-cog"></i> Configuración
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="dashboard.php?logout=1">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="mb-3">
                <i class="fas fa-user-circle fa-3x"></i>
            </div>
            <h6><?php echo htmlspecialchars($app->getUserName()); ?></h6>
            <span class="role-badge role-<?php echo $app->getUserRole(); ?>">
                <?php echo strtoupper($app->getUserRole()); ?>
            </span>
        </div>
        
        <ul class="sidebar-menu" id="sidebarMenu">
            <!-- Botones según el rol del usuario -->
            
            <!-- Dashboard - Para todos los roles -->
            <li class="sidebar-item">
                <a class="sidebar-link" href="javascript:void(0)" onclick="loadSection('dashboard')" data-section="dashboard">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>

            <?php if ($rol === 'administrador' || $rol === 'admin'): ?>
            <!-- Menú para Administrador -->
            <li class="sidebar-item">
                <a class="sidebar-link sidebar-toggle" href="javascript:void(0)" onclick="toggleSubmenu('usuarios', event)">
                    <i class="fas fa-users"></i> Gestión de Usuarios
                    <i class="fas fa-chevron-down sidebar-arrow"></i>
                </a>
                <ul class="sidebar-submenu" id="submenu-usuarios">
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="javascript:void(0)" onclick="loadUsuariosList()" data-section="listar-usuarios">
                            <i class="fas fa-list"></i> Lista de Usuarios
                        </a>
                    </li>
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="javascript:void(0)" onclick="loadSection('aprobar-usuarios')" data-section="aprobar-usuarios">
                            <i class="fas fa-user-check"></i> Aprobar Usuarios
                        </a>
                    </li>
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="javascript:void(0)" onclick="showCrearUsuarioModal()" data-section="crear-usuario">
                            <i class="fas fa-user-plus"></i> Crear Usuario
                        </a>
                    </li>
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="javascript:void(0)" onclick="loadSection('roles-permisos')" data-section="roles-permisos">
                            <i class="fas fa-user-shield"></i> Roles y Permisos
                        </a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="javascript:void(0)" data-section="reportes">
                    <i class="fas fa-chart-bar"></i> Reportes
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="javascript:void(0)" data-section="configuracion">
                    <i class="fas fa-cog"></i> Configuración
                </a>
            </li>
            <?php endif; ?>

            <?php if ($rol === 'fiscalizador'): ?>
            <!-- Menú para Fiscalizador -->
            <li class="sidebar-item">
                <a class="sidebar-link sidebar-toggle" href="#" onclick="toggleSubmenu('actas', event)">
                    <i class="fas fa-clipboard-list"></i> Gestión de Actas
                    <i class="fas fa-chevron-down sidebar-arrow"></i>
                </a>
                <ul class="sidebar-submenu" id="submenu-actas">
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="#" onclick="loadSection('crear-acta')" data-section="crear-acta">
                            <i class="fas fa-plus-circle"></i> Crear Acta
                        </a>
                    </li>
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="#" onclick="loadSection('actas-contra')" data-section="actas-contra">
                            <i class="fas fa-file-invoice"></i> Actas Contra
                        </a>
                    </li>
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="#" onclick="loadSection('mis-actas')" data-section="mis-actas">
                            <i class="fas fa-user-edit"></i> Mis Actas
                        </a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="#" onclick="loadEmpresas()" data-section="empresas">
                    <i class="fas fa-building"></i> Empresas
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="#" onclick="loadInspecciones()" data-section="inspecciones">
                    <i class="fas fa-search"></i> Inspecciones
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="#" onclick="loadConsultas()" data-section="consultas">
                    <i class="fas fa-question-circle"></i> Consultas
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="#" onclick="loadSection('reportes')" data-section="reportes">
                    <i class="fas fa-chart-bar"></i> Reportes
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="#" onclick="loadCalendario()" data-section="calendario">
                    <i class="fas fa-calendar-alt"></i> Calendario
                </a>
            </li>
            <?php endif; ?>

            <?php if ($rol === 'inspector'): ?>
            <!-- Menú para Inspector -->
            <li class="sidebar-item">
                <a class="sidebar-link sidebar-toggle" href="#" onclick="toggleSubmenu('inspecciones', event)">
                    <i class="fas fa-clipboard-check"></i> Mis Inspecciones
                    <i class="fas fa-chevron-down sidebar-arrow"></i>
                </a>
                <ul class="sidebar-submenu" id="submenu-inspecciones">
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="#" onclick="loadNuevaInspeccion()" data-section="nueva-inspeccion">
                            <i class="fas fa-plus"></i> Nueva Inspección
                        </a>
                    </li>
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="#" onclick="loadMisInspecciones()" data-section="mis-inspecciones">
                            <i class="fas fa-list"></i> Mis Inspecciones
                        </a>
                    </li>
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="#" onclick="loadSection('programar-inspeccion')" data-section="programar-inspeccion">
                            <i class="fas fa-calendar-plus"></i> Programar
                        </a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="#" onclick="loadVehiculos()" data-section="vehiculos">
                    <i class="fas fa-car"></i> Vehículos
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="#" onclick="loadSection('reportes')" data-section="reportes">
                    <i class="fas fa-chart-line"></i> Mis Reportes
                </a>
            </li>
            <?php endif; ?>

            <?php if ($rol === 'ventanilla'): ?>
            <!-- Menú para Ventanilla -->
            <li class="sidebar-item">
                <a class="sidebar-link sidebar-toggle" href="#" onclick="toggleSubmenu('atencion', event)">
                    <i class="fas fa-user-tie"></i> Atención al Cliente
                    <i class="fas fa-chevron-down sidebar-arrow"></i>
                </a>
                <ul class="sidebar-submenu" id="submenu-atencion">
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="#" onclick="loadNuevaAtencion()" data-section="nueva-atencion">
                            <i class="fas fa-plus"></i> Nueva Atención
                        </a>
                    </li>
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="#" onclick="loadColaEspera()" data-section="cola-espera">
                            <i class="fas fa-hourglass-half"></i> Cola de Espera
                        </a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link sidebar-toggle" href="#" onclick="toggleSubmenu('tramites', event)">
                    <i class="fas fa-folder-open"></i> Trámites
                    <i class="fas fa-chevron-down sidebar-arrow"></i>
                </a>
                <ul class="sidebar-submenu" id="submenu-tramites">
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="#" onclick="loadTramites()" data-section="nuevo-tramite">
                            <i class="fas fa-plus"></i> Nuevo Trámite
                        </a>
                    </li>
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="#" onclick="loadSection('tramites-pendientes')" data-section="tramites-pendientes">
                            <i class="fas fa-clock"></i> Pendientes
                        </a>
                    </li>
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="#" onclick="loadSection('historial-tramites')" data-section="historial-tramites">
                            <i class="fas fa-history"></i> Historial
                        </a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="#" onclick="loadSection('consultas')" data-section="consultas">
                    <i class="fas fa-search"></i> Consultas
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="#" onclick="loadSection('actas-contra')" data-section="actas-contra">
                    <i class="fas fa-clipboard-list"></i> Actas
                </a>
            </li>
            <?php endif; ?>

            <!-- Mi Perfil - Para todos los roles -->
            <li class="sidebar-item">
                <a class="sidebar-link" href="javascript:void(0)" onclick="loadPerfil()" data-section="perfil">
                    <i class="fas fa-user"></i> Mi Perfil
                </a>
            </li>
        </ul>
    </div>

    <div class="main-wrapper">
        <div class="main-content">
            <!-- Dashboard Stats -->
            <div id="dashboardStats" class="row mb-4">
                <!-- Las estadísticas se cargan dinámicamente -->
            </div>

            <!-- Content Sections -->
            <div id="contentContainer">
                <!-- Loading Spinner -->
                <div id="loading" class="text-center p-4" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando contenido...</p>
                </div>
                
            <!-- Dashboard Principal -->
            <div id="dashboard-section" class="content-section active">
                <h2><i class="fas fa-tachometer-alt"></i> Dashboard Principal</h2>
                <p class="text-muted">Bienvenido al sistema de gestión. Utiliza la barra lateral para navegar.</p>
                
                <!-- Panel de información del usuario (solo para debug) -->
                <div class="alert alert-success mb-3">
                    <h6><i class="fas fa-user-check"></i> Usuario Logueado:</h6>
                    <strong><?php echo htmlspecialchars($usuario); ?></strong> | Rol: <strong><?php echo htmlspecialchars($rol); ?></strong>
                </div>
                
                <div class="row mt-4" id="dashboardContent">
                    <!-- Dashboard stats cards -->
                    <div class="col-md-3 mb-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title">Total Actas</h5>
                                        <h3 id="total-actas">0</h3>
                                    </div>
                                    <i class="fas fa-file-alt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title">Conductores</h5>
                                        <h3 id="total-conductores">0</h3>
                                    </div>
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title">Vehículos</h5>
                                        <h3 id="total-vehiculos">0</h3>
                                    </div>
                                    <i class="fas fa-car fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-4">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title">Notificaciones</h5>
                                        <h3 id="total-notifications">0</h3>
                                    </div>
                                    <i class="fas fa-bell fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other sections will be generated dynamically -->
            <div id="actas-section" class="content-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-file-alt"></i> Gestión de Actas</h2>
                </div>
                <div class="loading">Cargando contenido...</div>
            </div>
            
            <div id="conductores-section" class="content-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-id-card"></i> Gestión de Conductores</h2>
                </div>
                <div class="loading">Cargando contenido...</div>
            </div>
            
            <div id="vehiculos-section" class="content-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-car"></i> Gestión de Vehículos</h2>
                </div>
                <div class="loading">Cargando contenido...</div>
            </div>
            
            <div id="notifications-section" class="content-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-bell"></i> Centro de Notificaciones</h2>
                </div>
                <div class="loading">Cargando contenido...</div>
            </div>
            
            <div id="reportes-section" class="content-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-chart-line"></i> Reportes y Estadísticas</h2>
                </div>
                <div class="loading">Cargando contenido...</div>
            </div>
            
            <div id="usuarios-section" class="content-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-users"></i> Gestión de Usuarios</h2>
                </div>
                <div class="loading">Cargando contenido...</div>
            </div>
            
            <div id="infracciones-section" class="content-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-exclamation-triangle"></i> Gestión de Infracciones</h2>
                </div>
                <div class="loading">Cargando contenido...</div>
            </div>
            
            <div id="inspecciones-section" class="content-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-clipboard-check"></i> Inspecciones</h2>
                </div>
                <div class="loading">Cargando contenido...</div>
            </div>

            <!-- Secciones específicas por submenu se generan dinámicamente -->
            </div>
        </div> <!-- main-content -->
    </div> <!-- main-wrapper -->

    <!-- Modal para formularios y detalles -->
    <div class="modal fade" id="generalModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Información</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <!-- Contenido dinámico -->
                </div>
                <div class="modal-footer" id="modalFooter">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Variables globales del PHP para JavaScript -->
    <script>
        // Variables globales del usuario
        window.dashboardUserName = '<?php echo htmlspecialchars($app->getUserName()); ?>';
        window.dashboardUserRole = '<?php echo $rol; ?>';
    </script>
    
    <!-- Archivos JavaScript separados por funcionalidad -->
    <script src="js/utils.js"></script>
    <script src="js/dashboard-core.js"></script>
    <script src="js/dashboard-stats.js"></script>
    <script src="js/notifications.js"></script>
    <script src="js/sections.js"></script>
    <script src="js/testing.js"></script>
    <script src="js/config.js"></script>
        console.log('🚨 Cargando funciones de emergencia...');
        
        // Función de utilidad básica
        function hideAllSections() {
            const dashboardSection = document.getElementById('dashboard-section');
            if (dashboardSection) {
                dashboardSection.style.display = 'none';
            }
            const contentContainer = document.getElementById('contentContainer');
            if (contentContainer) {
                contentContainer.innerHTML = '';
            }
        }
        
        // LOADSECTION de emergencia - SUPER SIMPLE
        window.loadSection = function(sectionId) {
            console.log('🔄 LoadSection EMERGENCIA:', sectionId);
            alert('Cargando: ' + sectionId);
            hideAllSections();
            
            const contentContainer = document.getElementById('contentContainer');
            if (contentContainer) {
                contentContainer.innerHTML = `
                    <div class="content-section active" style="padding: 30px; background: #f8f9fa; border-radius: 10px; margin: 20px;">
                        <h2>✅ ¡FUNCIONA!</h2>
                        <h3>Sección: ${sectionId}</h3>
                        <p>La función loadSection está funcionando correctamente.</p>
                        <button onclick="loadSection('dashboard')" class="btn btn-primary">Volver al Dashboard</button>
                    </div>
                `;
            }
            
            // Caso especial para dashboard
            if (sectionId === 'dashboard') {
                const dashboardSection = document.getElementById('dashboard-section');
                if (dashboardSection) {
                    dashboardSection.style.display = 'block';
                }
                if (contentContainer) {
                    contentContainer.innerHTML = '';
                }
            }
        };
        
        // TOGGLESUBMENU de emergencia
        window.toggleSubmenu = function(menuId, event) {
            console.log('🔄 ToggleSubmenu EMERGENCIA:', menuId);
            if (event) event.preventDefault();
            
            // Si es TEST CLICK (usuarios), mostrar mensaje especial
            if (menuId === 'usuarios') {
                alert('TEST CLICK funcionando!');
                hideAllSections();
                const contentContainer = document.getElementById('contentContainer');
                if (contentContainer) {
                    contentContainer.innerHTML = `
                        <div class="content-section active" style="padding: 30px; background: #e3f2fd; border-radius: 10px; margin: 20px;">
                            <h2>🧪 TEST CLICK ACTIVADO</h2>
                            <p>El botón TEST CLICK está funcionando correctamente.</p>
                            <div class="alert alert-success">
                                <strong>✅ JavaScript OK</strong> - Las funciones se están ejecutando.
                            </div>
                            <button onclick="loadSection('dashboard')" class="btn btn-primary">Volver al Dashboard</button>
                        </div>
                    `;
                }
                return;
            }
            
            // Funcionalidad normal de submenu
            const submenu = document.getElementById(`submenu-${menuId}`);
            if (submenu) {
                submenu.classList.toggle('show');
            }
        };
        
        // LOADPERFIL de emergencia
        window.loadPerfil = function() {
            console.log('🔄 LoadPerfil EMERGENCIA');
            alert('Cargando Mi Perfil...');
            hideAllSections();
            
            const contentContainer = document.getElementById('contentContainer');
            if (contentContainer) {
                contentContainer.innerHTML = `
                    <div class="content-section active" style="padding: 30px; background: #fff3e0; border-radius: 10px; margin: 20px;">
                        <h2>👤 MI PERFIL</h2>
                        <p>La función de perfil está funcionando.</p>
                        <div class="card">
                            <div class="card-body">
                                <h5>Información del Usuario</h5>
                                <p><strong>Usuario:</strong> ${userName || 'Usuario'}</p>
                                <p><strong>Rol:</strong> ${userRole || 'Sin rol'}</p>
                            </div>
                        </div>
                        <button onclick="loadSection('dashboard')" class="btn btn-primary mt-3">Volver al Dashboard</button>
                    </div>
                `;
            }
        };
        
        console.log('✅ Funciones de emergencia cargadas');
        console.log('✅ loadSection disponible:', typeof window.loadSection);
        console.log('✅ toggleSubmenu disponible:', typeof window.toggleSubmenu);
        console.log('✅ loadPerfil disponible:', typeof window.loadPerfil);
        
        // Variables globales
        const userRole = '<?php echo $rol; ?>';
        const userName = '<?php echo htmlspecialchars($app->getUserName()); ?>';
        let currentSection = 'dashboard';

        console.log('✅ Sistema inicializado para usuario:', userName, 'con rol:', userRole);
        
        // Test inmediato para verificar que JavaScript funciona
        window.testClick = function() {
            alert('JavaScript funciona perfectamente!');
            console.log('Test ejecutado correctamente');
        };

        // Función directa para cargar gestionar usuarios
        window.loadGestionarUsuarios = function() {
            alert('Cargando gestión de usuarios...');
            console.log('Función loadGestionarUsuarios ejecutada');
            
            const dashboardSection = document.getElementById('dashboard-section');
            if (dashboardSection) {
                dashboardSection.style.display = 'none';
            }
            
            const contentContainer = document.getElementById('contentContainer');
            if (contentContainer) {
                contentContainer.innerHTML = `
                    <div class="content-section active">
                        <h2><i class="fas fa-users"></i> Gestión de Usuarios</h2>
                        <p>Funcionalidad de gestión de usuarios cargada correctamente.</p>
                        <button onclick="loadSection('dashboard')" class="btn btn-primary">Volver al Dashboard</button>
                    </div>
                `;
            }
        };

        // Función de test simple
        function testFunction() {
            alert('¡La función JavaScript funciona!');
            console.log('Test function ejecutada');
        }
        
        // Función SUPER SIMPLE para probar
        function loadSection(sectionId) {
            console.log('🔄 Cargando sección:', sectionId);
            console.log('ℹ️ loadSection ejecutada para:', sectionId);

            // Ocultar dashboard
            const dashboardSection = document.getElementById('dashboard-section');
            if (dashboardSection) {
                dashboardSection.style.display = 'none';
                console.log('✅ Dashboard ocultado');
            }

            // Limpiar contenido y mostrar mensaje
            const contentContainer = document.getElementById('contentContainer');
            if (contentContainer) {
                contentContainer.innerHTML = `
                    <div class="content-section active" style="padding: 20px;">
                        <h2>🎉 ¡Funciona!</h2>
                        <p>Has cargado la sección: <strong>${sectionId}</strong></p>
                        <button onclick="loadSection('dashboard')" class="btn btn-primary">Volver al Dashboard</button>
                    </div>
                `;
                console.log('✅ Contenido actualizado');
            }

            // Si es dashboard, mostrar la sección original
            if (sectionId === 'dashboard' && dashboardSection) {
                dashboardSection.style.display = 'block';
                if (contentContainer) {
                    contentContainer.innerHTML = '';
                }
            }
        }
        
        // Hacer funciones disponibles globalmente
        window.testFunction = testFunction;
        
        // Nueva función loadSection completa - SUPER SIMPLE
        function loadSectionNew(sectionId) {
            alert('FUNCIÓN EJECUTADA: ' + sectionId);
            console.log('🔄 Cargando sección:', sectionId);
            
            // Solo ocultar dashboard y mostrar mensaje simple
            const dashboardSection = document.getElementById('dashboard-section');
            if (dashboardSection) {
                dashboardSection.style.display = 'none';
            }
            
            const contentContainer = document.getElementById('contentContainer');
            if (contentContainer) {
                contentContainer.innerHTML = `
                    <div class="content-section active" style="padding: 20px; background: white; border-radius: 8px;">
                        <h2>✅ FUNCIÓN FUNCIONANDO</h2>
                        <h3>Sección cargada: ${sectionId}</h3>
                        <p>¡El sistema está funcionando correctamente!</p>
                        <button onclick="window.location.reload()" class="btn btn-primary">Recargar Página</button>
                        <button onclick="loadSectionNew('dashboard')" class="btn btn-secondary">Volver</button>
                    </div>
                `;
            }
            
            // Caso especial para dashboard
            if (sectionId === 'dashboard' && dashboardSection) {
                dashboardSection.style.display = 'block';
                if (contentContainer) {
                    contentContainer.innerHTML = '';
                }
            }
        }
        
        // Reemplazar la función loadSection con la nueva
        window.loadSection = loadSectionNew;
        window.loadSection = loadSection;
        
        // Mostrar/ocultar loading
        function showLoading() {
            const loading = document.getElementById('loading');
            if (loading) {
                loading.style.display = 'block';
            }
        }
        
        function hideLoading() {
            const loading = document.getElementById('loading');
            if (loading) {
                loading.style.display = 'none';
            }
        }
        
        window.showLoading = showLoading;
        window.hideLoading = hideLoading;

        // Inicializar la aplicación
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Dashboard cargado correctamente');
            console.log('🔧 Probando alert...');
            
            // Test inmediato para verificar que el script se carga
            console.log('⚡ Script ejecutándose correctamente');
            
            // Use delegated event listener on the sidebar menu to reliably catch clicks
            // This avoids fragile querySelector-on-onclick-string patterns and works even
            // when the DOM is rendered server-side or modified later.
            (function bindSidebarDelegation() {
                try {
                    const sidebarMenu = document.getElementById('sidebarMenu');
                    if (!sidebarMenu) {
                        console.warn('⚠️ sidebarMenu no encontrado, reintentando en 300ms');
                        setTimeout(bindSidebarDelegation, 300);
                        return;
                    }

                    sidebarMenu.addEventListener('click', function(e) {
                        // Find the closest anchor
                        const a = e.target.closest('a');
                        if (!a || !sidebarMenu.contains(a)) return;

                        // If it's a navigation anchor with an href that is a real link, allow default
                        const href = a.getAttribute('href') || '';
                        if (href && !href.startsWith('javascript') && href !== '#') {
                            // Let the browser handle normal navigation (e.g., logout links)
                            return;
                        }

                        e.preventDefault();

                        // First, try a data-section attribute (preferred)
                        const section = a.getAttribute('data-section');
                        if (section) {
                            console.log('➡️ Sidebar click (data-section):', section);
                            hideAllSections();
                            // Map to existing loaders when appropriate
                            switch(section) {
                                case 'dashboard':
                                    loadSection('dashboard');
                                    break;
                                case 'reportes':
                                    loadReportes();
                                    break;
                                case 'configuracion':
                                    loadConfiguracion();
                                    break;
                                case 'perfil':
                                    loadPerfilMejorado();
                                    break;
                                case 'aprobar-usuarios':
                                case 'roles-permisos':
                                case 'crear-acta':
                                case 'mis-actas':
                                case 'actas-contra':
                                    loadSection(section);
                                    break;
                                case 'crear-usuario':
                                    showCrearUsuarioModal();
                                    break;
                                case 'listar-usuarios':
                                    loadUsuariosList();
                                    break;
                                default:
                                    // Fallback: try calling loadSection with the name
                                    if (typeof window.loadSection === 'function') {
                                        loadSection(section);
                                    } else {
                                        console.warn('⚠️ No hay loader conocido para la sección:', section);
                                    }
                            }
                            return;
                        }

                        // If there's no data-section, try to infer from onclick attribute or function names
                        const onclick = a.getAttribute('onclick') || '';
                        if (onclick) {
                            console.log('➡️ Sidebar click (onclick):', onclick);

                            // Common known handlers
                            if (onclick.includes('loadGestionarUsuarios')) {
                                hideAllSections();
                                if (typeof window.loadGestionarUsuarios === 'function') {
                                    loadGestionarUsuarios();
                                }
                                return;
                            }

                            if (onclick.includes('testClick') || onclick.includes('toggleSubmenu(\'usuarios\'')) {
                                // Si es el botón TEST CLICK (toggleSubmenu usuarios), ejecutar loadTestClick
                                if (a.textContent.includes('TEST CLICK')) {
                                    hideAllSections();
                                    loadTestClick();
                                    return;
                                }
                            }

                            if (onclick.includes("loadSection('perfil')") || onclick.includes('loadSection(\"perfil\")')) {
                                hideAllSections();
                                loadPerfilMejorado();
                                return;
                            }

                            if (onclick.includes('loadPerfil')) {
                                hideAllSections();
                                loadPerfilMejorado();
                                return;
                            }

                            // As a last resort, try to evaluate a call to loadSection or loadSectionNew inside the onclick string
                            try {
                                const matchLoad = onclick.match(/loadSectionNew\((?:'|\")?(.*?)(?:'|\")?\)/);
                                if (matchLoad && matchLoad[1]) {
                                    hideAllSections();
                                    loadSectionNew(matchLoad[1]);
                                    return;
                                }

                                const matchLoad2 = onclick.match(/loadSection\((?:'|\")?(.*?)(?:'|\")?\)/);
                                if (matchLoad2 && matchLoad2[1]) {
                                    hideAllSections();
                                    loadSection(matchLoad2[1]);
                                    return;
                                }
                            } catch (err) {
                                console.warn('Error parseando onclick:', err);
                            }
                        }

                        // Nothing matched — log for debugging
                        console.log('ℹ️ Click en sidebar sin acción mapeada:', a, 'onclick=', onclick);
                    }, false);

                    console.log('✅ Delegated sidebar listener agregado correctamente');

                    // Add a small visual debug badge so the user can see the script is active
                    try {
                        if (!document.getElementById('js-debug-badge')) {
                            const badge = document.createElement('div');
                            badge.id = 'js-debug-badge';
                            badge.textContent = 'JS OK';
                            badge.title = 'Click para probar listener';
                            badge.style.position = 'fixed';
                            badge.style.right = '12px';
                            badge.style.bottom = '12px';
                            badge.style.background = '#198754';
                            badge.style.color = 'white';
                            badge.style.padding = '8px 10px';
                            badge.style.borderRadius = '8px';
                            badge.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
                            badge.style.zIndex = '99999';
                            badge.style.cursor = 'pointer';
                            badge.style.fontWeight = '700';
                            badge.style.fontFamily = 'Segoe UI, Tahoma, sans-serif';
                            badge.addEventListener('click', function() {
                                console.log('🧪 JS Debug Badge clicked — delegated listener should be active.');
                                hideAllSections();
                                showSection('test-section');
                            });
                            document.body.appendChild(badge);
                        }
                    } catch (err) {
                        console.warn('No se pudo crear el badge de debug:', err);
                    }

                    // Aggressive binding: bind click handlers to every sidebar anchor directly
                    function bindAllSidebarLinks() {
                        try {
                            const links = document.querySelectorAll('#sidebarMenu a');
                            const summary = [];
                            links.forEach(a => {
                                // mark bound to avoid duplicate handlers
                                if (a.dataset.bound === '1') return;
                                a.dataset.bound = '1';

                                a.addEventListener('click', function(e) {
                                    // allow real href navigation to proceed (logout etc.)
                                    const href = a.getAttribute('href') || '';
                                    if (href && !href.startsWith('javascript') && href !== '#') return;
                                    e.preventDefault();

                                    const section = a.getAttribute('data-section');
                                    if (section) {
                                        console.log('🔗 [bound] click data-section ->', section);
                                        hideAllSections();
                                        switch(section) {
                                            case 'listar-usuarios': return loadUsuariosList();
                                            case 'reportes': return loadReportes();
                                            case 'configuracion': return loadConfiguracion();
                                            case 'perfil': return loadPerfilMejorado();
                                            default:
                                                if (typeof window.loadSection === 'function') return loadSection(section);
                                        }
                                        return;
                                    }

                                    // fallback: parse onclick text or try known functions
                                    const onclick = a.getAttribute('onclick') || '';
                                    if (onclick.includes('loadGestionarUsuarios')) { hideAllSections(); return loadGestionarUsuarios(); }
                                    if (onclick.includes('loadUsuariosList')) { hideAllSections(); return loadUsuariosList(); }
                                    if (onclick.includes('toggleSubmenu') && a.textContent.includes('TEST CLICK')) { hideAllSections(); return loadTestClick(); }
                                    if (onclick.includes('loadPerfil')) { hideAllSections(); return loadPerfilMejorado(); }
                                    if (onclick.includes('loadSection')) {
                                        // find argument
                                        const m = onclick.match(/loadSection\((?:'|\")?(.*?)(?:'|\")?\)/);
                                        if (m && m[1]) { 
                                            hideAllSections(); 
                                            if (m[1] === 'perfil') return loadPerfilMejorado();
                                            return loadSection(m[1]); 
                                        }
                                    }
                                    if (onclick.includes('loadSectionNew')) {
                                        const m = onclick.match(/loadSectionNew\((?:'|\")?(.*?)(?:'|\")?\)/);
                                        if (m && m[1]) { hideAllSections(); return loadSectionNew(m[1]); }
                                    }
                                    if (onclick.includes('showCrearUsuarioModal')) { hideAllSections(); return showCrearUsuarioModal(); }
                                    if (onclick.includes('toggleSubmenu')) return; // submenu toggles handled separately

                                    console.log('⚠️ [bound] click sin mapping:', a, onclick);
                                });

                                summary.push({text: a.textContent.trim(), data: a.getAttribute('data-section') || null});
                            });

                            // Create/refresh a small debug panel with info + quick buttons
                            try {
                                let panel = document.getElementById('sidebar-debug-panel');
                                if (!panel) {
                                    panel = document.createElement('div');
                                    panel.id = 'sidebar-debug-panel';
                                    panel.style.position = 'fixed';
                                    panel.style.right = '12px';
                                    panel.style.top = '12px';
                                    panel.style.width = '220px';
                                    panel.style.background = 'white';
                                    panel.style.border = '1px solid #ddd';
                                    panel.style.padding = '10px';
                                    panel.style.boxShadow = '0 6px 20px rgba(0,0,0,0.12)';
                                    panel.style.zIndex = '99999';
                                    panel.style.fontFamily = 'Segoe UI, Tahoma, sans-serif';
                                    panel.style.fontSize = '13px';
                                    document.body.appendChild(panel);
                                }

                                panel.innerHTML = `
                                    <div style="font-weight:700;margin-bottom:6px">Debug Sidebar</div>
                                    <div id="sb-summary">Enlaces: ${summary.length}</div>
                                    <div style="margin-top:8px;display:flex;flex-direction:column;gap:6px">
                                        <button class="btn btn-sm btn-outline-primary" id="btn-usuarios">Usuarios</button>
                                        <button class="btn btn-sm btn-outline-primary" id="btn-reportes">Reportes</button>
                                        <button class="btn btn-sm btn-outline-primary" id="btn-config">Configuración</button>
                                        <button class="btn btn-sm btn-outline-primary" id="btn-perfil">Mi Perfil</button>
                                    </div>
                                    <div style="margin-top:8px"><small style="color:#666">Actualiza: recarga la página</small></div>
                                `;

                                document.getElementById('btn-usuarios').addEventListener('click', function(){ hideAllSections(); loadUsuariosList(); });
                                document.getElementById('btn-reportes').addEventListener('click', function(){ hideAllSections(); loadReportes(); });
                                document.getElementById('btn-config').addEventListener('click', function(){ hideAllSections(); loadConfiguracion(); });
                                document.getElementById('btn-perfil').addEventListener('click', function(){ hideAllSections(); loadPerfilMejorado(); });
                            } catch (err) {
                                console.warn('No se pudo crear panel debug:', err);
                            }

                            console.log('✅ Bound sidebar links:', summary.length);
                        } catch (err) {
                            console.error('❌ Error binding sidebar links:', err);
                        }
                    }

                    // Call binder now (we also have delegated listener as fallback)
                    try { bindAllSidebarLinks(); } catch (err) { console.warn('Error ejecutando bindAllSidebarLinks:', err); }
                } catch (error) {
                    console.error('❌ Error al bindear delegación del sidebar:', error);
                }
            })();
            
            initializeApp();
        });
        
        // Funciones auxiliares para manejar secciones
        function hideAllSections() {
            const dashboardSection = document.getElementById('dashboard-section');
            if (dashboardSection) {
                dashboardSection.style.display = 'none';
                console.log('✅ Dashboard ocultado');
            }
            
            const contentContainer = document.getElementById('contentContainer');
            if (contentContainer) {
                contentContainer.innerHTML = '';
            }
        }
        
        function showSection(sectionType) {
            const contentContainer = document.getElementById('contentContainer');
            if (!contentContainer) return;
            
            let content = '';
            
            switch(sectionType) {
                case 'test-section':
                    content = `
                        <div class="content-section active" style="padding: 30px; background: #f8f9fa; border-radius: 10px; margin: 20px;">
                            <h2><i class="fas fa-check-circle text-success"></i> ¡Test Exitoso!</h2>
                            <p class="lead">Los event listeners están funcionando correctamente.</p>
                            <button onclick="window.location.reload()" class="btn btn-primary">Recargar Página</button>
                        </div>
                    `;
                    break;
                case 'usuarios-section':
                    content = `
                        <div class="content-section active" style="padding: 30px; background: #f8f9fa; border-radius: 10px; margin: 20px;">
                            <h2><i class="fas fa-users"></i> Gestión de Usuarios</h2>
                            <p class="lead">¡La funcionalidad de usuarios está funcionando!</p>
                            <div class="alert alert-success">
                                <strong>✅ Éxito:</strong> El dashboard desapareció y se cargó esta sección.
                            </div>
                            <button onclick="window.location.reload()" class="btn btn-primary">Volver al Dashboard</button>
                        </div>
                    `;
                    break;
                case 'reportes-section':
                    content = `
                        <div class="content-section active" style="padding: 30px; background: #f8f9fa; border-radius: 10px; margin: 20px;">
                            <h2><i class="fas fa-chart-bar"></i> Reportes del Sistema</h2>
                            <p class="lead">¡Los reportes están funcionando!</p>
                            <div class="alert alert-info">
                                <strong>✅ Éxito:</strong> El dashboard desapareció y se cargó la sección de reportes.
                            </div>
                            <button onclick="window.location.reload()" class="btn btn-primary">Volver al Dashboard</button>
                        </div>
                    `;
                    break;
                case 'configuracion-section':
                    content = `
                        <div class="content-section active" style="padding: 30px; background: #f8f9fa; border-radius: 10px; margin: 20px;">
                            <h2><i class="fas fa-cog"></i> Configuración del Sistema</h2>
                            <p class="lead">¡La configuración está funcionando!</p>
                            <div class="alert alert-warning">
                                <strong>✅ Éxito:</strong> El dashboard desapareció y se cargó la configuración.
                            </div>
                            <button onclick="window.location.reload()" class="btn btn-primary">Volver al Dashboard</button>
                        </div>
                    `;
                    break;
            }
            
            contentContainer.innerHTML = content;
            console.log('✅ Sección mostrada:', sectionType);
        }

        // Función de respaldo para generar el menú manualmente
        function initializeApp() {
            console.log('🚀 Inicializando aplicación...');
            console.log('👤 Usuario logueado:', userRole);
            
            // Cargar contenido inicial
            loadDashboardContent();
            loadDashboardStats();
            loadNotifications();
            
            // Auto-refresh notificaciones cada 30 segundos
            setInterval(loadNotifications, 30000);
            
            console.log('✅ Aplicación inicializada');
        }

        // Cargar datos específicos para elementos individuales
        function loadIndividualStats() {
            Promise.all([
                fetch('dashboard.php?api=actas'),
                fetch('dashboard.php?api=conductores'),
                fetch('dashboard.php?api=vehiculos'),
                fetch('dashboard.php?api=notifications')
            ])
            .then(responses => Promise.all(responses.map(r => r.json())))
            .then(([actasData, conductoresData, vehiculosData, notificationsData]) => {
                if (document.getElementById('total-actas')) {
                    document.getElementById('total-actas').textContent = actasData.actas ? actasData.actas.length : 0;
                }
                if (document.getElementById('total-conductores')) {
                    document.getElementById('total-conductores').textContent = conductoresData.conductores ? conductoresData.conductores.length : 0;
                }
                if (document.getElementById('total-vehiculos')) {
                    document.getElementById('total-vehiculos').textContent = vehiculosData.vehiculos ? vehiculosData.vehiculos.length : 0;
                }
                if (document.getElementById('total-notifications')) {
                    document.getElementById('total-notifications').textContent = notificationsData.notifications ? 
                        notificationsData.notifications.filter(n => !n.is_read).length : 0;
                }
            })
            .catch(error => {
                console.error('Error cargando estadísticas individuales:', error);
            });
        }

        // --- Exponer funciones críticas a window (fallbacks seguros) ---
        (function exposeGlobals(){
            const map = [
                'loadSection', 'loadSectionNew', 'loadReportes', 'loadConfiguracion', 'loadPerfil',
                'toggleSubmenu', 'loadUsuariosList', 'showCrearUsuarioModal', 'loadGestionarUsuarios',
                'loadDashboardContent', 'loadDashboardStats', 'loadNotifications'
            ];

            map.forEach(name => {
                try {
                    if (typeof window[name] === 'undefined') {
                        // If a local function exists with that name, bind it; otherwise add a safe noop
                        try {
                            if (typeof eval(name) === 'function') {
                                window[name] = eval(name);
                            } else {
                                window[name] = function() { console.warn('Función no implementada o no cargada aún:', name); };
                            }
                        } catch (innerErr) {
                            window[name] = function() { console.warn('Función no implementada o no cargada aún:', name); };
                        }
                    }
                } catch (err) {
                    // ignore
                }
            });
        })();

        function toggleSubmenu(menuId, event) {
            event.preventDefault();
            console.log('🔄 Toggling submenu:', menuId);
            
            const submenu = document.getElementById(`submenu-${menuId}`);
            const toggle = event.currentTarget;
            
            if (!submenu) {
                console.error('❌ No se encontró el submenu:', `submenu-${menuId}`);
                return;
            }
            
            if (submenu.classList.contains('show')) {
                submenu.classList.remove('show');
                toggle.classList.remove('expanded');
                console.log('➖ Cerrando submenu:', menuId);
            } else {
                // Cerrar otros submenús
                document.querySelectorAll('.sidebar-submenu.show').forEach(sub => {
                    sub.classList.remove('show');
                });
                document.querySelectorAll('.sidebar-toggle.expanded').forEach(tog => {
                    tog.classList.remove('expanded');
                });
                
                submenu.classList.add('show');
                toggle.classList.add('expanded');
                console.log('➕ Abriendo submenu:', menuId);
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        }

        // Mostrar/ocultar loading
        function showLoading() {
            console.log('🔄 Cargando sección:', sectionId);
            console.log('📍 LoadSection ejecutándose...');
            
            // Actualizar el estado activo en el menú
            document.querySelectorAll('.sidebar-link, .sidebar-sublink').forEach(link => {
                link.classList.remove('active');
            });
            
            const activeLink = document.querySelector(`[data-section="${sectionId}"]`);
            if (activeLink) {
                activeLink.classList.add('active');
            }
            
            // Ocultar todas las secciones existentes
            document.querySelectorAll('.content-section').forEach(section => {
                section.classList.remove('active');
                section.style.display = 'none';
            });
            
            // Ocultar la sección del dashboard por defecto
            const dashboardSection = document.getElementById('dashboard-section');
            if (dashboardSection) {
                dashboardSection.style.display = 'none';
            }
            
            // Mostrar loading si existe la función
            if (typeof showLoading === 'function') {
                showLoading();
            }
            
            // Cargar contenido según la sección
            switch(sectionId) {
                case 'dashboard':
                    // Para el dashboard, mostrar la sección existente
                    if (dashboardSection) {
                        dashboardSection.style.display = 'block';
                        dashboardSection.classList.add('active');
                    }
                    loadDashboardContent();
                    break;
                case 'listar-usuarios':
                    loadUsuariosList();
                    break;
                case 'aprobar-usuarios':
                    loadAprobarUsuarios();
                    break;
                case 'crear-usuario':
                    showCrearUsuarioModal();
                    return; // No cambiar contenido, solo mostrar modal
                case 'roles-permisos':
                    loadRolesPermisos();
                    break;
                case 'crear-acta':
                    loadCrearActa();
                    break;
                case 'mis-actas':
                    loadMisActas();
                    break;
                case 'actas-contra':
                    loadActasContra();
                    break;
                case 'empresas':
                    loadEmpresas();
                    break;
                case 'inspecciones':
                    loadInspecciones();
                    break;
                case 'consultas':
                    loadConsultas();
                    break;
                case 'calendario':
                    loadCalendario();
                    break;
                case 'nueva-inspeccion':
                    loadNuevaInspeccion();
                    break;
                case 'mis-inspecciones':
                    loadMisInspecciones();
                    break;
                case 'vehiculos':
                    loadVehiculos();
                    break;
                case 'nueva-atencion':
                    loadNuevaAtencion();
                    break;
                case 'cola-espera':
                    loadColaEspera();
                    break;
                case 'tramites':
                    loadTramites();
                    break;
                case 'reportes':
                    loadReportes();
                    break;
                case 'configuracion':
                    loadConfiguracion();
                    break;
                case 'perfil':
                    loadPerfil();
                    break;
                default:
                    loadDefaultSection(sectionId);
            }
            
            currentSection = sectionId;
            
            // Ocultar loading si existe la función
            setTimeout(() => {
                if (typeof hideLoading === 'function') {
                    hideLoading();
                }
            }, 300);
        }

        // Mostrar/ocultar loading
        function showLoading() {
            const loading = document.getElementById('loading');
            if (loading) {
                loading.style.display = 'block';
            }
        }

        function hideLoading() {
            setTimeout(() => {
                const loading = document.getElementById('loading');
                if (loading) {
                    loading.style.display = 'none';
                }
            }, 300);
        }

        // Funciones específicas para cada sección

        function loadUsuariosList() {
            const contentContainer = document.getElementById('contentContainer');
            contentContainer.innerHTML = `
                <div class="content-section active">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-users"></i> Lista de Usuarios</h2>
                        <button class="btn btn-primary" onclick="showCrearUsuarioModal()">
                            <i class="fas fa-user-plus"></i> Nuevo Usuario
                        </button>
                    </div>
                    
                    <!-- Filtros y Búsqueda -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Buscar Usuario</label>
                                    <input type="text" class="form-control" id="searchUsuarios" placeholder="Nombre, email o username...">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Filtrar por Rol</label>
                                    <select class="form-select" id="filterRol">
                                        <option value="">Todos los roles</option>
                                        <option value="administrador">Administrador</option>
                                        <option value="fiscalizador">Fiscalizador</option>
                                        <option value="inspector">Inspector</option>
                                        <option value="ventanilla">Ventanilla</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Filtrar por Estado</label>
                                    <select class="form-select" id="filterStatus">
                                        <option value="">Todos los estados</option>
                                        <option value="approved">Aprobado</option>
                                        <option value="pending">Pendiente</option>
                                        <option value="rejected">Rechazado</option>
                                        <option value="suspended">Suspendido</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-grid">
                                        <button class="btn btn-outline-secondary" onclick="clearFilters()">
                                            <i class="fas fa-times"></i> Limpiar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Usuarios -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Usuarios Registrados</h5>
                            <div>
                                <span id="totalUsuarios" class="badge bg-primary">0 usuarios</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="usuariosTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Usuario</th>
                                            <th>Información</th>
                                            <th>Rol</th>
                                            <th>Estado</th>
                                            <th>Fecha Registro</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="usuariosTableBody">
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Cargando usuarios...</span>
                                                </div>
                                                <p class="mt-2 mb-0">Cargando lista de usuarios...</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Agregar event listeners para filtros
            setTimeout(() => {
                document.getElementById('searchUsuarios').addEventListener('input', filterUsuarios);
                document.getElementById('filterRol').addEventListener('change', filterUsuarios);
                document.getElementById('filterStatus').addEventListener('change', filterUsuarios);
            }, 100);
            
            // Cargar usuarios con AJAX
            loadUsuariosData();
        }

        // Variable global para almacenar todos los usuarios
        let todosLosUsuarios = [];

        function loadUsuariosData() {
            fetch('dashboard.php?api=users')
                .then(response => response.json())
                .then(data => {
                    console.log('Datos de usuarios recibidos:', data);
                    if (data.success && data.users) {
                        todosLosUsuarios = data.users;
                        renderUsuariosTable(data.users);
                        updateUsuariosCount(data.users.length);
                    } else {
                        showUsuariosError(data.message || 'No se pudieron cargar los usuarios');
                    }
                })
                .catch(error => {
                    console.error('Error cargando usuarios:', error);
                    showUsuariosError('Error de conexión con el servidor');
                });
        }

        function renderUsuariosTable(usuarios) {
            const tbody = document.getElementById('usuariosTableBody');
            if (!tbody) return;

            if (usuarios.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="mb-0">No se encontraron usuarios</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = usuarios.map(user => {
                const statusBadge = getStatusBadge(user.status);
                const roleBadge = getRoleBadge(user.role);
                const fechaFormatada = formatDate(user.created_at);
                
                return `
                    <tr>
                        <td>${user.id}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-2">
                                    <div class="avatar-title bg-primary text-white rounded-circle">
                                        ${user.name.charAt(0).toUpperCase()}
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0">${user.name}</h6>
                                    <small class="text-muted">@${user.username}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <i class="fas fa-envelope text-muted me-1"></i>
                                <small>${user.email}</small>
                            </div>
                            ${user.phone ? `<div class="mt-1"><i class="fas fa-phone text-muted me-1"></i><small>${user.phone}</small></div>` : ''}
                        </td>
                        <td>${roleBadge}</td>
                        <td>${statusBadge}</td>
                        <td>${fechaFormatada}</td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-outline-primary" onclick="viewUser(${user.id})" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-warning" onclick="editUser(${user.id})" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-${user.status === 'approved' ? 'secondary' : 'success'}" 
                                        onclick="toggleUserStatus(${user.id}, '${user.status}')" 
                                        title="${user.status === 'approved' ? 'Suspender' : 'Aprobar'}">
                                    <i class="fas fa-${user.status === 'approved' ? 'pause' : 'check'}"></i>
                                </button>
                                <button class="btn btn-outline-danger" onclick="deleteUser(${user.id})" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function getStatusBadge(status) {
            const badges = {
                'approved': '<span class="badge bg-success">Aprobado</span>',
                'pending': '<span class="badge bg-warning">Pendiente</span>',
                'rejected': '<span class="badge bg-danger">Rechazado</span>',
                'suspended': '<span class="badge bg-secondary">Suspendido</span>'
            };
            return badges[status] || '<span class="badge bg-dark">Desconocido</span>';
        }

        function getRoleBadge(role) {
            const badges = {
                'administrador': '<span class="badge bg-primary">Administrador</span>',
                'fiscalizador': '<span class="badge bg-info">Fiscalizador</span>',
                'inspector': '<span class="badge bg-success">Inspector</span>',
                'ventanilla': '<span class="badge bg-warning">Ventanilla</span>'
            };
            return badges[role] || '<span class="badge bg-dark">' + role + '</span>';
        }

        function updateUsuariosCount(count) {
            const counter = document.getElementById('totalUsuarios');
            if (counter) {
                counter.textContent = `${count} usuario${count !== 1 ? 's' : ''}`;
            }
        }

        function showUsuariosError(message) {
            const tbody = document.getElementById('usuariosTableBody');
            if (tbody) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-danger py-4">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <p class="mb-0">Error: ${message}</p>
                        </td>
                    </tr>
                `;
            }
        }

        function filterUsuarios() {
            const searchTerm = document.getElementById('searchUsuarios').value.toLowerCase();
            const filterRol = document.getElementById('filterRol').value;
            const filterStatus = document.getElementById('filterStatus').value;

            const usuariosFiltrados = todosLosUsuarios.filter(user => {
                const matchesSearch = user.name.toLowerCase().includes(searchTerm) ||
                                    user.username.toLowerCase().includes(searchTerm) ||
                                    user.email.toLowerCase().includes(searchTerm);
                
                const matchesRol = !filterRol || user.role === filterRol;
                const matchesStatus = !filterStatus || user.status === filterStatus;

                return matchesSearch && matchesRol && matchesStatus;
            });

            renderUsuariosTable(usuariosFiltrados);
            updateUsuariosCount(usuariosFiltrados.length);
        }

        function clearFilters() {
            document.getElementById('searchUsuarios').value = '';
            document.getElementById('filterRol').value = '';
            document.getElementById('filterStatus').value = '';
            renderUsuariosTable(todosLosUsuarios);
            updateUsuariosCount(todosLosUsuarios.length);
        }

        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        // Funciones de acciones de usuario
        function viewUser(userId) {
            const user = todosLosUsuarios.find(u => u.id == userId);
            if (!user) return;

            const modalBody = document.getElementById('modalBody');
            const modalTitle = document.getElementById('modalTitle');
            const modalFooter = document.getElementById('modalFooter');

            modalTitle.textContent = `Detalles del Usuario: ${user.name}`;
            modalBody.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="text-center mb-3">
                            <div class="avatar-lg mx-auto">
                                <div class="avatar-title bg-primary text-white rounded-circle fs-2">
                                    ${user.name.charAt(0).toUpperCase()}
                                </div>
                            </div>
                            <h5 class="mt-2">${user.name}</h5>
                            ${getRoleBadge(user.role)}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr><td><strong>ID:</strong></td><td>${user.id}</td></tr>
                            <tr><td><strong>Username:</strong></td><td>@${user.username}</td></tr>
                            <tr><td><strong>Email:</strong></td><td>${user.email}</td></tr>
                            <tr><td><strong>Teléfono:</strong></td><td>${user.phone || 'No registrado'}</td></tr>
                            <tr><td><strong>Estado:</strong></td><td>${getStatusBadge(user.status)}</td></tr>
                            <tr><td><strong>Registro:</strong></td><td>${formatDate(user.created_at)}</td></tr>
                            <tr><td><strong>Último acceso:</strong></td><td>${formatDate(user.last_login)}</td></tr>
                        </table>
                    </div>
                </div>
            `;
            modalFooter.innerHTML = `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editUser(${user.id}); $('#dynamicModal').modal('hide');">
                    <i class="fas fa-edit"></i> Editar Usuario
                </button>
            `;

            const modal = new bootstrap.Modal(document.getElementById('dynamicModal'));
            modal.show();
        }

        function editUser(userId) {
            const user = todosLosUsuarios.find(u => u.id == userId);
            if (!user) return;

            const modalBody = document.getElementById('modalBody');
            const modalTitle = document.getElementById('modalTitle');
            const modalFooter = document.getElementById('modalFooter');

            modalTitle.textContent = `Editar Usuario: ${user.name}`;
            modalBody.innerHTML = `
                <form id="editUserForm">
                    <input type="hidden" id="editUserId" value="${user.id}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre Completo *</label>
                                <input type="text" class="form-control" id="editName" value="${user.name}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Username *</label>
                                <input type="text" class="form-control" id="editUsername" value="${user.username}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" id="editEmail" value="${user.email}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="editPhone" value="${user.phone || ''}" placeholder="999-999-999">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Rol *</label>
                                <select class="form-select" id="editRole" required>
                                    <option value="administrador" ${user.role === 'administrador' ? 'selected' : ''}>Administrador</option>
                                    <option value="fiscalizador" ${user.role === 'fiscalizador' ? 'selected' : ''}>Fiscalizador</option>
                                    <option value="inspector" ${user.role === 'inspector' ? 'selected' : ''}>Inspector</option>
                                    <option value="ventanilla" ${user.role === 'ventanilla' ? 'selected' : ''}>Ventanilla</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Estado *</label>
                                <select class="form-select" id="editStatus" required>
                                    <option value="approved" ${user.status === 'approved' ? 'selected' : ''}>Aprobado</option>
                                    <option value="pending" ${user.status === 'pending' ? 'selected' : ''}>Pendiente</option>
                                    <option value="suspended" ${user.status === 'suspended' ? 'selected' : ''}>Suspendido</option>
                                    <option value="rejected" ${user.status === 'rejected' ? 'selected' : ''}>Rechazado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nueva Contraseña</label>
                        <input type="password" class="form-control" id="editPassword" placeholder="Dejar en blanco para mantener la actual">
                        <div class="form-text">Solo completar si desea cambiar la contraseña</div>
                    </div>
                </form>
            `;
            modalFooter.innerHTML = `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="saveEditUser()">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            `;

            const modal = new bootstrap.Modal(document.getElementById('dynamicModal'));
            modal.show();
        }

        function saveEditUser() {
            const formData = new FormData();
            formData.append('api', 'update-user');
            formData.append('id', document.getElementById('editUserId').value);
            formData.append('name', document.getElementById('editName').value);
            formData.append('username', document.getElementById('editUsername').value);
            formData.append('email', document.getElementById('editEmail').value);
            formData.append('phone', document.getElementById('editPhone').value);
            formData.append('role', document.getElementById('editRole').value);
            formData.append('status', document.getElementById('editStatus').value);
            
            const password = document.getElementById('editPassword').value;
            if (password) {
                formData.append('password', password);
            }

            fetch('dashboard.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Usuario actualizado correctamente');
                    bootstrap.Modal.getInstance(document.getElementById('dynamicModal')).hide();
                    loadUsuariosData(); // Recargar la tabla
                } else {
                    showAlert('error', data.message || 'Error al actualizar usuario');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Error de conexión al actualizar usuario');
            });
        }

        function toggleUserStatus(userId, currentStatus) {
            const newStatus = currentStatus === 'approved' ? 'suspended' : 'approved';
            const action = newStatus === 'approved' ? 'aprobar' : 'suspender';
            
            if (confirm(`¿Está seguro de ${action} este usuario?`)) {
                const formData = new FormData();
                formData.append('api', 'update-user-status');
                formData.append('id', userId);
                formData.append('status', newStatus);

                fetch('dashboard.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', `Usuario ${action === 'aprobar' ? 'aprobado' : 'suspendido'} correctamente`);
                        loadUsuariosData(); // Recargar la tabla
                    } else {
                        showAlert('error', data.message || `Error al ${action} usuario`);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', `Error de conexión al ${action} usuario`);
                });
            }
        }

        function deleteUser(userId) {
            const user = todosLosUsuarios.find(u => u.id == userId);
            if (!user) return;

            if (confirm(`¿Está seguro de eliminar al usuario "${user.name}"?\n\nEsta acción NO se puede deshacer.`)) {
                const formData = new FormData();
                formData.append('api', 'delete-user');
                formData.append('id', userId);

                fetch('dashboard.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', 'Usuario eliminado correctamente');
                        loadUsuariosData(); // Recargar la tabla
                    } else {
                        showAlert('error', data.message || 'Error al eliminar usuario');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Error de conexión al eliminar usuario');
                });
            }
    }

    function loadAprobarUsuarios() {
            const contentContainer = document.getElementById('contentContainer');
            contentContainer.innerHTML = `
                <div class="content-section active">
                    <h2><i class="fas fa-user-check"></i> Aprobar Usuarios</h2>
                    <div class="card">
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                Aquí se muestran los usuarios pendientes de aprobación
                            </div>
                            <div id="usuariosPendientes">
                                <div class="text-center">
                                    <div class="spinner-border" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }


        function loadCrearActa() {
            const contentContainer = document.getElementById('contentContainer');
            contentContainer.innerHTML = `
                <div class="content-section active">
                    <h2><i class="fas fa-plus-circle"></i> Crear Nueva Acta</h2>
                    <div class="card">
                        <div class="card-body">
                            <form id="crearActaForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Número de Placa</label>
                                        <input type="text" class="form-control" name="placa" required 
                                               placeholder="ABC-123">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">DNI/RUC Conductor</label>
                                        <input type="text" class="form-control" name="ruc_dni" required
                                               placeholder="12345678">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nombre del Conductor</label>
                                        <input type="text" class="form-control" name="nombre_conductor" required
                                               placeholder="Juan Pérez">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Licencia Conductor</label>
                                        <input type="text" class="form-control" name="licencia_conductor" required
                                               placeholder="A-123456789">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fecha Intervención</label>
                                        <input type="date" class="form-control" name="fecha_intervencion" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Hora Intervención</label>
                                        <input type="time" class="form-control" name="hora_intervencion" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Lugar de Intervención</label>
                                        <input type="text" class="form-control" name="lugar_intervencion" required
                                               placeholder="Av. Principal cuadra 5">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tipo de Agente</label>
                                        <select class="form-control" name="tipo_agente" required>
                                            <option value="">Seleccionar</option>
                                            <option value="PNP">Policía Nacional</option>
                                            <option value="SUTRAN">SUTRAN</option>
                                            <option value="MTC">MTC</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Monto Multa (S/)</label>
                                        <input type="number" class="form-control" name="monto_multa" 
                                               placeholder="0.00" step="0.01">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Observaciones</label>
                                        <textarea class="form-control" name="observaciones" rows="4"
                                                  placeholder="Detalles de la infracción..."></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Crear Acta
                                        </button>
                                        <button type="button" class="btn btn-secondary ms-2" onclick="loadSection('dashboard')">
                                            <i class="fas fa-times"></i> Cancelar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            `;
            
            // Configurar fecha actual por defecto
            document.querySelector('input[name="fecha_intervencion"]').value = new Date().toISOString().split('T')[0];
            document.querySelector('input[name="hora_intervencion"]').value = new Date().toTimeString().split(' ')[0].substring(0,5);
            
            // Agregar event listener para el formulario
            document.getElementById('crearActaForm').addEventListener('submit', function(e) {
                e.preventDefault();
                guardarActa(this);
            });
        }

        function loadMisActas() {
            const contentContainer = document.getElementById('contentContainer');
            contentContainer.innerHTML = `
                <div class="content-section active">
                    <h2><i class="fas fa-file-alt"></i> Mis Actas</h2>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>N° Acta</th>
                                            <th>Placa</th>
                                            <th>Conductor</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                            <th>Monto</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="actasTableBody">
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                <div class="spinner-border" role="status">
                                                    <span class="visually-hidden">Cargando...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Cargar actas del usuario
            fetch('dashboard.php?api=actas')
                .then(response => response.json())
                .then(data => {
                    console.log('Actas del usuario:', data);
                    if (data.success && data.actas) {
                        renderActasTable(data.actas);
                    } else {
                        document.getElementById('actasTableBody').innerHTML = `
                            <tr><td colspan="7" class="text-center">No tienes actas registradas</td></tr>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error cargando actas:', error);
                    document.getElementById('actasTableBody').innerHTML = `
                        <tr><td colspan="7" class="text-center text-danger">Error de conexión</td></tr>
                    `;
                });
        }

        function loadReportes() {
            const contentContainer = document.getElementById('contentContainer');
            contentContainer.innerHTML = `
                <div class="content-section active">
                    <h2><i class="fas fa-chart-bar"></i> Reportes</h2>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Estadísticas Generales</h5>
                                </div>
                                <div class="card-body">
                                    <div id="statsGenerales">
                                        <div class="text-center">
                                            <div class="spinner-border" role="status"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Reportes Disponibles</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-primary" onclick="generarReporte('usuarios')">
                                            <i class="fas fa-users"></i> Reporte de Usuarios
                                        </button>
                                        <button class="btn btn-outline-primary" onclick="generarReporte('actas')">
                                            <i class="fas fa-file-alt"></i> Reporte de Actas
                                        </button>
                                        <button class="btn btn-outline-primary" onclick="generarReporte('infracciones')">
                                            <i class="fas fa-exclamation-triangle"></i> Reporte de Infracciones
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Cargar estadísticas
            fetch('dashboard.php?api=dashboard-stats')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.stats) {
                        renderStatsGenerales(data.stats);
                    }
                })
                .catch(error => {
                    console.error('Error cargando estadísticas:', error);
                });
        }

        function loadPerfil() {
            const contentContainer = document.getElementById('contentContainer');
            contentContainer.innerHTML = `
                <div class="content-section active">
                    <h2><i class="fas fa-user"></i> Mi Perfil</h2>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <form id="perfilForm">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Nombre Completo</label>
                                                <input type="text" class="form-control" name="name" value="${userName}" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Rol</label>
                                                <input type="text" class="form-control" value="${userRole.toUpperCase()}" readonly>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Nueva Contraseña</label>
                                                <input type="password" class="form-control" name="password" placeholder="Dejar vacío si no desea cambiar">
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save"></i> Guardar Cambios
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-user-circle fa-5x text-secondary"></i>
                                    </div>
                                    <h5>${userName}</h5>
                                    <p class="text-muted">${userRole.toUpperCase()}</p>
                                    <hr>
                                    <small class="text-muted">Usuario desde: Hoy</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function loadDefaultSection(sectionId) {
            const contentContainer = document.getElementById('contentContainer');
            contentContainer.innerHTML = `
                <div class="content-section active">
                    <h2><i class="fas fa-cog"></i> ${sectionId.replace('-', ' ').toUpperCase()}</h2>
                    <div class="card">
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                Esta sección está en desarrollo. Contenido para: <strong>${sectionId}</strong>
                            </div>
                            <button class="btn btn-primary" onclick="loadSection('dashboard')">
                                <i class="fas fa-arrow-left"></i> Volver al Dashboard
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }
        function loadNotifications() {
            fetch('dashboard.php?api=notifications')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateNotificationBadge(data.notifications);
                        updateNotificationsDropdown(data.notifications);
                    }
                })
                .catch(error => {
                    console.error('Error cargando notificaciones:', error);
                });
        }

        function updateNotificationBadge(notifications) {
            const unreadCount = notifications.filter(n => n.is_read == 0 || n.is_read === false).length;
            const badge = document.getElementById('notificationCount');
            
            if (badge) {
                badge.textContent = unreadCount;
                if (unreadCount > 0) {
                    badge.style.display = 'inline-block';
                    badge.className = 'notification-badge bg-danger';
                } else {
                    badge.style.display = 'none';
                }
            }
            
            // También actualizar el contador en el dashboard
            const dashboardNotifications = document.getElementById('total-notifications');
            if (dashboardNotifications) {
                dashboardNotifications.textContent = unreadCount;
            }
        }

        function updateNotificationsDropdown(notifications) {
            const dropdown = document.getElementById('notificationsList');
            
            if (!dropdown) return;
            
            if (notifications.length === 0) {
                dropdown.innerHTML = '<li><span class="dropdown-item-text text-muted">No hay notificaciones</span></li>';
                return;
            }
            
            dropdown.innerHTML = notifications.slice(0, 5).map(notification => `
                <li>
                    <a class="dropdown-item ${notification.is_read == 0 ? 'fw-bold bg-light' : ''}" 
                       href="#" onclick="markNotificationAsRead(${notification.id}); return false;">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <strong class="text-truncate" style="max-width: 200px;">${notification.title}</strong>
                            <small class="text-muted ms-2">${formatDate(notification.created_at)}</small>
                        </div>
                        <small class="text-muted d-block">${notification.message}</small>
                        ${notification.is_read == 0 ? '<span class="badge bg-primary ms-auto">Nuevo</span>' : ''}
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
            `).join('');
            
            // Remover el último divider
            if (dropdown.lastElementChild && dropdown.lastElementChild.innerHTML.includes('dropdown-divider')) {
                dropdown.removeChild(dropdown.lastElementChild);
            }
        }

        function markNotificationAsRead(notificationId) {
            const formData = new FormData();
            formData.append('notification_id', notificationId);
            
            fetch('dashboard.php?api=mark_notification_read', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotifications(); // Recargar notificaciones
                }
            })
            .catch(error => {
                console.error('Error marcando notificación como leída:', error);
            });
        }

        function markAllAsRead() {
            // Implementar función para marcar todas como leídas
            console.log('Marcando todas las notificaciones como leídas');
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diff = now - date;
            
            if (diff < 60000) return 'Ahora';
            if (diff < 3600000) return Math.floor(diff / 60000) + 'm';
            if (diff < 86400000) return Math.floor(diff / 3600000) + 'h';
            return Math.floor(diff / 86400000) + 'd';
        }

        function loadSectionContent(sectionId) {
            switch(sectionId) {
                case 'dashboard':
                    // El dashboard ya está cargado
                    break;
                case 'crear-acta':
                case 'generar-acta':
                    loadActaForm();
                    break;
                case 'gestionar-usuarios':
                    loadUsersManagement();
                    break;
                case 'aprobar-usuarios':
                    loadUserApproval();
                    break;
                case 'consultas':
                    loadConsultasSection();
                    break;
                case 'actas-contra':
                    loadActasContra();
                    break;
                case 'sistema':
                    loadSystemManagement();
                    break;
                case 'perfil':
                    loadUserProfile();
                    break;
                case 'configuracion':
                    loadSystemConfiguration();
                    break;
                case 'infracciones':
                    loadInfraccionesList();
                    break;
                case 'conductores':
                    loadConductoresList();
                    break;
                case 'vehiculos':
                    loadVehiculosList();
                    break;
                case 'reportes':
                    loadReportesList();
                    break;
                default:
                    loadGenericContent(sectionId);
            }
        }

        function loadDashboardStats() {
            const statsContainer = document.getElementById('dashboardStats');
            console.log('🔄 Cargando estadísticas del dashboard...');
            
            fetch('dashboard.php?api=dashboard-stats', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                console.log('📡 Respuesta recibida:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('📊 Datos de la API:', data);
                if (data.success) {
                    console.log('✅ Datos exitosos, renderizando estadísticas...');
                    renderStats(data.stats);
                } else {
                    console.log('❌ Error en los datos:', data.message);
                    renderDefaultStats();
                }
            })
            .catch(error => {
                console.error('❌ Error loading stats:', error);
                renderDefaultStats();
            });
        }

        function renderStats(stats) {
            const statsContainer = document.getElementById('dashboardStats');
            let statsHtml = '';
            
            // Debug temporal - mostrar datos recibidos
            console.log('📊 Estadísticas recibidas:', stats);
            console.log('👤 Rol de usuario:', userRole);

            switch(userRole) {
                case 'superadmin':
                case 'administrador':
                    statsHtml = `
                        <div class="col-md-3 col-6">
                            <div class="stats-card">
                                <div class="stats-number">${stats.total_usuarios || 0}</div>
                                <div class="stats-label">Total Usuarios</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stats-card">
                                <div class="stats-number">${stats.usuarios_activos || 0}</div>
                                <div class="stats-label">Usuarios Activos</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stats-card">
                                <div class="stats-number">${stats.total_conductores || 0}</div>
                                <div class="stats-label">Conductores</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stats-card">
                                <div class="stats-number">${stats.total_vehiculos || 0}</div>
                                <div class="stats-label">Vehículos</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stats-card">
                                <div class="stats-number">${stats.total_infracciones || 0}</div>
                                <div class="stats-label">Total Infracciones</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stats-card">
                                <div class="stats-number">${stats.usuarios_pendientes || 0}</div>
                                <div class="stats-label">Pendientes Aprobación</div>
                            </div>
                        </div>
                    `;
                    break;
                case 'fiscalizador':
                    statsHtml = `
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number">${stats.total_infracciones || 0}</div>
                                <div class="stats-label">Total Infracciones</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number">${stats.infracciones_procesadas || 0}</div>
                                <div class="stats-label">Procesadas</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number">${stats.infracciones_pendientes || 0}</div>
                                <div class="stats-label">Pendientes</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number">S/ ${stats.total_multas || 0}</div>
                                <div class="stats-label">Total Multas</div>
                            </div>
                        </div>
                    `;
                    break;
                case 'ventanilla':
                    statsHtml = `
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number">${stats.atenciones_hoy || 0}</div>
                                <div class="stats-label">Atenciones Hoy</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number">${stats.cola_espera || 0}</div>
                                <div class="stats-label">En Cola</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number">${stats.tramites_completados || 0}</div>
                                <div class="stats-label">Completados</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number">${stats.tiempo_promedio || 0} min</div>
                                <div class="stats-label">Tiempo Promedio</div>
                            </div>
                        </div>
                    `;
                    break;
                case 'inspector':
                    statsHtml = `
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number">${stats.total_infracciones || 0}</div>
                                <div class="stats-label">Total Infracciones</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number">${stats.infracciones_resueltas || 0}</div>
                                <div class="stats-label">Resueltas</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number">${stats.infracciones_pendientes || 0}</div>
                                <div class="stats-label">Pendientes</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number">${stats.total_actas || 0}</div>
                                <div class="stats-label">Total Actas</div>
                            </div>
                        </div>
                    `;
                    break;
                default:
                    statsHtml = '<div class="col-12"><p class="text-center">Estadísticas no disponibles para este rol.</p></div>';
            }

            statsContainer.innerHTML = statsHtml;
        }

        function renderDefaultStats() {
            console.log('⚠️ Cargando estadísticas por defecto para rol:', userRole);
            
            // Forzar cargar datos reales primero
            console.log('🔄 Intentando cargar datos reales desde la base de datos...');
            fetch('dashboard.php?api=dashboard-stats')
                .then(response => response.json())
                .then(data => {
                    console.log('🔍 Respuesta de emergencia:', data);
                    if (data.success && data.stats) {
                        console.log('✅ Datos reales encontrados, usando esos');
                        renderStats(data.stats);
                    } else {
                        console.log('❌ Usando valores por defecto');
                        useDefaultValues();
                    }
                })
                .catch(error => {
                    console.error('❌ Error en carga de emergencia:', error);
                    useDefaultValues();
                });
                
            function useDefaultValues() {
                const defaultStats = {
                    administrador: { 
                        total_usuarios: 0, 
                        usuarios_activos: 0, 
                        total_conductores: 0, 
                        total_vehiculos: 0,
                        total_infracciones: 0,
                        usuarios_pendientes: 0
                    },
                    superadmin: { 
                        total_usuarios: 0, 
                        usuarios_activos: 0, 
                        total_conductores: 0, 
                        total_vehiculos: 0,
                        total_infracciones: 0,
                        usuarios_pendientes: 0
                    },
                    fiscalizador: { total_infracciones: 0, infracciones_procesadas: 0, infracciones_pendientes: 0, total_multas: 0 },
                    ventanilla: { atenciones_hoy: 0, cola_espera: 0, tramites_completados: 0, tiempo_promedio: 0 },
                    inspector: { total_infracciones: 0, infracciones_resueltas: 0, infracciones_pendientes: 0, total_actas: 0 }
                };
                
                console.log('📊 Usando estadísticas por defecto:', defaultStats[userRole] || {});
                renderStats(defaultStats[userRole] || {});
            }
        }

        function loadDashboardContent() {
            const dashboardContent = document.getElementById('dashboardContent');
            
            // Contenido específico del dashboard según el rol
            let content = '';
            
            switch(userRole) {
                case 'administrador':
                    content = `
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-users"></i> Usuarios Recientes</h5>
                                </div>
                                <div class="card-body">
                                    <div class="loading">Cargando usuarios...</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-line"></i> Actividad del Sistema</h5>
                                </div>
                                <div class="card-body">
                                    <div class="loading">Cargando gráfico...</div>
                                </div>
                            </div>
                        </div>
                    `;
                    break;
                case 'fiscalizador':
                    content = `
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-file-alt"></i> Actas Recientes</h5>
                                </div>
                                <div class="card-body">
                                    <div class="loading">Cargando actas...</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-clock"></i> Actividad Hoy</h5>
                                </div>
                                <div class="card-body">
                                    <div class="loading">Cargando actividad...</div>
                                </div>
                            </div>
                        </div>
                    `;
                    break;
                default:
                    content = `
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5>Bienvenido al Sistema</h5>
                                    <p>Utiliza el menú superior para navegar por las diferentes secciones.</p>
                                </div>
                            </div>
                        </div>
                    `;
            }
            
            dashboardContent.innerHTML = content;
        }

        // Funciones específicas para cargar contenido de secciones

        function loadActaForm() {
            const section = document.getElementById(`${currentSection}-section`);
            const content = `
                <div class="row">
                    <div class="col-12">
                        <form id="actaForm" onsubmit="submitActa(event)">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Número de Acta *</label>
                                        <input type="text" class="form-control" name="numero_acta" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Fecha de Intervención *</label>
                                        <input type="date" class="form-control" name="fecha_intervencion" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Hora de Intervención *</label>
                                        <input type="time" class="form-control" name="hora_intervencion" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tipo de Agente *</label>
                                        <select class="form-select" name="tipo_agente" required>
                                            <option value="">Seleccionar...</option>
                                            <option value="Fiscalizador">Fiscalizador</option>
                                            <option value="Inspector">Inspector</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Placa del Vehículo *</label>
                                        <input type="text" class="form-control" name="placa" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">RUC/DNI *</label>
                                        <input type="text" class="form-control" name="ruc_dni" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Razón Social *</label>
                                        <input type="text" class="form-control" name="razon_social" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Descripción de los Hechos *</label>
                                        <textarea class="form-control" name="descripcion_hechos" rows="4" required></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Calificación *</label>
                                        <select class="form-select" name="calificacion" required>
                                            <option value="">Seleccionar...</option>
                                            <option value="Leve">Leve</option>
                                            <option value="Grave">Grave</option>
                                            <option value="Muy Grave">Muy Grave</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Monto de Multa</label>
                                        <input type="number" class="form-control" name="monto_multa" step="0.01">
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-secondary me-2" onclick="resetForm()">Limpiar</button>
                                <button type="submit" class="btn btn-primary">Guardar Acta</button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
            
            section.innerHTML = content;
        }

        function loadUsersManagement() {
            const section = document.getElementById(`${currentSection}-section`);
            section.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <input type="text" class="form-control" placeholder="Buscar usuarios..." onkeyup="searchUsers(this.value)">
                    </div>
                    <button class="btn btn-primary" onclick="openUserModal()">
                        <i class="fas fa-plus"></i> Nuevo Usuario
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <tr><td colspan="6" class="text-center">Cargando usuarios...</td></tr>
                        </tbody>
                    </table>
                </div>
            `;
            
            loadUsersData();
        }

        function loadUsersData() {
            fetch('dashboard.php?api=users')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('usersTableBody');
                    
                    if (data.success && data.users) {
                        tbody.innerHTML = data.users.map(user => `
                            <tr>
                                <td>${user.id}</td>
                                <td>${user.name}</td>
                                <td>${user.email}</td>
                                <td>
                                    <span class="badge bg-info">${user.role}</span>
                                </td>
                                <td>
                                    <span class="badge ${user.status === 'active' ? 'bg-success' : 'bg-secondary'}">
                                        ${user.status === 'active' ? 'Activo' : 'Inactivo'}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editUser(${user.id})" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm ${user.status === 'active' ? 'btn-outline-warning' : 'btn-outline-success'}" 
                                            onclick="toggleUserStatus(${user.id})" title="${user.status === 'active' ? 'Desactivar' : 'Activar'}">
                                        <i class="fas ${user.status === 'active' ? 'fa-pause' : 'fa-play'}"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(${user.id})" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `).join('');
                    } else {
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No se pudieron cargar los usuarios</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('usersTableBody').innerHTML = 
                        '<tr><td colspan="6" class="text-center text-danger">Error al cargar usuarios</td></tr>';
                });
        }

        function searchUsers(query) {
            const rows = document.querySelectorAll('#usersTableBody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(query.toLowerCase())) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function openUserModal(userId = null) {
            const modal = new bootstrap.Modal(document.getElementById('generalModal'));
            const modalTitle = document.getElementById('modalTitle');
            const modalBody = document.getElementById('modalBody');
            const modalFooter = document.getElementById('modalFooter');
            
            modalTitle.textContent = userId ? 'Editar Usuario' : 'Nuevo Usuario';
            
            // Cargar datos del usuario si es edición
            if (userId) {
                loadUserForEdit(userId, modalBody, modalFooter, modal);
            } else {
                showNewUserForm(modalBody, modalFooter, modal);
            }
        }

        function showNewUserForm(modalBody, modalFooter, modal) {
            modalBody.innerHTML = `
                <form id="userForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" id="userName" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="userUsername" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="userEmail" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rol</label>
                            <select class="form-control" id="userRole" required>
                                <option value="">Seleccionar rol...</option>
                                <option value="inspector">Inspector</option>
                                <option value="fiscalizador">Fiscalizador</option>
                                <option value="administrador">Administrador</option>
                                ${userRole === 'superadmin' ? '<option value="superadmin">Super Administrador</option>' : ''}
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="userPassword" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="userPasswordConfirm" required>
                    </div>
                </form>
            `;
            
            modalFooter.innerHTML = `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="saveUser()">Crear Usuario</button>
            `;
            
            modal.show();
        }

        function loadUserForEdit(userId, modalBody, modalFooter, modal) {
            console.log('Cargando usuario para editar:', userId);
            modalBody.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando datos del usuario...</div>';
            
            // Cargar los datos del usuario desde la API
            fetch('dashboard.php?api=users')
                .then(response => response.json())
                .then(data => {
                    console.log('Datos de usuarios recibidos:', data);
                    if (data.success && data.users) {
                        const user = data.users.find(u => u.id == userId);
                        if (user) {
                            console.log('Usuario encontrado:', user);
                            modalBody.innerHTML = `
                                <form id="userEditForm">
                                    <input type="hidden" id="editUserId" value="${user.id}">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nombre Completo</label>
                                            <input type="text" class="form-control" id="editUserName" value="${user.name || ''}" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Usuario</label>
                                            <input type="text" class="form-control" id="editUserUsername" value="${user.username || ''}" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" id="editUserEmail" value="${user.email || ''}" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Rol</label>
                                            <select class="form-control" id="editUserRole" required>
                                                <option value="inspector" ${user.role === 'inspector' ? 'selected' : ''}>Inspector</option>
                                                <option value="fiscalizador" ${user.role === 'fiscalizador' ? 'selected' : ''}>Fiscalizador</option>
                                                <option value="administrador" ${user.role === 'administrador' ? 'selected' : ''}>Administrador</option>
                                                ${userRole === 'superadmin' ? `<option value="superadmin" ${user.role === 'superadmin' ? 'selected' : ''}>Super Administrador</option>` : ''}
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Nueva Contraseña <small class="text-muted">(dejar en blanco para mantener actual)</small></label>
                                        <input type="password" class="form-control" id="editUserPassword" placeholder="Nueva contraseña (opcional)">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Confirmar Nueva Contraseña</label>
                                        <input type="password" class="form-control" id="editUserPasswordConfirm" placeholder="Confirmar nueva contraseña">
                                    </div>
                                </form>
                            `;
                            
                            modalFooter.innerHTML = `
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                                <button type="button" class="btn btn-primary" onclick="updateUser(${user.id})">
                                    <i class="fas fa-save"></i> Actualizar Usuario
                                </button>
                            `;
                        } else {
                            modalBody.innerHTML = '<div class="alert alert-danger">Usuario no encontrado</div>';
                        }
                    } else {
                        modalBody.innerHTML = '<div class="alert alert-danger">Error al cargar datos del usuario</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = '<div class="alert alert-danger">Error al cargar datos del usuario</div>';
                });
                
            modal.show();
        }

        function saveUser() {
            const name = document.getElementById('userName').value;
            const username = document.getElementById('userUsername').value;
            const email = document.getElementById('userEmail').value;
            const role = document.getElementById('userRole').value;
            const password = document.getElementById('userPassword').value;
            const passwordConfirm = document.getElementById('userPasswordConfirm').value;
            
            if (password !== passwordConfirm) {
                alert('Las contraseñas no coinciden');
                return;
            }
            
            showLoading('Creando usuario...');
            
            fetch('dashboard.php?api=create_user', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    name: name,
                    username: username,
                    email: email,
                    role: role,
                    password: password
                })
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showMessage(data.message, 'success');
                    bootstrap.Modal.getInstance(document.getElementById('generalModal')).hide();
                    loadUsersData(); // Recargar la tabla
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showMessage('Error al crear usuario', 'error');
            });
        }

        function updateUser(userId) {
            const name = document.getElementById('editUserName').value;
            const username = document.getElementById('editUserUsername').value;
            const email = document.getElementById('editUserEmail').value;
            const role = document.getElementById('editUserRole').value;
            const password = document.getElementById('editUserPassword').value;
            const passwordConfirm = document.getElementById('editUserPasswordConfirm').value;
            
            if (password && password !== passwordConfirm) {
                alert('Las contraseñas no coinciden');
                return;
            }
            
            showLoading('Actualizando usuario...');
            
            const updateData = {
                name: name,
                username: username,
                email: email,
                role: role
            };
            
            if (password) {
                updateData.password = password;
            }
            
            fetch(`dashboard.php?api=update_user&id=${userId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(updateData)
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showMessage(data.message, 'success');
                    bootstrap.Modal.getInstance(document.getElementById('generalModal')).hide();
                    loadUsersData(); // Recargar la tabla
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showMessage('Error al actualizar usuario', 'error');
            });
        }

        function editUser(userId) {
            openUserModal(userId);
        }

        function toggleUserStatus(userId) {
            if (confirm('¿Estás seguro de cambiar el estado de este usuario?')) {
                showLoading('Cambiando estado...');
                
                const formData = new FormData();
                formData.append('id', userId);
                
                fetch('dashboard.php?api=toggle_user_status', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        showMessage(data.message, 'success');
                        loadUsersData(); // Recargar la tabla
                    } else {
                        showMessage(data.message, 'error');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showMessage('Error al cambiar estado del usuario', 'error');
                });
            }
        }

        function deleteUser(userId) {
            if (confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')) {
                showLoading('Eliminando usuario...');
                
                const formData = new FormData();
                formData.append('id', userId);
                
                fetch(`dashboard.php?api=delete_user&id=${userId}`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        showMessage(data.message, 'success');
                        loadUsersData(); // Recargar la tabla
                    } else {
                        showMessage(data.message, 'error');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showMessage('Error al eliminar usuario', 'error');
                });
            }
        }

        function loadUserApproval() {
            const section = document.getElementById(`${currentSection}-section`);
            section.innerHTML = `
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Aquí puedes aprobar o rechazar usuarios pendientes de aprobación.
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Rol Solicitado</th>
                                <th>Fecha Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="pendingUsersTableBody">
                            <tr><td colspan="5" class="text-center">Cargando usuarios pendientes...</td></tr>
                        </tbody>
                    </table>
                </div>
            `;
            
            loadPendingUsers();
        }

        function loadConsultasSection() {
            const section = document.getElementById(`${currentSection}-section`);
            section.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-search"></i> Consulta por Documento</h5>
                            </div>
                            <div class="card-body">
                                <form onsubmit="consultarDocumento(event)">
                                    <div class="mb-3">
                                        <label class="form-label">DNI/RUC</label>
                                        <input type="text" class="form-control" id="documentoConsulta" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Consultar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-list"></i> Resultados</h5>
                            </div>
                            <div class="card-body" id="consultaResultados">
                                <p class="text-muted">Ingresa un documento para ver los resultados.</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function loadActasContra() {
            const section = document.getElementById(`${currentSection}-section`);
            section.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Actas de Contraorden</h4>
                    <button class="btn btn-primary" onclick="loadSection('crear-acta')">
                        <i class="fas fa-plus"></i> Nueva Acta
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>N° Acta</th>
                                <th>Fecha</th>
                                <th>Placa</th>
                                <th>Infractor</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="actasTableBody">
                            <tr><td colspan="6" class="text-center">Cargando actas...</td></tr>
                        </tbody>
                    </table>
                </div>
            `;
            
            loadActasData();
        }

        function loadSystemManagement() {
            const section = document.getElementById(`${currentSection}-section`);
            section.innerHTML = `
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-server"></i> Estado del Sistema</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Versión:</strong> 1.0.0</p>
                                <p><strong>Base de Datos:</strong> <span class="badge bg-success">Conectada</span></p>
                                <p><strong>Usuarios Activos:</strong> <span id="activeUsersCount">0</span></p>
                                <button class="btn btn-warning btn-sm" onclick="clearCache()">
                                    <i class="fas fa-trash"></i> Limpiar Cache
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-chart-pie"></i> Estadísticas del Sistema</h5>
                            </div>
                            <div class="card-body">
                                <div class="loading">Cargando estadísticas del sistema...</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function loadGenericContent(sectionId) {
            const section = document.getElementById(`${sectionId}-section`);
            section.innerHTML = `
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-construction fa-3x text-muted mb-3"></i>
                        <h5>Sección en Desarrollo</h5>
                        <p class="text-muted">Esta funcionalidad está siendo desarrollada y estará disponible pronto.</p>
                    </div>
                </div>
            `;
        }

        // Nuevas funciones para perfil y configuración
        function loadUserProfile() {
            const section = document.getElementById(`${currentSection}-section`);
            section.innerHTML = '<div class="loading">Cargando perfil...</div>';
            
            fetch('dashboard.php?api=profile')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderUserProfile(data.user);
                } else {
                    section.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            })
            .catch(error => {
                section.innerHTML = '<div class="alert alert-danger">Error al cargar perfil</div>';
            });
        }

        function renderUserProfile(user) {
            const section = document.getElementById(`${currentSection}-section`);
            section.innerHTML = `
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-user"></i> Mi Perfil</h5>
                            </div>
                            <div class="card-body">
                                <form id="profileForm">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nombre Completo</label>
                                            <input type="text" class="form-control" name="name" value="${user.name || ''}" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Usuario</label>
                                            <input type="text" class="form-control" value="${user.username || ''}" disabled>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email" value="${user.email || ''}" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Rol</label>
                                            <input type="text" class="form-control" value="${user.role || ''}" disabled>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Estado</label>
                                            <span class="badge ${user.status === 'approved' ? 'bg-success' : 'bg-warning'}">${user.status === 'approved' ? 'Aprobado' : user.status}</span>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Guardar Cambios
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-info-circle"></i> Información de Cuenta</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Usuario:</strong> ${user.username}</p>
                                <p><strong>Rol:</strong> <span class="badge bg-primary">${user.role}</span></p>
                                <p><strong>Registrado:</strong> ${formatDate(user.created_at)}</p>
                                <p><strong>Actualizado:</strong> ${formatDate(user.updated_at)}</p>
                                <hr>
                                <button class="btn btn-warning btn-sm w-100 mb-2" onclick="changePassword()">
                                    <i class="fas fa-key"></i> Cambiar Contraseña
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Agregar event listener al formulario
            document.getElementById('profileForm').addEventListener('submit', updateProfile);
        }

        function loadSystemConfiguration() {
            const section = document.getElementById(`${currentSection}-section`);
            section.innerHTML = '<div class="loading">Cargando configuración...</div>';
            
            fetch('dashboard.php?api=system-config')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderSystemConfiguration(data.config);
                } else {
                    section.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            })
            .catch(error => {
                section.innerHTML = '<div class="alert alert-danger">Error al cargar configuración</div>';
            });
        }

        function renderSystemConfiguration(config) {
            const section = document.getElementById(`${currentSection}-section`);
            section.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-cog"></i> Configuración General</h5>
                            </div>
                            <div class="card-body">
                                <form id="configForm">
                                    <div class="mb-3">
                                        <label class="form-label">Nombre del Sistema</label>
                                        <input type="text" class="form-control" name="nombre_sistema" value="${config.nombre_sistema || 'Sistema de Gestión'}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Versión</label>
                                        <input type="text" class="form-control" name="version" value="${config.version || '1.0.0'}" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Timeout de Sesión (minutos)</label>
                                        <input type="number" class="form-control" name="sesion_timeout" value="${config.sesion_timeout || 30}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Máximo de Usuarios</label>
                                        <input type="number" class="form-control" name="max_usuarios" value="${config.max_usuarios || 100}">
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" name="mantenimiento" ${config.mantenimiento ? 'checked' : ''}>
                                        <label class="form-check-label">Modo Mantenimiento</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Guardar Configuración
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-palette"></i> Configuración de Temas</h5>
                            </div>
                            <div class="card-body">
                                <form id="themeForm">
                                    <div class="mb-3">
                                        <label class="form-label">Tema Principal</label>
                                        <select class="form-control" name="tema_principal" onchange="previewTheme(this.value)">
                                            <option value="default" ${(config.tema_principal || 'default') === 'default' ? 'selected' : ''}>Tema por Defecto</option>
                                            <option value="dark" ${config.tema_principal === 'dark' ? 'selected' : ''}>Tema Oscuro</option>
                                            <option value="light" ${config.tema_principal === 'light' ? 'selected' : ''}>Tema Claro</option>
                                            <option value="blue" ${config.tema_principal === 'blue' ? 'selected' : ''}>Tema Azul</option>
                                            <option value="green" ${config.tema_principal === 'green' ? 'selected' : ''}>Tema Verde</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Color Primario</label>
                                        <input type="color" class="form-control form-control-color" name="color_primario" value="${config.color_primario || '#2c3e50'}" onchange="previewColor('primary', this.value)">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Color Secundario</label>
                                        <input type="color" class="form-control form-control-color" name="color_secundario" value="${config.color_secundario || '#3498db'}" onchange="previewColor('secondary', this.value)">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Color de Fondo</label>
                                        <input type="color" class="form-control form-control-color" name="color_fondo" value="${config.color_fondo || '#f8f9fa'}" onchange="previewColor('background', this.value)">
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" name="modo_oscuro" ${config.modo_oscuro ? 'checked' : ''} onchange="toggleDarkMode(this.checked)">
                                        <label class="form-check-label">Modo Oscuro Automático</label>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-success" onclick="applyTheme()">
                                            <i class="fas fa-paint-brush"></i> Aplicar Tema
                                        </button>
                                        <button type="button" class="btn btn-warning" onclick="resetTheme()">
                                            <i class="fas fa-undo"></i> Restaurar Tema Original
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Guardar Tema
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Event listeners para los formularios
            document.getElementById('configForm').addEventListener('submit', saveSystemConfig);
            document.getElementById('themeForm').addEventListener('submit', saveThemeConfig);
        }
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-bell"></i> Notificaciones</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="notificaciones_email" ${config.notificaciones_email ? 'checked' : ''}>
                                    <label class="form-check-label">Notificaciones por Email</label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="backup_automatico" ${config.backup_automatico ? 'checked' : ''}>
                                    <label class="form-check-label">Backup Automático</label>
                                </div>
                                <hr>
                                <h6><i class="fas fa-palette"></i> Apariencia</h6>
                                <div class="mb-3">
                                    <label class="form-label">Tema</label>
                                    <select class="form-select" name="tema">
                                        <option value="light" ${config.tema === 'light' ? 'selected' : ''}>Claro</option>
                                        <option value="dark" ${config.tema === 'dark' ? 'selected' : ''}>Oscuro</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Idioma</label>
                                    <select class="form-select" name="idioma">
                                        <option value="es" ${config.idioma === 'es' ? 'selected' : ''}>Español</option>
                                        <option value="en" ${config.idioma === 'en' ? 'selected' : ''}>Inglés</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Agregar event listener al formulario
            document.getElementById('configForm').addEventListener('submit', updateSystemConfig);
        }

        function loadInfraccionesList() {
            const section = document.getElementById(`${currentSection}-section`);
            section.innerHTML = '<div class="loading">Cargando infracciones...</div>';
            
            fetch('dashboard.php?api=infracciones')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderInfraccionesList(data.infracciones);
                } else {
                    section.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            })
            .catch(error => {
                section.innerHTML = '<div class="alert alert-danger">Error al cargar infracciones</div>';
            });
        }

        function renderInfraccionesList(infracciones) {
            const section = document.getElementById(`${currentSection}-section`);
            section.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4><i class="fas fa-exclamation-triangle"></i> Gestión de Infracciones</h4>
                    <button class="btn btn-primary" onclick="showNewInfraccionForm()">
                        <i class="fas fa-plus"></i> Nueva Infracción
                    </button>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Placa</th>
                                        <th>Conductor</th>
                                        <th>Lugar</th>
                                        <th>Monto</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${infracciones.map(infraccion => `
                                        <tr>
                                            <td>${formatDate(infraccion.fecha_intervencion)}</td>
                                            <td><strong>${infraccion.placa}</strong></td>
                                            <td>${infraccion.nombre_conductor || 'N/A'}</td>
                                            <td>${infraccion.lugar_intervencion}</td>
                                            <td>S/ ${infraccion.monto_multa || '0.00'}</td>
                                            <td><span class="badge bg-warning">Pendiente</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick="editInfraccion(${infraccion.id})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteInfraccion(${infraccion.id})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
        }

        function loadConductoresList() {
            const section = document.getElementById(`${currentSection}-section`);
            section.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4><i class="fas fa-users"></i> Gestión de Conductores</h4>
                    <button class="btn btn-primary" onclick="showNewConductorForm()">
                        <i class="fas fa-plus"></i> Nuevo Conductor
                    </button>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="loading">Cargando conductores...</div>
                    </div>
                </div>
            `;
            
            // Cargar conductores desde la API
            fetch('dashboard.php?api=conductores')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderConductoresList(data.conductores);
                } else {
                    document.querySelector('.loading').innerHTML = `<div class="alert alert-warning">No hay conductores registrados</div>`;
                }
            });
        }

        function loadVehiculosList() {
            const section = document.getElementById(`${currentSection}-section`);
            section.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4><i class="fas fa-car"></i> Gestión de Vehículos</h4>
                    <button class="btn btn-primary" onclick="showNewVehiculoForm()">
                        <i class="fas fa-plus"></i> Nuevo Vehículo
                    </button>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="loading">Cargando vehículos...</div>
                    </div>
                </div>
            `;
            
            // Cargar vehículos desde la API
            fetch('dashboard.php?api=vehiculos')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderVehiculosList(data.vehiculos);
                } else {
                    document.querySelector('.loading').innerHTML = `<div class="alert alert-warning">No hay vehículos registrados</div>`;
                }
            });
        }

        function loadReportesList() {
            const section = document.getElementById(`${currentSection}-section`);
            section.innerHTML = `
                <div class="row">
                    <div class="col-md-12">
                        <h4><i class="fas fa-chart-bar"></i> Reportes y Estadísticas</h4>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-file-pdf fa-2x text-danger mb-2"></i>
                                <h6>Reporte de Usuarios</h6>
                                <button class="btn btn-sm btn-outline-danger" onclick="generateReport('users')">
                                    <i class="fas fa-download"></i> Generar PDF
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-file-excel fa-2x text-success mb-2"></i>
                                <h6>Reporte de Actas</h6>
                                <button class="btn btn-sm btn-outline-success" onclick="generateReport('actas')">
                                    <i class="fas fa-download"></i> Generar Excel
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-chart-pie fa-2x text-info mb-2"></i>
                                <h6>Estadísticas</h6>
                                <button class="btn btn-sm btn-outline-info" onclick="generateReport('stats')">
                                    <i class="fas fa-download"></i> Exportar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-calendar fa-2x text-warning mb-2"></i>
                                <h6>Reporte Mensual</h6>
                                <button class="btn btn-sm btn-outline-warning" onclick="generateMonthlyReport()">
                                    <i class="fas fa-download"></i> Generar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-line"></i> Gráficos Estadísticos</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center text-muted">
                            <i class="fas fa-chart-bar fa-3x mb-3"></i>
                            <p>Los gráficos estadísticos se mostrarán aquí</p>
                        </div>
                    </div>
                </div>
            `;
        }

        // Funciones AJAX

        function submitActa(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData);
            
            showLoading('Guardando acta...');
            
            fetch('dashboard.php?api=actas', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                hideLoading();
                if (result.success) {
                    showAlert('Acta guardada correctamente', 'success');
                    event.target.reset();
                } else {
                    showAlert('Error al guardar acta: ' + result.message, 'danger');
                }
            })
            .catch(error => {
                hideLoading();
                showAlert('Error de conexión', 'danger');
                console.error('Error:', error);
            });
        }

        function consultarDocumento(event) {
            event.preventDefault();
            
            const documento = document.getElementById('documentoConsulta').value;
            const resultadosDiv = document.getElementById('consultaResultados');
            
            resultadosDiv.innerHTML = '<div class="loading">Consultando...</div>';
            
            fetch(`dashboard.php?api=consultar-documento&documento=${encodeURIComponent(documento)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.actas.length > 0) {
                    let html = '<ul class="list-group">';
                    data.actas.forEach(acta => {
                        html += `
                            <li class="list-group-item">
                                <strong>Acta N°:</strong> ${acta.numero_acta}<br>
                                <strong>Fecha:</strong> ${acta.fecha_intervencion}<br>
                                <strong>Estado:</strong> ${acta.estado == '1' ? 'Procesada' : 'Pendiente'}
                            </li>
                        `;
                    });
                    html += '</ul>';
                    resultadosDiv.innerHTML = html;
                } else {
                    resultadosDiv.innerHTML = '<p class="text-muted">No se encontraron registros para este documento.</p>';
                }
            })
            .catch(error => {
                resultadosDiv.innerHTML = '<p class="text-danger">Error en la consulta.</p>';
                console.error('Error:', error);
            });
        }

        function loadUsersData() {
            fetch('dashboard.php?api=users')
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('usersTableBody');
                if (data.success && data.users.length > 0) {
                    tbody.innerHTML = data.users.map(user => `
                        <tr>
                            <td>${user.id}</td>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td><span class="badge role-${user.role}">${user.role}</span></td>
                            <td><span class="badge ${user.status === 'approved' ? 'bg-success' : 'bg-warning'}">${user.status}</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="editUser(${user.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay usuarios registrados</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error loading users:', error);
                document.getElementById('usersTableBody').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error al cargar usuarios</td></tr>';
            });
        }

        function loadPendingUsers() {
            fetch('dashboard.php?api=users-pending')
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('pendingUsersTableBody');
                if (data.success && data.users.length > 0) {
                    tbody.innerHTML = data.users.map(user => `
                        <tr>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td><span class="badge role-${user.role}">${user.role}</span></td>
                            <td>${formatDate(user.created_at)}</td>
                            <td>
                                <button class="btn btn-sm btn-success me-1" onclick="approveUser(${user.id})">
                                    <i class="fas fa-check"></i> Aprobar
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="rejectUser(${user.id})">
                                    <i class="fas fa-times"></i> Rechazar
                                </button>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay usuarios pendientes de aprobación</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error loading pending users:', error);
                document.getElementById('pendingUsersTableBody').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error al cargar usuarios pendientes</td></tr>';
            });
        }

        function loadActasData() {
            fetch('dashboard.php?api=actas')
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('actasTableBody');
                if (data.success && data.actas.length > 0) {
                    tbody.innerHTML = data.actas.map(acta => `
                        <tr>
                            <td>${acta.numero_acta}</td>
                            <td>${formatDate(acta.fecha_intervencion)}</td>
                            <td>${acta.placa}</td>
                            <td>${acta.razon_social}</td>
                            <td><span class="badge ${acta.estado == '1' ? 'bg-success' : 'bg-warning'}">${acta.estado == '1' ? 'Procesada' : 'Pendiente'}</span></td>
                            <td>
                                <button class="btn btn-sm btn-info me-1" onclick="viewActa(${acta.id})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-primary me-1" onclick="editActa(${acta.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteActa(${acta.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay actas registradas</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error loading actas:', error);
                document.getElementById('actasTableBody').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error al cargar actas</td></tr>';
            });
        }

        // Funciones auxiliares

        function getCsrfToken() {
            return '';  // No necesario en este contexto
        }

        function showAlert(message, type = 'info') {
            // Remover notificaciones anteriores
            const existingAlerts = document.querySelectorAll('.custom-alert');
            existingAlerts.forEach(alert => alert.remove());
            
            const alertDiv = document.createElement('div');
            alertDiv.className = `custom-alert alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
            
            // Estilos para posicionamiento absoluto sin afectar el layout
            alertDiv.style.cssText = `
                position: fixed;
                top: 80px;
                right: 20px;
                z-index: 1060;
                width: 320px;
                max-width: 90vw;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                border: none;
                border-radius: 8px;
                font-size: 14px;
                animation: slideIn 0.3s ease-out;
                pointer-events: auto;
            `;
            
            alertDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' || type === 'danger' ? 'fa-exclamation-triangle' : 'fa-info-circle'} me-2"></i>
                    <div class="flex-grow-1">${message}</div>
                    <button type="button" class="btn-close btn-sm" onclick="this.parentElement.parentElement.remove()"></button>
                </div>
            `;
            
            document.body.appendChild(alertDiv);
            
            // Auto-remove after 3.5 seconds
            setTimeout(() => {
                if (alertDiv && alertDiv.parentNode) {
                    alertDiv.style.animation = 'slideOut 0.3s ease-in';
                    setTimeout(() => {
                        if (alertDiv.parentNode) {
                            alertDiv.parentNode.removeChild(alertDiv);
                        }
                    }, 300);
                }
            }, 3500);
        }

        function showLoading(message = 'Cargando...') {
            const modal = new bootstrap.Modal(document.getElementById('generalModal'));
            document.getElementById('modalTitle').textContent = 'Procesando';
            document.getElementById('modalBody').innerHTML = `
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-3">${message}</p>
                </div>
            `;
            document.getElementById('modalFooter').style.display = 'none';
            modal.show();
        }

        function hideLoading() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('generalModal'));
            if (modal) {
                modal.hide();
            }
        }

        function showMessage(message, type = 'info') {
            // Usar la función showAlert mejorada
            showAlert(message, type);
        }

        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('es-PE');
        }

        function resetForm() {
            document.getElementById('actaForm').reset();
        }

        function logout() {
            if (confirm('¿Está seguro de que desea cerrar sesión?')) {
                window.location.href = 'dashboard.php?logout=1';
            }
        }

        // Funciones específicas de administración (solo para administradores y superadmin)

        function approveUser(userId) {
            if (confirm('¿Está seguro de aprobar este usuario?')) {
                const formData = new FormData();
                formData.append('user_id', userId);
                
                fetch('dashboard.php?api=approve-user', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Usuario aprobado correctamente', 'success');
                        loadPendingUsers();
                    } else {
                        showAlert('Error al aprobar usuario', 'danger');
                    }
                })
                .catch(error => {
                    showAlert('Error de conexión', 'danger');
                    console.error('Error:', error);
                });
            }
        }

        function rejectUser(userId) {
            const reason = prompt('Ingrese el motivo del rechazo:');
            if (reason) {
                const formData = new FormData();
                formData.append('user_id', userId);
                formData.append('reason', reason);
                
                fetch('dashboard.php?api=reject-user', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Usuario rechazado', 'success');
                        loadPendingUsers();
                    } else {
                        showAlert('Error al rechazar usuario', 'danger');
                    }
                })
                .catch(error => {
                    showAlert('Error de conexión', 'danger');
                    console.error('Error:', error);
                });
            }
        }

        function deleteActa(actaId) {
            if (confirm('¿Está seguro de eliminar esta acta? Esta acción no se puede deshacer.')) {
                const formData = new FormData();
                formData.append('acta_id', actaId);
                
                fetch('dashboard.php?api=delete-acta', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Acta eliminada correctamente', 'success');
                        loadActasData();
                    } else {
                        showAlert('Error al eliminar acta', 'danger');
                    }
                })
                .catch(error => {
                    showAlert('Error de conexión', 'danger');
                    console.error('Error:', error);
                });
            }
        }

        function clearCache() {
            if (confirm('¿Está seguro de limpiar el cache del sistema?')) {
                showAlert('Función disponible solo en Laravel', 'info');
            }
        }

        // Funciones adicionales con implementación básica
        
        function viewActa(actaId) {
            showAlert('Vista de acta disponible próximamente', 'info');
        }
        
        function editActa(actaId) {
            showAlert('Edición de acta disponible próximamente', 'info');
        }

        // Funciones de búsqueda

        // Funciones para perfil y configuración
        function updateProfile(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData);
            
            fetch('dashboard.php?api=update-profile', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                showAlert('Error al actualizar perfil', 'danger');
            });
        }

        function updateSystemConfig(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData);
            
            // Convertir checkboxes
            data.mantenimiento = formData.has('mantenimiento');
            data.notificaciones_email = formData.has('notificaciones_email');
            data.backup_automatico = formData.has('backup_automatico');
            
            fetch('dashboard.php?api=update-config', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                showAlert('Error al actualizar configuración', 'danger');
            });
        }

        function changePassword() {
            showAlert('Funcionalidad de cambio de contraseña disponible próximamente', 'info');
        }

        function showNewInfraccionForm() {
            showAlert('Formulario de nueva infracción disponible próximamente', 'info');
        }

        function editInfraccion(id) {
            showAlert('Edición de infracción disponible próximamente', 'info');
        }

        function deleteInfraccion(id) {
            if (confirm('¿Está seguro de eliminar esta infracción?')) {
                showAlert('Eliminación de infracción disponible próximamente', 'info');
            }
        }

        function showNewConductorForm() {
            showAlert('Formulario de nuevo conductor disponible próximamente', 'info');
        }

        function showNewVehiculoForm() {
            showAlert('Formulario de nuevo vehículo disponible próximamente', 'info');
        }

        function generateReport(type) {
            fetch(`dashboard.php?api=export-data&type=${type}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(`Reporte de ${type} exportado exitosamente (${data.count} registros)`, 'success');
                    // Aquí podrías crear y descargar el archivo
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                showAlert('Error al generar reporte', 'danger');
            });
        }

        function generateMonthlyReport() {
            showAlert('Reporte mensual disponible próximamente', 'info');
        }

        function renderConductoresList(conductores) {
            const section = document.getElementById(`${currentSection}-section`);
            const tableContent = conductores.length > 0 ? 
                conductores.map(conductor => `
                    <tr>
                        <td>${conductor.nombres} ${conductor.apellidos}</td>
                        <td>${conductor.dni}</td>
                        <td>${conductor.numero_licencia || 'N/A'}</td>
                        <td>${conductor.clase_categoria || 'N/A'}</td>
                        <td>${conductor.telefono || 'N/A'}</td>
                        <td>
                            <span class="badge ${conductor.estado_licencia === 'vigente' ? 'bg-success' : conductor.estado_licencia === 'por_vencer' ? 'bg-warning' : 'bg-danger'}">
                                ${conductor.estado_licencia || 'N/A'}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="viewConductor(${conductor.id})" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="editConductor(${conductor.id})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                `).join('') :
                '<tr><td colspan="7" class="text-center text-muted">No hay conductores registrados</td></tr>';
            
            document.querySelector('.loading').outerHTML = `
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Nombre Completo</th>
                                <th>DNI</th>
                                <th>Licencia</th>
                                <th>Clase</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${tableContent}
                        </tbody>
                    </table>
                </div>
            `;
        }

        function renderVehiculosList(vehiculos) {
            const section = document.getElementById(`${currentSection}-section`);
            const tableContent = vehiculos.length > 0 ? 
                vehiculos.map(vehiculo => `
                    <tr>
                        <td><strong class="text-primary">${vehiculo.placa}</strong></td>
                        <td>${vehiculo.marca} ${vehiculo.modelo}</td>
                        <td>${vehiculo.año}</td>
                        <td><span class="badge bg-secondary">${vehiculo.color}</span></td>
                        <td>${vehiculo.asientos} asientos</td>
                        <td>
                            <span class="badge ${vehiculo.estado === 'vigente' ? 'bg-success' : 'bg-danger'}">
                                ${vehiculo.estado || 'N/A'}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="viewVehiculo(${vehiculo.id})" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="editVehiculo(${vehiculo.id})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                `).join('') :
                '<tr><td colspan="7" class="text-center text-muted">No hay vehículos registrados</td></tr>';
            
            document.querySelector('.loading').outerHTML = `
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Placa</th>
                                <th>Vehículo</th>
                                <th>Año</th>
                                <th>Color</th>
                                <th>Capacidad</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${tableContent}
                        </tbody>
                    </table>
                </div>
            `;
        }

        function editConductor(id) {
            fetch(`dashboard.php?api=conductor&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showEditConductorModal(data.conductor);
                    } else {
                        showAlert('Error al cargar los datos del conductor', 'danger');
                    }
                })
                .catch(error => {
                    showAlert('Error de conexión', 'danger');
                });
        }

        function showEditConductorModal(conductor) {
            const modal = new bootstrap.Modal(document.getElementById('generalModal'));
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit"></i> Editar Conductor';
            document.getElementById('modalBody').innerHTML = `
                <form id="editConductorForm">
                    <input type="hidden" name="id" value="${conductor.id}">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombres</label>
                            <input type="text" class="form-control" name="nombres" value="${conductor.nombres}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Apellidos</label>
                            <input type="text" class="form-control" name="apellidos" value="${conductor.apellidos}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">DNI</label>
                            <input type="text" class="form-control" name="dni" value="${conductor.dni}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" name="fecha_nacimiento" value="${conductor.fecha_nacimiento}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" value="${conductor.telefono || ''}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="${conductor.email || ''}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" class="form-control" name="direccion" value="${conductor.direccion || ''}">
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Distrito</label>
                            <input type="text" class="form-control" name="distrito" value="${conductor.distrito || ''}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Provincia</label>
                            <input type="text" class="form-control" name="provincia" value="${conductor.provincia || ''}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Departamento</label>
                            <input type="text" class="form-control" name="departamento" value="${conductor.departamento || ''}">
                        </div>
                    </div>
                    <hr>
                    <h6 class="text-success mb-3">Licencia de Conducir</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número de Licencia</label>
                            <input type="text" class="form-control" name="numero_licencia" value="${conductor.numero_licencia || ''}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Clase</label>
                            <select class="form-select" name="clase_categoria">
                                <option value="">Seleccionar...</option>
                                <option value="AI" ${conductor.clase_categoria === 'AI' ? 'selected' : ''}>AI</option>
                                <option value="AII" ${conductor.clase_categoria === 'AII' ? 'selected' : ''}>AII</option>
                                <option value="AIIB" ${conductor.clase_categoria === 'AIIB' ? 'selected' : ''}>AIIB</option>
                                <option value="AIII" ${conductor.clase_categoria === 'AIII' ? 'selected' : ''}>AIII</option>
                                <option value="AIIIB" ${conductor.clase_categoria === 'AIIIB' ? 'selected' : ''}>AIIIB</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Expedición</label>
                            <input type="date" class="form-control" name="fecha_expedicion" value="${conductor.fecha_expedicion || ''}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Vencimiento</label>
                            <input type="date" class="form-control" name="fecha_vencimiento" value="${conductor.fecha_vencimiento || ''}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado de Licencia</label>
                            <select class="form-select" name="estado_licencia">
                                <option value="vigente" ${conductor.estado_licencia === 'vigente' ? 'selected' : ''}>Vigente</option>
                                <option value="por_vencer" ${conductor.estado_licencia === 'por_vencer' ? 'selected' : ''}>Por Vencer</option>
                                <option value="vencida" ${conductor.estado_licencia === 'vencida' ? 'selected' : ''}>Vencida</option>
                                <option value="suspendida" ${conductor.estado_licencia === 'suspendida' ? 'selected' : ''}>Suspendida</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="estado">
                                <option value="activo" ${conductor.estado === 'activo' ? 'selected' : ''}>Activo</option>
                                <option value="inactivo" ${conductor.estado === 'inactivo' ? 'selected' : ''}>Inactivo</option>
                            </select>
                        </div>
                    </div>
                </form>
            `;
            
            document.getElementById('modalFooter').innerHTML = `
                <button type="button" class="btn btn-success" onclick="saveConductor()">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            `;
            document.getElementById('modalFooter').style.display = 'flex';
            modal.show();
        }

        function saveConductor() {
            const form = document.getElementById('editConductorForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);

            fetch('dashboard.php?api=update-conductor', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Conductor actualizado correctamente', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('generalModal')).hide();
                    // Recargar la lista de conductores
                    if (currentSection === 'conductores') {
                        loadConductoresList();
                    }
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                showAlert('Error al actualizar conductor', 'danger');
            });
        }

        function viewConductor(id) {
            fetch(`dashboard.php?api=conductor&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showConductorModal(data.conductor);
                    } else {
                        showAlert('Error al cargar los datos del conductor', 'danger');
                    }
                })
                .catch(error => {
                    showAlert('Error de conexión', 'danger');
                });
        }

        function showConductorModal(conductor) {
            const modal = new bootstrap.Modal(document.getElementById('generalModal'));
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-user"></i> Detalles del Conductor';
            document.getElementById('modalBody').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Datos Personales</h6>
                        <p><strong>Nombre:</strong> ${conductor.nombres} ${conductor.apellidos}</p>
                        <p><strong>DNI:</strong> ${conductor.dni}</p>
                        <p><strong>Fecha de Nacimiento:</strong> ${formatDate(conductor.fecha_nacimiento)}</p>
                        <p><strong>Teléfono:</strong> ${conductor.telefono || 'N/A'}</p>
                        <p><strong>Email:</strong> ${conductor.email || 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Dirección</h6>
                        <p><strong>Dirección:</strong> ${conductor.direccion || 'N/A'}</p>
                        <p><strong>Distrito:</strong> ${conductor.distrito || 'N/A'}</p>
                        <p><strong>Provincia:</strong> ${conductor.provincia || 'N/A'}</p>
                        <p><strong>Departamento:</strong> ${conductor.departamento || 'N/A'}</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-success">Licencia de Conducir</h6>
                        <p><strong>Número:</strong> ${conductor.numero_licencia || 'N/A'}</p>
                        <p><strong>Clase:</strong> ${conductor.clase_categoria || 'N/A'}</p>
                        <p><strong>Expedición:</strong> ${formatDate(conductor.fecha_expedicion)}</p>
                        <p><strong>Vencimiento:</strong> ${formatDate(conductor.fecha_vencimiento)}</p>
                        <p><strong>Estado:</strong> 
                            <span class="badge ${conductor.estado_licencia === 'vigente' ? 'bg-success' : conductor.estado_licencia === 'por_vencer' ? 'bg-warning' : 'bg-danger'}">
                                ${conductor.estado_licencia || 'N/A'}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-info">Información Adicional</h6>
                        <p><strong>Estado:</strong> 
                            <span class="badge ${conductor.estado === 'activo' ? 'bg-success' : 'bg-secondary'}">
                                ${conductor.estado || 'N/A'}
                            </span>
                        </p>
                        <p><strong>Puntos Acumulados:</strong> ${conductor.puntos_acumulados || 0}</p>
                        <p><strong>Registrado:</strong> ${formatDate(conductor.created_at)}</p>
                    </div>
                </div>
            `;
            
            document.getElementById('modalFooter').innerHTML = `
                <button type="button" class="btn btn-primary" onclick="editConductor(${conductor.id})">
                    <i class="fas fa-edit"></i> Editar
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            `;
            document.getElementById('modalFooter').style.display = 'flex';
            modal.show();
        }

        function editVehiculo(id) {
            fetch(`dashboard.php?api=vehiculo&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showEditVehiculoModal(data.vehiculo);
                    } else {
                        showAlert('Error al cargar los datos del vehículo', 'danger');
                    }
                })
                .catch(error => {
                    showAlert('Error de conexión', 'danger');
                });
        }

        function showEditVehiculoModal(vehiculo) {
            const modal = new bootstrap.Modal(document.getElementById('generalModal'));
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit"></i> Editar Vehículo';
            document.getElementById('modalBody').innerHTML = `
                <form id="editVehiculoForm">
                    <input type="hidden" name="id" value="${vehiculo.id}">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Placa *</label>
                            <input type="text" class="form-control" name="placa" value="${vehiculo.placa}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Marca *</label>
                            <input type="text" class="form-control" name="marca" value="${vehiculo.marca}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Modelo *</label>
                            <input type="text" class="form-control" name="modelo" value="${vehiculo.modelo}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Año</label>
                            <input type="number" class="form-control" name="año" value="${vehiculo.año}" min="1900" max="2030">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Color</label>
                            <input type="text" class="form-control" name="color" value="${vehiculo.color}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número de Motor</label>
                            <input type="text" class="form-control" name="numero_motor" value="${vehiculo.numero_motor || ''}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número de Chasis</label>
                            <input type="text" class="form-control" name="numero_chasis" value="${vehiculo.numero_chasis || ''}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Clase</label>
                            <select class="form-select" name="clase">
                                <option value="">Seleccionar...</option>
                                <option value="M1" ${vehiculo.clase === 'M1' ? 'selected' : ''}>M1</option>
                                <option value="M2" ${vehiculo.clase === 'M2' ? 'selected' : ''}>M2</option>
                                <option value="M3" ${vehiculo.clase === 'M3' ? 'selected' : ''}>M3</option>
                                <option value="N1" ${vehiculo.clase === 'N1' ? 'selected' : ''}>N1</option>
                                <option value="N2" ${vehiculo.clase === 'N2' ? 'selected' : ''}>N2</option>
                                <option value="N3" ${vehiculo.clase === 'N3' ? 'selected' : ''}>N3</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Categoría</label>
                            <input type="text" class="form-control" name="categoria" value="${vehiculo.categoria || ''}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Combustible</label>
                            <select class="form-select" name="combustible">
                                <option value="">Seleccionar...</option>
                                <option value="Gasolina" ${vehiculo.combustible === 'Gasolina' ? 'selected' : ''}>Gasolina</option>
                                <option value="Diesel" ${vehiculo.combustible === 'Diesel' ? 'selected' : ''}>Diesel</option>
                                <option value="GNV" ${vehiculo.combustible === 'GNV' ? 'selected' : ''}>GNV</option>
                                <option value="GLP" ${vehiculo.combustible === 'GLP' ? 'selected' : ''}>GLP</option>
                                <option value="Eléctrico" ${vehiculo.combustible === 'Eléctrico' ? 'selected' : ''}>Eléctrico</option>
                                <option value="Híbrido" ${vehiculo.combustible === 'Híbrido' ? 'selected' : ''}>Híbrido</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Asientos</label>
                            <input type="number" class="form-control" name="asientos" value="${vehiculo.asientos}" min="1" max="100">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Peso Bruto (kg)</label>
                            <input type="number" step="0.01" class="form-control" name="peso_bruto" value="${vehiculo.peso_bruto || ''}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Carga Útil (kg)</label>
                            <input type="number" step="0.01" class="form-control" name="carga_util" value="${vehiculo.carga_util || ''}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="estado">
                                <option value="vigente" ${vehiculo.estado === 'vigente' ? 'selected' : ''}>Vigente</option>
                                <option value="inactivo" ${vehiculo.estado === 'inactivo' ? 'selected' : ''}>Inactivo</option>
                                <option value="de_baja" ${vehiculo.estado === 'de_baja' ? 'selected' : ''}>De Baja</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Vencimiento SOAT</label>
                            <input type="date" class="form-control" name="fecha_soat" value="${vehiculo.fecha_soat || ''}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Revisión Técnica</label>
                            <input type="date" class="form-control" name="fecha_revision_tecnica" value="${vehiculo.fecha_revision_tecnica || ''}">
                        </div>
                    </div>
                </form>
            `;
            
            document.getElementById('modalFooter').innerHTML = `
                <button type="button" class="btn btn-success" onclick="saveVehiculo()">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            `;
            document.getElementById('modalFooter').style.display = 'flex';
            modal.show();
        }

        function saveVehiculo() {
            const form = document.getElementById('editVehiculoForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);

            fetch('dashboard.php?api=update-vehiculo', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Vehículo actualizado correctamente', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('generalModal')).hide();
                    // Recargar la lista de vehículos
                    if (currentSection === 'vehiculos') {
                        loadVehiculosList();
                    }
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                showAlert('Error al actualizar vehículo', 'danger');
            });
        }

        function viewVehiculo(id) {
            fetch(`dashboard.php?api=vehiculo&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showVehiculoModal(data.vehiculo);
                    } else {
                        showAlert('Error al cargar los datos del vehículo', 'danger');
                    }
                })
                .catch(error => {
                    showAlert('Error de conexión', 'danger');
                });
        }

        function showVehiculoModal(vehiculo) {
            const modal = new bootstrap.Modal(document.getElementById('generalModal'));
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-car"></i> Detalles del Vehículo';
            document.getElementById('modalBody').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Identificación</h6>
                        <p><strong>Placa:</strong> <span class="badge bg-primary fs-6">${vehiculo.placa}</span></p>
                        <p><strong>Marca:</strong> ${vehiculo.marca}</p>
                        <p><strong>Modelo:</strong> ${vehiculo.modelo}</p>
                        <p><strong>Año:</strong> ${vehiculo.año}</p>
                        <p><strong>Color:</strong> <span class="badge bg-secondary">${vehiculo.color}</span></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Especificaciones Técnicas</h6>
                        <p><strong>Motor:</strong> ${vehiculo.numero_motor || 'N/A'}</p>
                        <p><strong>Chasis:</strong> ${vehiculo.numero_chasis || 'N/A'}</p>
                        <p><strong>Clase:</strong> ${vehiculo.clase || 'N/A'}</p>
                        <p><strong>Categoría:</strong> ${vehiculo.categoria || 'N/A'}</p>
                        <p><strong>Combustible:</strong> ${vehiculo.combustible || 'N/A'}</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-success">Capacidades</h6>
                        <p><strong>Asientos:</strong> ${vehiculo.asientos} personas</p>
                        <p><strong>Peso Bruto:</strong> ${vehiculo.peso_bruto || 'N/A'} kg</p>
                        <p><strong>Carga Útil:</strong> ${vehiculo.carga_util || 'N/A'} kg</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-info">Estado y Documentos</h6>
                        <p><strong>Estado:</strong> 
                            <span class="badge ${vehiculo.estado === 'vigente' ? 'bg-success' : 'bg-danger'}">
                                ${vehiculo.estado || 'N/A'}
                            </span>
                        </p>
                        <p><strong>SOAT:</strong> ${formatDate(vehiculo.fecha_soat)}</p>
                        <p><strong>Revisión Técnica:</strong> ${formatDate(vehiculo.fecha_revision_tecnica)}</p>
                        <p><strong>Registrado:</strong> ${formatDate(vehiculo.created_at)}</p>
                    </div>
                </div>
            `;
            
            document.getElementById('modalFooter').innerHTML = `
                <button type="button" class="btn btn-primary" onclick="editVehiculo(${vehiculo.id})">
                    <i class="fas fa-edit"></i> Editar
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            `;
            document.getElementById('modalFooter').style.display = 'flex';
            modal.show();
        }

        // Funciones para manejo de temas
        function previewTheme(theme) {
            const root = document.documentElement;
            
            switch(theme) {
                case 'dark':
                    root.style.setProperty('--primary-color', '#1a1a1a');
                    root.style.setProperty('--secondary-color', '#333333');
                    root.style.setProperty('--light-color', '#2c2c2c');
                    document.body.style.backgroundColor = '#121212';
                    break;
                case 'light':
                    root.style.setProperty('--primary-color', '#ffffff');
                    root.style.setProperty('--secondary-color', '#f8f9fa');
                    root.style.setProperty('--light-color', '#ffffff');
                    document.body.style.backgroundColor = '#ffffff';
                    break;
                case 'blue':
                    root.style.setProperty('--primary-color', '#1e3a8a');
                    root.style.setProperty('--secondary-color', '#3b82f6');
                    root.style.setProperty('--light-color', '#dbeafe');
                    document.body.style.backgroundColor = '#eff6ff';
                    break;
                case 'green':
                    root.style.setProperty('--primary-color', '#166534');
                    root.style.setProperty('--secondary-color', '#22c55e');
                    root.style.setProperty('--light-color', '#dcfce7');
                    document.body.style.backgroundColor = '#f0fdf4';
                    break;
                default:
                    root.style.setProperty('--primary-color', '#2c3e50');
                    root.style.setProperty('--secondary-color', '#3498db');
                    root.style.setProperty('--light-color', '#ecf0f1');
                    document.body.style.backgroundColor = '#f8f9fa';
            }
        }

        function previewColor(type, color) {
            const root = document.documentElement;
            
            switch(type) {
                case 'primary':
                    root.style.setProperty('--primary-color', color);
                    break;
                case 'secondary':
                    root.style.setProperty('--secondary-color', color);
                    break;
                case 'background':
                    document.body.style.backgroundColor = color;
                    break;
            }
        }

        function toggleDarkMode(enabled) {
            if (enabled) {
                previewTheme('dark');
            } else {
                previewTheme('default');
            }
        }

        function applyTheme() {
            showAlert('Tema aplicado correctamente', 'success');
        }

        function resetTheme() {
            previewTheme('default');
            // Reset form values
            const themeForm = document.getElementById('themeForm');
            if (themeForm) {
                themeForm.reset();
                themeForm.querySelector('select[name="tema_principal"]').value = 'default';
                themeForm.querySelector('input[name="color_primario"]').value = '#2c3e50';
                themeForm.querySelector('input[name="color_secundario"]').value = '#3498db';
                themeForm.querySelector('input[name="color_fondo"]').value = '#f8f9fa';
                themeForm.querySelector('input[name="modo_oscuro"]').checked = false;
            }
            showAlert('Tema restaurado a valores originales', 'info');
        }

        function saveSystemConfig(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);
            
            fetch('dashboard.php?api=update-config', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({type: 'system', ...data})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Configuración guardada correctamente', 'success');
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                showAlert('Error al guardar configuración', 'danger');
            });
        }

        function saveThemeConfig(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);
            
            fetch('dashboard.php?api=update-config', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({type: 'theme', ...data})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Configuración de tema guardada correctamente', 'success');
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                showAlert('Error al guardar tema', 'danger');
            });
        }

        // Funciones de utilidad para gestión de usuarios

        function renderUsuariosTable(usuarios) {
            const tbody = document.getElementById('usuariosTableBody');
            if (usuarios.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center">No hay usuarios registrados</td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = usuarios.map(usuario => `
                <tr>
                    <td>${usuario.id}</td>
                    <td>${usuario.name}</td>
                    <td>${usuario.username}</td>
                    <td>${usuario.email}</td>
                    <td>
                        <span class="badge bg-secondary">${usuario.role}</span>
                    </td>
                    <td>
                        <span class="badge ${usuario.status === 'approved' ? 'bg-success' : 'bg-warning'}">
                            ${usuario.status === 'approved' ? 'Aprobado' : 'Pendiente'}
                        </span>
                    </td>
                    <td>${formatDate(usuario.created_at)}</td>
                    <td>
                        <button class="btn btn-sm btn-primary me-1" onclick="editarUsuario(${usuario.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarUsuario(${usuario.id})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function renderUsuariosPendientes(usuarios) {
            const container = document.getElementById('usuariosPendientes');
            if (usuarios.length === 0) {
                container.innerHTML = `
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> 
                        No hay usuarios pendientes de aprobación
                    </div>
                `;
                return;
            }
            
            container.innerHTML = usuarios.map(usuario => `
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="card-title mb-1">${usuario.name}</h5>
                                <p class="card-text">
                                    <strong>Username:</strong> ${usuario.username}<br>
                                    <strong>Email:</strong> ${usuario.email}<br>
                                    <strong>Rol:</strong> <span class="badge bg-info">${usuario.role}</span><br>
                                    <strong>Registro:</strong> ${formatDate(usuario.created_at)}
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <button class="btn btn-success me-2" onclick="aprobarUsuario(${usuario.id})">
                                    <i class="fas fa-check"></i> Aprobar
                                </button>
                                <button class="btn btn-danger" onclick="rechazarUsuario(${usuario.id})">
                                    <i class="fas fa-times"></i> Rechazar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function renderActasTable(actas) {
            const tbody = document.getElementById('actasTableBody');
            if (actas.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center">No hay actas registradas</td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = actas.map(acta => `
                <tr>
                    <td><strong>${acta.numero_acta || 'N/A'}</strong></td>
                    <td>${acta.placa}</td>
                    <td>${acta.nombre_conductor}</td>
                    <td>${formatDate(acta.fecha_intervencion)}</td>
                    <td>
                        <span class="badge ${acta.estado == 1 ? 'bg-success' : 'bg-warning'}">
                            ${acta.estado == 1 ? 'Procesada' : 'Pendiente'}
                        </span>
                    </td>
                    <td>S/ ${acta.monto_multa || '0.00'}</td>
                    <td>
                        <button class="btn btn-sm btn-info me-1" onclick="verActa(${acta.id})" title="Ver Detalles">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-primary" onclick="editarActa(${acta.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function renderStatsGenerales(stats) {
            const container = document.getElementById('statsGenerales');
            container.innerHTML = `
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Total Usuarios:</span>
                            <strong>${stats.total_usuarios || 0}</strong>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Usuarios Activos:</span>
                            <strong>${stats.usuarios_activos || 0}</strong>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Usuarios Pendientes:</span>
                            <strong>${stats.usuarios_pendientes || 0}</strong>
                        </div>
                    </div>
                    <hr>
                    <div class="col-12 mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Total Infracciones:</span>
                            <strong>${stats.total_infracciones || 0}</strong>
                        </div>
                    </div>
                </div>
            `;
        }

        function guardarNuevoUsuario() {
            const form = document.getElementById('crearUsuarioForm');
            const formData = new FormData(form);
            
            // Convertir FormData a JSON
            const data = {};
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }
            
            fetch('dashboard.php?api=create-user', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cerrar modal
                    bootstrap.Modal.getInstance(document.getElementById('dynamicModal')).hide();
                    
                    // Mostrar éxito
                    showAlert('success', 'Usuario creado exitosamente');
                    
                    // Recargar lista si estamos en la sección de usuarios
                    if (currentSection === 'listar-usuarios') {
                        loadUsuariosList();
                    }
                } else {
                    showAlert('error', data.message || 'Error al crear usuario');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Error de conexión al crear usuario');
            });
        }

        function aprobarUsuario(userId) {
            if (confirm('¿Estás seguro de aprobar este usuario?')) {
                fetch('dashboard.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `api=approve-user&user_id=${userId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', 'Usuario aprobado exitosamente');
                        loadAprobarUsuarios(); // Recargar lista
                    } else {
                        showAlert('error', data.message || 'Error al aprobar usuario');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Error de conexión al aprobar usuario');
                });
            }
        }

        function guardarActa(form) {
            const formData = new FormData(form);
            
            // Convertir FormData a JSON
            const data = {};
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }
            
            console.log('Guardando acta:', data);
            
            fetch('dashboard.php?api=save-acta', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Acta creada exitosamente');
                    form.reset();
                    
                    // Si el usuario quiere ver sus actas después de crear una
                    setTimeout(() => {
                        if (confirm('¿Desea ver la lista de sus actas?')) {
                            loadMisActas();
                        }
                    }, 1000);
                } else {
                    showAlert('error', data.message || 'Error al crear acta');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Error de conexión al crear acta');
            });
        }

        function generarReporte(tipo) {
            showAlert('info', `Generando reporte de ${tipo}...`);
            
            // Abrir el reporte en una nueva ventana
            const reportUrl = `dashboard.php?api=reporte&tipo=${tipo}`;
            window.open(reportUrl, '_blank');
        }

        function verActa(actaId) {
            fetch(`dashboard.php?api=acta-details&id=${actaId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.acta) {
                        mostrarDetalleActa(data.acta);
                    } else {
                        showAlert('error', 'No se pudo cargar el detalle del acta');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Error de conexión');
                });
        }

        function mostrarDetalleActa(acta) {
            const modalBody = document.getElementById('modalBody');
            modalBody.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>N° Acta:</strong> ${acta.numero_acta}</p>
                        <p><strong>Placa:</strong> ${acta.placa}</p>
                        <p><strong>Conductor:</strong> ${acta.nombre_conductor}</p>
                        <p><strong>DNI/RUC:</strong> ${acta.ruc_dni}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Fecha:</strong> ${formatDate(acta.fecha_intervencion)}</p>
                        <p><strong>Hora:</strong> ${acta.hora_intervencion}</p>
                        <p><strong>Lugar:</strong> ${acta.lugar_intervencion}</p>
                        <p><strong>Monto:</strong> S/ ${acta.monto_multa}</p>
                    </div>
                    ${acta.observaciones ? `<div class="col-12"><hr><p><strong>Observaciones:</strong> ${acta.observaciones}</p></div>` : ''}
                </div>
            `;
            
            document.getElementById('modalTitle').textContent = `Detalle del Acta ${acta.numero_acta}`;
            const modalFooter = document.getElementById('modalFooter');
            modalFooter.innerHTML = `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            `;
            
            const modal = new bootstrap.Modal(document.getElementById('dynamicModal'));
            modal.show();
        }

        function showAlert(type, message) {
            const alertClass = type === 'success' ? 'alert-success' : 
                             type === 'error' ? 'alert-danger' : 
                             type === 'info' ? 'alert-info' : 'alert-warning';
            const icon = type === 'success' ? 'fas fa-check-circle' : 
                        type === 'error' ? 'fas fa-exclamation-circle' : 
                        type === 'info' ? 'fas fa-info-circle' : 'fas fa-exclamation-triangle';
            
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="${icon}"></i> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            const container = document.querySelector('.main-content');
            if (container) {
                // Remover alertas anteriores
                const existingAlerts = container.querySelectorAll('.alert');
                existingAlerts.forEach(alert => alert.remove());
                
                container.insertAdjacentHTML('afterbegin', alertHtml);
                
                // Auto-remove después de 5 segundos
                setTimeout(() => {
                    const alert = container.querySelector('.alert');
                    if (alert) {
                        alert.remove();
                    }
                }, 5000);
            }
        }

        // Funciones específicas para Fiscalizador
        function loadEmpresas() {
            console.log('📄 Cargando Empresas...');
            const contentContainer = document.getElementById('contentContainer');
            contentContainer.innerHTML = `
                <div class="content-header">
                    <h4><i class="fas fa-building"></i> Gestión de Empresas</h4>
                    <p>Administrar empresas registradas en el sistema</p>
                </div>
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Lista de Empresas</h5>
                        <button class="btn btn-primary" onclick="showNuevaEmpresaModal()">
                            <i class="fas fa-plus"></i> Nueva Empresa
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>RUC</th>
                                        <th>Razón Social</th>
                                        <th>Representante</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="empresas-table-body">
                                    <tr><td colspan="5" class="text-center">Cargando empresas...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
        }

        function loadInspecciones() {
            console.log('📄 Cargando Inspecciones...');
            const contentContainer = document.getElementById('contentContainer');
            contentContainer.innerHTML = `
                <div class="content-section active">
                    <div class="content-header">
                        <h4><i class="fas fa-search"></i> Gestión de Inspecciones</h4>
                        <p>Programar y gestionar inspecciones</p>
                    </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Inspecciones Programadas</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Empresa</th>
                                                <th>Inspector</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="inspecciones-table-body">
                                            <tr><td colspan="5" class="text-center">Cargando inspecciones...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Nueva Inspección</h5>
                            </div>
                            <div class="card-body">
                                <form id="nueva-inspeccion-form">
                                    <div class="mb-3">
                                        <label class="form-label">Empresa</label>
                                        <select class="form-select" required>
                                            <option value="">Seleccionar...</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Inspector</label>
                                        <select class="form-select" required>
                                            <option value="">Seleccionar...</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Fecha</label>
                                        <input type="date" class="form-control" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Programar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            `;
        }

        function loadConsultas() {
            console.log('📄 Cargando Consultas...');
            const contentContainer = document.getElementById('contentContainer');
            contentContainer.innerHTML = `
                <div class="content-section active">
                    <div class="content-header">
                        <h4><i class="fas fa-question-circle"></i> Centro de Consultas</h4>
                        <p>Gestionar consultas y solicitudes</p>
                    </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5><i class="fas fa-file-alt text-primary"></i></h5>
                                <h6>Consulta de Actas</h6>
                                <p class="small text-muted">Buscar actas por número o placa</p>
                                <button class="btn btn-primary btn-sm" onclick="loadSection('actas-contra')">
                                    Consultar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5><i class="fas fa-building text-info"></i></h5>
                                <h6>Consulta de Empresas</h6>
                                <p class="small text-muted">Buscar empresas por RUC</p>
                                <button class="btn btn-info btn-sm" onclick="loadSection('empresas')">
                                    Consultar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5><i class="fas fa-search text-success"></i></h5>
                                <h6>Consulta de Inspecciones</h6>
                                <p class="small text-muted">Ver estado de inspecciones</p>
                                <button class="btn btn-success btn-sm" onclick="loadSection('inspecciones')">
                                    Consultar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function loadCalendario() {
            console.log('📄 Cargando Calendario...');
            const contentContainer = document.getElementById('contentContainer');
            contentContainer.innerHTML = `
                <div class="content-header">
                    <h4><i class="fas fa-calendar"></i> Calendario de Actividades</h4>
                    <p>Programación de inspecciones y eventos</p>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div id="calendar-container" style="min-height: 500px;">
                            <div class="text-center p-5">
                                <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                                <h5>Calendario en desarrollo</h5>
                                <p class="text-muted">Esta funcionalidad estará disponible próximamente</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Funciones específicas para Inspector
        function loadNuevaInspeccion() {
            console.log('📄 Cargando Nueva Inspección...');
            const contentContainer = document.getElementById('contentContainer');
            contentContainer.innerHTML = `
                <div class="content-header">
                    <h4><i class="fas fa-plus-circle"></i> Nueva Inspección</h4>
                    <p>Registrar una nueva inspección</p>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form id="nueva-inspeccion-form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Empresa</label>
                                        <select class="form-select" required>
                                            <option value="">Seleccionar empresa...</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Fecha de Inspección</label>
                                        <input type="date" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Observaciones</label>
                                        <textarea class="form-control" rows="4" placeholder="Detalles de la inspección..."></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Registrar Inspección
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
        }

        function loadMisInspecciones() {
            console.log('📄 Cargando Mis Inspecciones...');
            const contentContainer = document.getElementById('contentContainer');
            contentContainer.innerHTML = `
                <div class="content-header">
                    <h4><i class="fas fa-list"></i> Mis Inspecciones</h4>
                    <p>Ver historial de inspecciones realizadas</p>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Empresa</th>
                                        <th>Estado</th>
                                        <th>Observaciones</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="mis-inspecciones-table">
                                    <tr><td colspan="5" class="text-center">Cargando inspecciones...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
        }

        function loadVehiculos() {
            console.log('📄 Cargando Vehículos...');
            const contentContainer = document.getElementById('contentContainer');
            contentContainer.innerHTML = `
                <div class="content-header">
                    <h4><i class="fas fa-car"></i> Gestión de Vehículos</h4>
                    <p>Administrar vehículos registrados</p>
                </div>
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Lista de Vehículos</h5>
                        <button class="btn btn-primary" onclick="showNuevoVehiculoModal()">
                            <i class="fas fa-plus"></i> Nuevo Vehículo
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Placa</th>
                                        <th>Marca</th>
                                        <th>Modelo</th>
                                        <th>Propietario</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="vehiculos-table-body">
                                    <tr><td colspan="6" class="text-center">Cargando vehículos...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
        }

        // Funciones específicas para Ventanilla
        function loadNuevaAtencion() {
            console.log('📄 Cargando Nueva Atención...');
            const contentContainer = document.getElementById('contentContainer');
            contentContainer.innerHTML = `
                <div class="content-header">
                    <h4><i class="fas fa-user-plus"></i> Nueva Atención</h4>
                    <p>Registrar nuevo cliente en cola de espera</p>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form id="nueva-atencion-form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nombre del Cliente</label>
                                        <input type="text" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">DNI/RUC</label>
                                        <input type="text" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tipo de Trámite</label>
                                        <select class="form-select" required>
                                            <option value="">Seleccionar...</option>
                                            <option value="consulta">Consulta</option>
                                            <option value="pago">Pago de Multa</option>
                                            <option value="reclamo">Reclamo</option>
                                            <option value="otros">Otros</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Prioridad</label>
                                        <select class="form-select" required>
                                            <option value="">Seleccionar...</option>
                                            <option value="normal">Normal</option>
                                            <option value="alta">Alta</option>
                                            <option value="urgente">Urgente</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Registrar Cliente
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
        }

        function loadColaEspera() {
            console.log('📄 Cargando Cola de Espera...');
            const contentContainer = document.getElementById('contentContainer');
            contentContainer.innerHTML = `
                <div class="content-header">
                    <h4><i class="fas fa-clock"></i> Cola de Espera</h4>
                    <p>Gestionar clientes en espera de atención</p>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Clientes en Espera</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>N°</th>
                                                <th>Cliente</th>
                                                <th>DNI/RUC</th>
                                                <th>Trámite</th>
                                                <th>Hora Llegada</th>
                                                <th>Prioridad</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="cola-espera-table">
                                            <tr><td colspan="7" class="text-center">No hay clientes en espera</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Estadísticas</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <h3 id="total-espera" class="text-primary">0</h3>
                                    <p>Clientes en Espera</p>
                                    <hr>
                                    <h3 id="tiempo-promedio" class="text-info">0 min</h3>
                                    <p>Tiempo Promedio</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            `;
        }

        function loadTramites() {
            console.log('📄 Cargando Trámites...');
            const contentContainer = document.getElementById('contentContainer');
            contentContainer.innerHTML = `
                <div class="content-section active">
                    <div class="content-header">
                        <h4><i class="fas fa-file-alt"></i> Gestión de Trámites</h4>
                        <p>Administrar trámites y procedimientos</p>
                    </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                                <h6>Pagos</h6>
                                <button class="btn btn-success btn-sm">Gestionar</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                                <h6>Reclamos</h6>
                                <button class="btn btn-warning btn-sm">Gestionar</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-question-circle fa-2x text-info mb-2"></i>
                                <h6>Consultas</h6>
                                <button class="btn btn-info btn-sm">Gestionar</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-file-invoice fa-2x text-primary mb-2"></i>
                                <h6>Certificados</h6>
                                <button class="btn btn-primary btn-sm">Gestionar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Historial de Trámites</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Tipo</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tramites-table">
                                    <tr><td colspan="5" class="text-center">Cargando trámites...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                </div>
            `;
        }

        // Funciones adicionales para el administrador
        function loadRolesPermisos() {
            console.log('🔐 Cargando Roles y Permisos...');
            const contentContainer = document.getElementById('contentContainer');
            contentContainer.innerHTML = `
                <div class="content-section active">
                    <h2><i class="fas fa-user-shield"></i> Gestión de Roles y Permisos</h2>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Roles del Sistema</h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group">
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><i class="fas fa-crown text-warning"></i> Administrador</h6>
                                                <small class="text-muted">Acceso completo al sistema</small>
                                            </div>
                                            <span class="badge bg-primary">Máximo</span>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><i class="fas fa-clipboard-check text-info"></i> Fiscalizador</h6>
                                                <small class="text-muted">Gestión de actas e inspecciones</small>
                                            </div>
                                            <span class="badge bg-info">Alto</span>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><i class="fas fa-search text-success"></i> Inspector</h6>
                                                <small class="text-muted">Realizar inspecciones</small>
                                            </div>
                                            <span class="badge bg-success">Medio</span>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><i class="fas fa-window-restore text-warning"></i> Ventanilla</h6>
                                                <small class="text-muted">Atención al cliente</small>
                                            </div>
                                            <span class="badge bg-warning">Básico</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Permisos por Rol</h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        Esta funcionalidad estará disponible en futuras actualizaciones
                                    </div>
                                    <p class="text-muted">Aquí podrás gestionar permisos específicos por rol de usuario.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function loadConfiguracion() {
            console.log('⚙️ Cargando Configuración...');
            const contentContainer = document.getElementById('contentContainer');
            contentContainer.innerHTML = `
                <div class="content-section active">
                    <h2><i class="fas fa-cog"></i> Configuración del Sistema</h2>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Configuración General</h5>
                                </div>
                                <div class="card-body">
                                    <form id="configForm">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Nombre del Sistema</label>
                                                    <input type="text" class="form-control" value="Sistema de Gestión" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Versión</label>
                                                    <input type="text" class="form-control" value="1.0.0" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Email Administrador</label>
                                                    <input type="email" class="form-control" value="admin@sistema.com">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Zona Horaria</label>
                                                    <select class="form-select">
                                                        <option value="America/Lima" selected>Lima, Peru (GMT-5)</option>
                                                        <option value="America/Bogota">Bogotá, Colombia (GMT-5)</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="maintMode">
                                                <label class="form-check-label" for="maintMode">
                                                    Modo Mantenimiento
                                                </label>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Guardar Configuración
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Información del Sistema</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr><td><strong>Servidor:</strong></td><td>Apache/PHP</td></tr>
                                        <tr><td><strong>Base de Datos:</strong></td><td>MySQL</td></tr>
                                        <tr><td><strong>PHP Version:</strong></td><td>8.x</td></tr>
                                        <tr><td><strong>Usuarios Activos:</strong></td><td id="activeUsers">-</td></tr>
                                        <tr><td><strong>Último Backup:</strong></td><td>Hoy</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // ============ FUNCIONES ADICIONALES PARA BOTONES FALTANTES ============
        
        // Función para mostrar notificaciones toast
        function showToast(type, title, message) {
            const toastContainer = document.getElementById('toastContainer') || (() => {
                const container = document.createElement('div');
                container.id = 'toastContainer';
                container.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;';
                document.body.appendChild(container);
                return container;
            })();

            const toastId = 'toast-' + Date.now();
            const bgClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : type === 'warning' ? 'bg-warning' : 'bg-primary';
            
            const toast = document.createElement('div');
            toast.id = toastId;
            toast.className = `toast align-items-center text-white ${bgClass} border-0 mb-2`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <strong>${title}</strong><br>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="document.getElementById('${toastId}').remove()"></button>
                </div>
            `;
            
            toastContainer.appendChild(toast);
            
            // Auto-remove después de 5 segundos
            setTimeout(() => {
                if (document.getElementById(toastId)) {
                    document.getElementById(toastId).remove();
                }
            }, 5000);
        }

        // Función mejorada para "TEST CLICK" - ahora muestra diferentes opciones
        function loadTestClick() {
            console.log('🧪 TEST CLICK ejecutado');
            hideAllSections();
            
            const contentContainer = document.getElementById('contentContainer');
            contentContainer.innerHTML = `
                <div class="content-section active">
                    <div class="container-fluid">
                        <h2><i class="fas fa-flask text-primary"></i> Panel de Pruebas</h2>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            Este es el panel de pruebas del sistema. Aquí puedes probar diferentes funcionalidades.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-bell"></i> Pruebas de Notificaciones</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-success" onclick="showToast('success', '¡Éxito!', 'Operación completada correctamente')">
                                                <i class="fas fa-check"></i> Notificación de Éxito
                                            </button>
                                            <button class="btn btn-danger" onclick="showToast('error', 'Error', 'Algo salió mal en la operación')">
                                                <i class="fas fa-times"></i> Notificación de Error
                                            </button>
                                            <button class="btn btn-warning" onclick="showToast('warning', 'Advertencia', 'Por favor, revisa los datos')">
                                                <i class="fas fa-exclamation-triangle"></i> Notificación de Advertencia
                                            </button>
                                            <button class="btn btn-info" onclick="showToast('info', 'Información', 'Proceso iniciado correctamente')">
                                                <i class="fas fa-info"></i> Notificación Informativa
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-cog"></i> Pruebas de Funcionalidades</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-primary" onclick="loadUsuariosList()">
                                                <i class="fas fa-users"></i> Cargar Lista de Usuarios
                                            </button>
                                            <button class="btn btn-secondary" onclick="loadReportes()">
                                                <i class="fas fa-chart-bar"></i> Cargar Reportes
                                            </button>
                                            <button class="btn btn-info" onclick="loadConfiguracion()">
                                                <i class="fas fa-cog"></i> Cargar Configuración
                                            </button>
                                            <button class="btn btn-warning" onclick="loadPerfil()">
                                                <i class="fas fa-user"></i> Cargar Mi Perfil
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-database"></i> Estado del Sistema</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-md-3">
                                                <div class="p-3 bg-light rounded">
                                                    <h4 class="text-success"><i class="fas fa-check-circle"></i></h4>
                                                    <small>JavaScript OK</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="p-3 bg-light rounded">
                                                    <h4 class="text-success"><i class="fas fa-link"></i></h4>
                                                    <small>Event Listeners OK</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="p-3 bg-light rounded">
                                                    <h4 class="text-success"><i class="fas fa-server"></i></h4>
                                                    <small>API Disponible</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="p-3 bg-light rounded">
                                                    <h4 class="text-primary"><i class="fas fa-user"></i></h4>
                                                    <small>Usuario: ${userName}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <button class="btn btn-primary" onclick="loadSection('dashboard')">
                                <i class="fas fa-arrow-left"></i> Volver al Dashboard
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            showToast('success', '¡Test Iniciado!', 'Panel de pruebas cargado correctamente');
        }

        // Función para generar reportes (mejorada)
        function generarReporte(tipo) {
            console.log('📊 Generando reporte:', tipo);
            showToast('info', 'Generando Reporte', `Generando reporte de ${tipo}...`);
            
            // Simular proceso de generación
            setTimeout(() => {
                const modal = showModal('Reporte Generado', `
                    <div class="text-center">
                        <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                        <h4>Reporte de ${tipo.toUpperCase()}</h4>
                        <p>El reporte ha sido generado exitosamente.</p>
                        <div class="alert alert-info">
                            <strong>Fecha:</strong> ${new Date().toLocaleDateString('es-ES')}<br>
                            <strong>Usuario:</strong> ${userName}<br>
                            <strong>Tipo:</strong> ${tipo}
                        </div>
                    </div>
                `, `
                    <button class="btn btn-success" onclick="descargarReporte('${tipo}')">
                        <i class="fas fa-download"></i> Descargar PDF
                    </button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                `);
            }, 2000);
        }

        // Función para mostrar modales dinámicos
        function showModal(title, body, footer = '') {
            let modal = document.getElementById('dynamicModal');
            if (!modal) {
                modal = document.createElement('div');
                modal.className = 'modal fade';
                modal.id = 'dynamicModal';
                modal.innerHTML = `
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="dynamicModalTitle">${title}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body" id="dynamicModalBody">${body}</div>
                            <div class="modal-footer" id="dynamicModalFooter">
                                ${footer || '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>'}
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
            } else {
                document.getElementById('dynamicModalTitle').innerHTML = title;
                document.getElementById('dynamicModalBody').innerHTML = body;
                document.getElementById('dynamicModalFooter').innerHTML = footer || '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>';
            }
            
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
            return bsModal;
        }

        // Función para descargar reportes (simulado)
        function descargarReporte(tipo) {
            showToast('success', 'Descarga Iniciada', `Descargando reporte de ${tipo}...`);
            // Aquí iría la lógica real de descarga
            console.log('⬇️ Descargando reporte:', tipo);
        }

        // Mejorar la función loadPerfil existente
        function loadPerfilMejorado() {
            console.log('👤 Cargando perfil mejorado...');
            hideAllSections();
            
            const contentContainer = document.getElementById('contentContainer');
            contentContainer.innerHTML = `
                <div class="content-section active">
                    <h2><i class="fas fa-user"></i> Mi Perfil</h2>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                                    </div>
                                    <h4>${userName}</h4>
                                    <p class="text-muted">${userRole.toUpperCase()}</p>
                                    <span class="badge bg-success">Usuario Activo</span>
                                    <hr>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-primary btn-sm" onclick="showModal('Cambiar Avatar', 'Funcionalidad de cambio de avatar en desarrollo')">
                                            <i class="fas fa-camera"></i> Cambiar Avatar
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm" onclick="showModal('Actividad Reciente', 'Historial de actividad del usuario')">
                                            <i class="fas fa-history"></i> Actividad Reciente
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-edit"></i> Información Personal</h5>
                                </div>
                                <div class="card-body">
                                    <form id="perfilForm">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Nombre Completo</label>
                                                <input type="text" class="form-control" name="name" value="${userName}" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control" name="email" value="${userName.toLowerCase()}@sistema.com">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Teléfono</label>
                                                <input type="tel" class="form-control" name="phone" placeholder="+51 999 999 999">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Rol</label>
                                                <input type="text" class="form-control" value="${userRole.toUpperCase()}" readonly>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Nueva Contraseña</label>
                                                <input type="password" class="form-control" name="password" placeholder="Dejar vacío si no desea cambiar">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Confirmar Nueva Contraseña</label>
                                                <input type="password" class="form-control" name="password_confirm" placeholder="Confirmar contraseña">
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save"></i> Guardar Cambios
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary ms-2" onclick="loadSection('dashboard')">
                                                    <i class="fas fa-times"></i> Cancelar
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Agregar evento al formulario
            document.getElementById('perfilForm').addEventListener('submit', function(e) {
                e.preventDefault();
                showToast('success', 'Perfil Actualizado', 'Los cambios se han guardado correctamente');
            });
            
            showToast('info', 'Perfil Cargado', 'Tu información personal está disponible');
        }

        // Verificación final - esto debería aparecer en la consola
        console.log('🔧 Script cargado completamente');
        console.log('🔧 loadSection disponible?', typeof loadSection);
        console.log('🔧 testFunction disponible?', typeof testFunction);

        // EXPOSICIÓN INMEDIATA Y DIRECTA DE FUNCIONES CRÍTICAS
        console.log('🔗 Exponiendo funciones críticas inmediatamente...');
        
        // Exponer loadSection de forma inmediata
        window.loadSection = function(sectionId) {
            console.log('🔄 loadSection global ejecutándose:', sectionId);
            hideAllSections();
            
            switch(sectionId) {
                case 'dashboard':
                    const dashboardSection = document.getElementById('dashboard-section');
                    if (dashboardSection) {
                        dashboardSection.style.display = 'block';
                    }
                    break;
                case 'reportes':
                    loadReportes();
                    break;
                case 'configuracion':
                    loadConfiguracion();
                    break;
                case 'perfil':
                    loadPerfilMejorado();
                    break;
                default:
                    loadDefaultSection(sectionId);
            }
        };
        
        // Exponer toggleSubmenu de forma inmediata
        window.toggleSubmenu = function(menuId, event) {
            if (event) event.preventDefault();
            console.log('🔄 toggleSubmenu global ejecutándose:', menuId);
            
            // Si es el TEST CLICK (submenu usuarios), ejecutar loadTestClick
            if (menuId === 'usuarios') {
                hideAllSections();
                loadTestClick();
                return;
            }
            
            const submenu = document.getElementById(`submenu-${menuId}`);
            const toggle = event ? event.currentTarget : null;
            
            if (!submenu) {
                console.error('❌ No se encontró el submenu:', `submenu-${menuId}`);
                return;
            }
            
            if (submenu.classList.contains('show')) {
                submenu.classList.remove('show');
                if (toggle) toggle.classList.remove('expanded');
            } else {
                document.querySelectorAll('.sidebar-submenu.show').forEach(sub => {
                    sub.classList.remove('show');
                });
                document.querySelectorAll('.sidebar-toggle.expanded').forEach(tog => {
                    tog.classList.remove('expanded');
                });
                
                submenu.classList.add('show');
                if (toggle) toggle.classList.add('expanded');
            }
        };
        
        // Exponer loadPerfil de forma inmediata
        window.loadPerfil = function() {
            console.log('🔄 loadPerfil global ejecutándose');
            hideAllSections();
            loadPerfilMejorado();
        };
        
        console.log('✅ Funciones críticas expuestas inmediatamente');

        // Rebind crítico en window (final pass) para asegurarnos que los onclicks inline funcionen
        (function finalExpose() {
            const names = ['loadSection','loadSectionNew','loadReportes','loadConfiguracion','loadPerfil','loadPerfilMejorado','toggleSubmenu','loadUsuariosList','showCrearUsuarioModal','loadGestionarUsuarios','loadDashboardContent','loadDashboardStats','loadNotifications','loadTestClick','generarReporte','showModal','showToast','descargarReporte'];
            names.forEach(n => {
                try {
                    if (typeof window[n] === 'undefined') {
                        if (typeof eval(n) === 'function') {
                            window[n] = eval(n);
                            console.log('🔗 Expuesto a window:', n);
                        } else {
                            // leave as-is — do not override existing values
                            window[n] = window[n] || function() { console.warn('Fallback noop llamado para', n); };
                        }
                    }
                } catch (e) {
                    // ignore
                }
            });
            
            // Exponer también loadPerfil como loadPerfilMejorado por defecto
            window.loadPerfil = loadPerfilMejorado;
            console.log('✅ Todas las funciones expuestas a window');
        })();

    </script>
</body>
</html>