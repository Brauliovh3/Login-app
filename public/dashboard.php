<?php
session_start();

$config = require __DIR__ . '/../config/database.php';

// Clase principal del Dashboard
class DashboardApp {
    private $pdo;
    private $user;
    private $userRole;
    private $userName;
    
    public function __construct() {
        $this->connectDatabase();
        
        // Manejar llamadas API antes de autenticación completa
        if (isset($_GET['api'])) {
            // Allow public registration without authentication
            $apiName = $_GET['api'] ?? '';
            if ($apiName === 'register') {
                $this->apiRegisterUser();
                exit;
            }

            $this->handleApiRequest();
            exit;
        }
        
        $this->authenticateUser();
    }
    
    private function connectDatabase() {
        global $config;
        try {
            $dsn = "mysql:host={$config['host']};dbname={$config['name']}";
            $this->pdo = new PDO($dsn, $config['user'], $config['pass'], $config['options']);
        } catch(PDOException $e) {
            $this->showLoginForm("Error de conexión a la base de datos: " . $e->getMessage());
            exit;
        }
    }
    
    private function handleApiRequest() {
        header('Content-Type: application/json');
        
        // Verificar que el usuario esté autenticado para APIs
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'No autenticado']);
            return;
        }
        
        // Obtener datos del usuario para las APIs
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $this->user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$this->user || $this->user['status'] !== 'approved') {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Usuario no válido']);
            return;
        }
        
        $this->userRole = $this->user['role'];
        $this->userName = $this->user['name'];
        
        $api = $_GET['api'];
        $method = $_SERVER['REQUEST_METHOD'];
        
        try {
            switch ($api) {
                case 'users':
                    if ($method === 'GET') {
                        $this->apiGetUsers();
                    } elseif ($method === 'POST') {
                        $this->apiCreateUser();
                    }
                    break;
                    
                case 'user':
                    if ($method === 'PUT') {
                        $this->apiUpdateUser();
                    } elseif ($method === 'DELETE') {
                        $this->apiDeleteUser();
                    }
                    break;
                    
                case 'approve-user':
                    if ($method === 'POST') {
                        $userId = $_POST['user_id'] ?? 0;
                        echo json_encode($this->approveUser($userId));
                    } else {
                        http_response_code(405);
                        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                    }
                    break;
                    
                case 'reject-user':
                    if ($method === 'POST') {
                        $userId = $_POST['user_id'] ?? 0;
                        $reason = $_POST['reason'] ?? '';
                        echo json_encode($this->rejectUser($userId, $reason));
                    } else {
                        http_response_code(405);
                        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                    }
                    break;

                case 'pending-users':
                    if ($method === 'GET') {
                        $this->apiGetPendingUsers();
                    } else {
                        http_response_code(405);
                        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                    }
                    break;
                    
                case 'pending-users':
                    echo json_encode($this->getPendingUsers());
                    break;
                    
                case 'obtener_actas_fiscalizador':
                    if ($method === 'POST') {
                        $input = json_decode(file_get_contents('php://input'), true);
                        $fiscalizador_id = $input['fiscalizador_id'] ?? null;
                        echo json_encode($this->getActasFiscalizador($fiscalizador_id));
                    } else {
                        http_response_code(405);
                        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                    }
                    break;
                    
                case 'actas':
                    echo json_encode($this->getActas());
                    break;

                case 'guardar_acta':
                    if ($method === 'POST') {
                        echo json_encode($this->saveActa());
                    } else {
                        http_response_code(405);
                        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                    }
                    break;
                    
                case 'notifications':
                    echo json_encode($this->getUserNotifications());
                    break;
                    
                default:
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'API endpoint no encontrado']);
                    break;
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    // Devuelve usuarios con estado pendiente
    private function apiGetPendingUsers() {
        $stmt = $this->pdo->prepare("SELECT id, name as nombre, username, email, role as rol_solicitado, status, created_at as fecha_solicitud FROM usuarios WHERE status = 'pending' OR status = 'pendiente' ORDER BY created_at DESC");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'users' => $users]);
    }
    
    private function apiGetUsers() {
        $stmt = $this->pdo->prepare("SELECT id, name, username, email, role, status, created_at FROM usuarios ORDER BY created_at DESC");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'users' => $users]);
    }
    
    private function apiCreateUser() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Si no hay JSON, intentar leer de POST
        if (!$input) {
            $input = $_POST;
        }
        
        $nombre = $input['nombre'] ?? '';
        $username = $input['username'] ?? '';
        $email = $input['email'] ?? '';
        $rol = $input['rol'] ?? '';
        $password = $input['password'] ?? '';
        $estado = $input['estado'] ?? 'activo';
        
        // Validaciones
        if (empty($nombre) || empty($username) || empty($email) || empty($rol) || empty($password)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
            return;
        }
        
        // Verificar duplicados
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetchColumn() > 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Username o email ya existe']);
            return;
        }
        
        // Crear usuario
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $status = $estado === 'activo' ? 'approved' : 'pending';
        
        $stmt = $this->pdo->prepare("INSERT INTO usuarios (name, username, email, password, role, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$nombre, $username, $email, $hashedPassword, $rol, $status]);
        
        $userId = $this->pdo->lastInsertId();
        
        echo json_encode(['success' => true, 'message' => 'Usuario creado exitosamente', 'user_id' => $userId]);
    }

    // Endpoint público para registro de usuarios (quedan en estado pending)
    private function apiRegisterUser() {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        $nombre = trim($input['nombre'] ?? '');
        $username = trim($input['username'] ?? '');
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $rol = $input['rol'] ?? 'usuario';

        // Si no hay nombre, usar username como nombre
        if (empty($nombre)) {
            $nombre = $username;
        }

        if (empty($username) || empty($email) || empty($password)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Username, email y contraseña son obligatorios']);
            return;
        }

        // Verificar duplicados
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetchColumn() > 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Username o email ya existe']);
            return;
        }

        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("INSERT INTO usuarios (name, username, email, password, role, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
            $stmt->execute([$nombre, $username, $email, $hashedPassword, $rol]);

            echo json_encode(['success' => true, 'message' => 'Usuario registrado con éxito. Su cuenta está pendiente de aprobación por un administrador.']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al registrar usuario: ' . $e->getMessage()]);
        }
    }
    
    private function apiUpdateUser() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $userId = $input['id'] ?? '';
        $nombre = $input['nombre'] ?? '';
        $username = $input['username'] ?? '';
        $email = $input['email'] ?? '';
        $rol = $input['rol'] ?? '';
        $estado = $input['estado'] ?? '';
        $password = $input['password'] ?? '';
        
        // Validaciones
        if (empty($userId) || empty($nombre) || empty($username) || empty($email) || empty($rol)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Campos obligatorios faltantes']);
            return;
        }
        
        // Verificar duplicados (excluyendo el usuario actual)
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE (username = ? OR email = ?) AND id != ?");
        $stmt->execute([$username, $email, $userId]);
        if ($stmt->fetchColumn() > 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Username o email ya existe']);
            return;
        }
        
        // Actualizar usuario
        $status = $estado === 'activo' ? 'approved' : 'pending';
        
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("UPDATE usuarios SET name = ?, username = ?, email = ?, password = ?, role = ?, status = ? WHERE id = ?");
            $stmt->execute([$nombre, $username, $email, $hashedPassword, $rol, $status, $userId]);
        } else {
            $stmt = $this->pdo->prepare("UPDATE usuarios SET name = ?, username = ?, email = ?, role = ?, status = ? WHERE id = ?");
            $stmt->execute([$nombre, $username, $email, $rol, $status, $userId]);
        }
        
        echo json_encode(['success' => true, 'message' => 'Usuario actualizado exitosamente']);
    }
    
    private function apiDeleteUser() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $userId = $input['id'] ?? '';
        
        if (empty($userId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID de usuario requerido']);
            return;
        }
        
        // No permitir que el usuario se elimine a sí mismo
        if ($userId == $_SESSION['user_id']) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No puedes eliminarte a ti mismo']);
            return;
        }
        
        $stmt = $this->pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$userId]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Usuario eliminado exitosamente']);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
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
                    max-width: 700px;
                    width: 90%;
                    margin: auto;
                    min-height: 500px;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
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
                    padding: 50px;
                    flex: 1;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
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
                        <div class="col-12">
                            <div class="login-container" style="max-width: 400px; width: 100%; margin: 0 auto;">
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
                                    
                                    <div style="display: flex; justify-content: center; align-items: center; min-height: 400px; width: 100%;">
                                        <div style="max-width: 600px; min-width: 350px; width: 100%;">
                                            <div class="card p-4 shadow-lg border-0" id="loginCard" style="border-radius: 18px; margin: 0 auto;">
                                                <h4 class="mb-3 text-center">Iniciar Sesión</h4>
                                                <form id="loginForm" method="POST" action="dashboard.php">
                                                    <input type="hidden" name="login_action" value="1">
                                                    <div class="mb-3">
                                                        <label class="form-label"><i class="fas fa-user"></i> Usuario o Email</label>
                                                        <input type="text" class="form-control" name="username" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label"><i class="fas fa-lock"></i> Contraseña</label>
                                                        <div class="password-container">
                                                            <input type="password" class="form-control" name="password" id="password" required>
                                                            <i class="fas fa-eye password-toggle" id="togglePassword" onclick="togglePasswordVisibility()"></i>
                                                        </div>
                                                    </div>
                                                    <div class="d-grid mb-2">
                                                        <button type="submit" class="btn btn-primary btn-login" style="font-size: 1.15rem; padding: 0.75rem 0; border-radius: 2rem;">
                                                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                                                        </button>
                                                    </div>
                                                    <div class="text-center">
                                                        <button type="button" id="showRegisterBtn" class="btn btn-link" style="font-size: 1rem;" onclick="showRegisterForm()">¿No tienes cuenta? <b>Registrarse</b></button>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="card p-4 shadow-lg border-0 d-none" id="registerCard" style="border-radius: 18px; margin: 0 auto;">
                                                <h4 class="mb-3 text-center">Registro de Nuevo Usuario</h4>
                                                <div id="registerAlert" class="alert d-none"></div>
                                                <form id="registerForm" method="POST" action="#" autocomplete="off">
                                                    <div class="mb-3">
                                                        <label class="form-label"><i class="fas fa-user-tag"></i> Nombre de usuario</label>
                                                        <input type="text" class="form-control" name="username" id="username" required placeholder="Elija un nombre de usuario único">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
                                                        <input type="email" class="form-control" name="email" id="email" required placeholder="tu@email.com">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label"><i class="fas fa-lock"></i> Contraseña</label>
                                                        <div class="password-container">
                                                            <input type="password" class="form-control" name="password" id="registerPassword" required placeholder="Mínimo 8 caracteres">
                                                            <i class="fas fa-eye password-toggle" id="toggleRegisterPassword" onclick="toggleRegisterPasswordVisibility()"></i>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label"><i class="fas fa-lock"></i> Confirmar contraseña</label>
                                                        <div class="password-container">
                                                            <input type="password" class="form-control" name="password_confirm" id="passwordConfirm" required placeholder="Repita la contraseña">
                                                            <i class="fas fa-eye password-toggle" id="toggleConfirmPassword" onclick="toggleConfirmPasswordVisibility()"></i>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label"><i class="fas fa-user-cog"></i> Rol</label>
                                                        <select class="form-select" name="rol" id="rol" required>
                                                            <option value="">Seleccione un rol</option>
                                                            <option value="administrador">Administrador</option>
                                                            <option value="fiscalizador">Fiscalizador</option>
                                                            <option value="ventanilla">Ventanilla</option>
                                                            <option value="inspector">Inspector</option>
                                                        </select>
                                                    </div>
                                                    <div class="d-grid mb-2">
                                                        <button type="submit" class="btn btn-success btn-login" id="registerSubmit" style="font-size: 1.15rem; padding: 0.75rem 0; border-radius: 2rem;">
                                                            <i class="fas fa-user-plus"></i> Registrarse
                                                        </button>
                                                    </div>
                                                    <div class="text-center">
                                                        <button type="button" class="btn btn-link" style="font-size: 1rem;" onclick="showLoginForm()">¿Ya tienes cuenta? Iniciar Sesión</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        // Funciones para toggle de contraseña en login (si no está en login.js)
                                        function togglePasswordVisibility() {
                                            const passwordInput = document.getElementById('password');
                                            const toggleIcon = document.getElementById('togglePassword');
                                            if (passwordInput && toggleIcon) {
                                                if (passwordInput.type === 'password') {
                                                    passwordInput.type = 'text';
                                                    toggleIcon.classList.remove('fa-eye');
                                                    toggleIcon.classList.add('fa-eye-slash');
                                                } else {
                                                    passwordInput.type = 'password';
                                                    toggleIcon.classList.remove('fa-eye-slash');
                                                    toggleIcon.classList.add('fa-eye');
                                                }
                                            }
                                        }

                                        // Funciones para toggle de contraseña en registro
                                        function toggleRegisterPasswordVisibility() {
                                            const passwordInput = document.getElementById('registerPassword');
                                            const toggleIcon = document.getElementById('toggleRegisterPassword');
                                            if (passwordInput && toggleIcon) {
                                                if (passwordInput.type === 'password') {
                                                    passwordInput.type = 'text';
                                                    toggleIcon.classList.remove('fa-eye');
                                                    toggleIcon.classList.add('fa-eye-slash');
                                                } else {
                                                    passwordInput.type = 'password';
                                                    toggleIcon.classList.remove('fa-eye-slash');
                                                    toggleIcon.classList.add('fa-eye');
                                                }
                                            }
                                        }

                                        function toggleConfirmPasswordVisibility() {
                                            const passwordInput = document.getElementById('passwordConfirm');
                                            const toggleIcon = document.getElementById('toggleConfirmPassword');
                                            if (passwordInput && toggleIcon) {
                                                if (passwordInput.type === 'password') {
                                                    passwordInput.type = 'text';
                                                    toggleIcon.classList.remove('fa-eye');
                                                    toggleIcon.classList.add('fa-eye-slash');
                                                } else {
                                                    passwordInput.type = 'password';
                                                    toggleIcon.classList.remove('fa-eye-slash');
                                                    toggleIcon.classList.add('fa-eye');
                                                }
                                            }
                                        }

                                        // Toggle entre login y register
                                        function showRegisterForm() {
                                            document.getElementById('loginCard').classList.add('d-none');
                                            document.getElementById('registerCard').classList.remove('d-none');
                                        }

                                        function showLoginForm() {
                                            document.getElementById('registerCard').classList.add('d-none');
                                            document.getElementById('loginCard').classList.remove('d-none');
                                            const alertDiv = document.getElementById('registerAlert');
                                            if (alertDiv) {
                                                alertDiv.classList.add('d-none');
                                            }
                                            const form = document.getElementById('registerForm');
                                            if (form) {
                                                form.reset();
                                            }
                                        }

                                        // Validación y envío AJAX para registro
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const registerForm = document.getElementById('registerForm');
                                            if (registerForm) {
                                                registerForm.addEventListener('submit', function(e) {
                                                    e.preventDefault();
                                                    
                                                    const password = document.getElementById('registerPassword').value;
                                                    const confirmPassword = document.getElementById('passwordConfirm').value;
                                                    
                                                    if (password !== confirmPassword) {
                                                        showAlert('Las contraseñas no coinciden', 'danger');
                                                        return false;
                                                    }
                                                    
                                                    if (password.length < 8) {
                                                        showAlert('La contraseña debe tener al menos 8 caracteres', 'danger');
                                                        return false;
                                                    }
                                                    
                                                    const formData = new FormData(registerForm);
                                                    const submitBtn = document.getElementById('registerSubmit');
                                                    if (submitBtn) {
                                                        submitBtn.disabled = true;
                                                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registrando...';
                                                    }
                                                    
                                                    fetch(window.location.href + '?api=register', {
                                                        method: 'POST',
                                                        body: formData
                                                    })
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        if (data.success) {
                                                            showAlert(data.message || 'Registro exitoso. Un administrador revisará su solicitud.', 'success');
                                                            setTimeout(() => {
                                                                showLoginForm();
                                                            }, 3000);
                                                        } else {
                                                            showAlert(data.message || 'Error en el registro', 'danger');
                                                        }
                                                    })
                                                    .catch(error => {
                                                        showAlert('Error de conexión: ' + error.message, 'danger');
                                                    })
                                                    .finally(() => {
                                                        if (submitBtn) {
                                                            submitBtn.disabled = false;
                                                            submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Registrarse';
                                                        }
                                                    });
                                                });
                                            }
                                        });

                                        // Función para mostrar alertas en registro
                                        function showAlert(message, type) {
                                            const alertDiv = document.getElementById('registerAlert');
                                            if (alertDiv) {
                                                alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
                                                alertDiv.innerHTML = `
                                                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                                                    ${message}
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                `;
                                            }
                                        }
                                    </script>
        </body>
        </html>
        <?php
        exit;
    }
    
    // ...existing code...
    
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
            
            // Convertir estado numérico a texto
            foreach ($actas as &$acta) {
                switch((int)$acta['estado']) {
                    case 0:
                        $acta['estado'] = 'pendiente';
                        break;
                    case 1:
                        $acta['estado'] = 'procesada';
                        break;
                    case 2:
                        $acta['estado'] = 'anulada';
                        break;
                    case 3:
                        $acta['estado'] = 'pagada';
                        break;
                    default:
                        $acta['estado'] = 'pendiente';
                }
            }
            
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
                LEFT JOIN conductores c ON a.licencia = c.numero_licencia
                LEFT JOIN vehiculos v ON a.placa = v.placa
                LEFT JOIN infracciones i ON a.numero_acta = i.codigo_infraccion
                WHERE a.id = ?
            ");
            $stmt->execute([$actaId]);
            $acta = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($acta) {
                // Convertir estado numérico a texto
                switch((int)$acta['estado']) {
                    case 0:
                        $acta['estado'] = 'pendiente';
                        break;
                    case 1:
                        $acta['estado'] = 'procesada';
                        break;
                    case 2:
                        $acta['estado'] = 'anulada';
                        break;
                    case 3:
                        $acta['estado'] = 'pagada';
                        break;
                    default:
                        $acta['estado'] = 'pendiente';
                }
                
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
            
            //Si no se envió JSON intentar obtener de $_POST
            if (!$data) {
                $data = $_POST;
            }
            
            //Generar número de acta automático si no se proporciona
            if (empty($data['numero_acta'])) {
                $year = date('Y');
                $stmt = $this->pdo->query("SELECT MAX(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(numero_acta, '-', -1), '-', -1) AS UNSIGNED)) as last_num FROM actas WHERE numero_acta LIKE 'DRTC-APU-$year-%'");
                $lastNum = $stmt->fetch()['last_num'] ?? 0;
                $data['numero_acta'] = 'DRTC-APU-' . $year . '-' . str_pad($lastNum + 1, 6, '0', STR_PAD_LEFT);
            }
            
            //Preparar la consulta SQL con todos los campos correctos de la base de datos
            $sql = "INSERT INTO actas (
                numero_acta, lugar_intervencion, fecha_intervencion, hora_intervencion,
                inspector_responsable, tipo_servicio, tipo_agente, placa, placa_vehiculo,
                razon_social, ruc_dni, nombre_conductor, licencia,
                descripcion_hechos, monto_multa, estado, user_id, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $data['numero_acta'],
                $data['lugar_intervencion'] ?? null,
                $data['fecha_intervencion'] ?? date('Y-m-d'),
                $data['hora_intervencion'] ?? date('H:i:s'),
                $data['inspector_responsable'] ?? $this->userName,
                $data['tipo_servicio'] ?? null,
                $data['tipo_agente'] ?? 'Conductor', // Valor por defecto si no se especifica
                $data['placa'] ?? null,
                $data['placa_vehiculo'] ?? $data['placa'] ?? null, // Usar la misma placa si no se especifica placa_vehiculo
                $data['razon_social'] ?? null,
                $data['ruc_dni'] ?? null,
                $data['nombre_conductor'] ?? null,
                $data['licencia_conductor'] ?? $data['licencia'] ?? null,
                $data['descripcion_hechos'] ?? null,
                $data['monto_multa'] ?? null,
                $data['estado'] ?? 'pendiente',
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
                
                return [
                    'success' => true, 
                    'message' => 'Acta guardada correctamente', 
                    'acta_id' => $actaId,
                    'numero_acta' => $data['numero_acta']
                ];
            } else {
                return ['success' => false, 'message' => 'Error al guardar acta en la base de datos'];
            }
        } catch (Exception $e) {
            error_log("Error en saveActa(): " . $e->getMessage());
            return ['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()];
        }
    }
    
    private function updateActa($actaId) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $sql = "UPDATE actas SET 
                lugar_intervencion = ?, fecha_intervencion = ?, hora_intervencion = ?,
                inspector_responsable = ?, tipo_servicio = ?, tipo_agente = ?,
                placa = ?, placa_vehiculo = ?, razon_social = ?, ruc_dni = ?,
                licencia = ?, nombre_conductor = ?, clase_licencia = ?,
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
                $data['licencia_conductor'] ?? $data['licencia'] ?? null,
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
            // Simplificar la consulta para usar solo user_id
            $stmt = $this->pdo->prepare("
                SELECT * FROM notifications 
                WHERE user_id IS NULL OR user_id = ?
                ORDER BY created_at DESC 
                LIMIT 20
            ");
            $stmt->execute([$this->user['id']]);
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
                INSERT INTO notifications (title, message, user_id, created_at, type) 
                VALUES (?, ?, ?, NOW(), 'info')
            ");
            $result = $stmt->execute([$title, $message, $userId]);
            
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
                licencia, nombre_conductor, clase_licencia,
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
                $data['licencia_conductor'] ?? $data['licencia'] ?? '',
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
    
    public function getUserId() {
        return $this->user['id'] ?? 0;
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

    private function getActasFiscalizador($fiscalizadorId) {
        try {
            if (!$fiscalizadorId) {
                return ['success' => false, 'message' => 'ID de fiscalizador requerido'];
            }

            // Verificar que el usuario actual puede ver las actas (debe ser el mismo fiscalizador o un administrador)
            if (!in_array($this->userRole, ['administrador', 'superadmin', 'fiscalizador']) || 
                ($this->userRole === 'fiscalizador' && $this->user['id'] != $fiscalizadorId)) {
                return ['success' => false, 'message' => 'No tienes permisos para ver estas actas'];
            }

            $stmt = $this->pdo->prepare("
                SELECT a.*, u.name as fiscalizador_nombre 
                FROM actas a 
                LEFT JOIN usuarios u ON a.user_id = u.id 
                WHERE a.user_id = ? 
                ORDER BY a.created_at DESC
            ");
            
            $stmt->execute([$fiscalizadorId]);
            $actas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Convertir estado numérico a texto
            foreach ($actas as &$acta) {
                switch((int)$acta['estado']) {
                    case 0:
                        $acta['estado'] = 'pendiente';
                        break;
                    case 1:
                        $acta['estado'] = 'procesada';
                        break;
                    case 2:
                        $acta['estado'] = 'anulada';
                        break;
                    case 3:
                        $acta['estado'] = 'pagada';
                        break;
                    default:
                        $acta['estado'] = 'pendiente';
                }
            }
            
            return [
                'success' => true, 
                'actas' => $actas,
                'total' => count($actas),
                'fiscalizador_id' => $fiscalizadorId
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al obtener actas del fiscalizador: ' . $e->getMessage()];
        }
    }
}

// Inicializar la aplicación

$app = new DashboardApp();

// Variables para la vista
$usuario = $app->getUserName();
$rol = $app->getUserRole();

// Debug temporal - remover después
echo "<!-- DEBUG: Usuario: $usuario, Rol: $rol -->";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo bin2hex(random_bytes(32)); ?>">
    <title>Dashboard - Sistema de Gestión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/dashboard.css" rel="stylesheet">
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
            margin-left: 20px;
            padding-top: 5px;
            padding-bottom: 5px;
            overflow: visible;
        }

        .sidebar-submenu.show {
            display: block !important;
            max-height: 500px;
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
            display: none; /* Ocultar flecha para mejor apariencia */
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
                        <!-- Enlaces de perfil y configuración removidos por petición del administrador -->
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
                <a class="sidebar-link sidebar-toggle" href="#" onclick="toggleSubmenuAlt('usuarios', event); return false;">
                    <i class="fas fa-users"></i> Gestión de Usuarios
                    <i class="fas fa-chevron-down sidebar-arrow"></i>
                </a>
                <ul class="sidebar-submenu" id="submenu-usuarios" style="display: none !important;">
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="javascript:void(0)" onclick="loadUsuariosList()" data-section="listar-usuarios">
                            <i class="fas fa-list"></i> Lista de Usuarios
                        </a>
                    </li>
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="javascript:void(0)" onclick="loadAprobarUsuarios()" data-section="aprobar-usuarios">
                            <i class="fas fa-user-check"></i> Aprobar Usuarios
                        </a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link sidebar-toggle" href="#" onclick="toggleSubmenuAlt('actas', event); return false;">
                    <i class="fas fa-file-invoice"></i> Gestión de Actas
                    <i class="fas fa-chevron-down sidebar-arrow"></i>
                </a>
                <ul class="sidebar-submenu" id="submenu-actas" style="display: none !important;">
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="javascript:void(0)" onclick="loadActasList()" data-section="listar-actas">
                            <i class="fas fa-list"></i> Lista de Actas
                        </a>
                    </li>
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="javascript:void(0)" onclick="loadCrearActa()" data-section="crear-acta">
                            <i class="fas fa-plus-circle"></i> Crear Acta
                        </a>
                    </li>
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="javascript:void(0)" onclick="loadGestionarInfracciones()" data-section="gestionar-infracciones">
                            <i class="fas fa-exclamation-triangle"></i> Infracciones
                        </a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="javascript:void(0)" data-section="reportes">
                    <i class="fas fa-chart-bar"></i> Reportes
                </a>
            </li>
            <!-- Configuración eliminada del menú del administrador por petición -->
            <?php endif; ?>

            <?php if ($rol === 'fiscalizador'): ?>
            <!-- Menú para Fiscalizador -->
            <li class="sidebar-item">
                <a class="sidebar-link sidebar-toggle" href="#" onclick="toggleSubmenuAlt('actas', event)">
                    <i class="fas fa-clipboard-list"></i> Gestión de Actas
                </a>
                <ul class="sidebar-submenu" id="submenu-actas" style="display: none;">
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="#" onclick="loadActas(event)" data-section="crear-acta">
                            <i class="fas fa-plus-circle"></i> Crear Acta
                        </a>
                    </li>
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="#" onclick="loadActas(event)" data-section="mis-actas">
                            <i class="fas fa-user-edit"></i> Mis Actas
                        </a>
                    </li>
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="#" onclick="loadConductores()" data-section="buscar-conductor">
                            <i class="fas fa-search"></i> Buscar Conductor
                        </a>
                    </li>
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="#" onclick="loadVehiculos()" data-section="buscar-vehiculo">
                            <i class="fas fa-car"></i> Buscar Vehículo
                        </a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link sidebar-toggle" href="#" onclick="toggleSubmenu('inspecciones', event)">
                    <i class="fas fa-search"></i> Inspecciones
                </a>
                <ul class="sidebar-submenu" id="submenu-inspecciones">
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="#" onclick="loadInspecciones()" data-section="nueva-inspeccion">
                            <i class="fas fa-plus"></i> Nueva Inspección
                        </a>
                    </li>
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="#" onclick="loadInspecciones()" data-section="mis-inspecciones">
                            <i class="fas fa-list"></i> Mis Inspecciones
                        </a>
                    </li>
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="#" onclick="loadInspecciones()" data-section="inspecciones-pendientes">
                            <i class="fas fa-clock"></i> Pendientes
                        </a>
                    </li>
                </ul>
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
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="#" onclick="loadConsultas()" data-section="consultas-publico">
                            <i class="fas fa-question-circle"></i> Consultas Públicas
                        </a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link sidebar-toggle" href="#" onclick="toggleSubmenu('tramites', event)">
                    <i class="fas fa-folder-open"></i> Trámites
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
                
                <!-- Dashboard Stats - Solo visible en dashboard principal -->
                <div id="dashboardStats" class="row mb-4">
                    <!-- Las estadísticas se cargan dinámicamente -->
                </div>
                
                <!-- Panel de información del usuario (solo para debug) -->
                <div class="alert alert-success mb-3">
                    <h6><i class="fas fa-user-check"></i> Usuario Logueado:</h6>
                    <strong><?php echo htmlspecialchars($usuario); ?></strong> | Rol: <strong><?php echo htmlspecialchars($rol); ?></strong>
                    
                    <?php if ($rol === 'administrador' || $rol === 'admin'): ?>
                    <div class="mt-3 p-3 bg-light border rounded">
                        <h6><strong> Panel de Administrador</strong></h6>
                        <button class="btn btn-primary me-2" onclick="loadUsuariosList()" style="display: block !important;">
                            <i class="fas fa-users"></i> Lista de Usuarios
                        </button>
                        <button class="btn btn-success mt-2" onclick="loadAprobarUsuarios()" style="display: block !important;">
                            <i class="fas fa-user-check"></i> Aprobar Usuarios
                        </button>
                        <button class="btn btn-warning mt-2 me-2" onclick="toggleSubmenu('usuarios', null)" style="display: inline-block !important;">
                            <i class="fas fa-toggle-on"></i> Test Toggle Normal
                        </button>
                        <button class="btn btn-info mt-2" onclick="toggleSubmenuAlt('usuarios', null)" style="display: inline-block !important;">
                            <i class="fas fa-toggle-off"></i> Test Toggle Alt
                        </button>
                        <button class="btn btn-danger mt-2" onclick="forceShowSubmenu('usuarios')" style="display: inline-block !important;">
                            <i class="fas fa-eye"></i> Force Show
                        </button>
                        <button class="btn btn-warning mt-2" onclick="diagnosticarSubmenu('usuarios')" style="display: inline-block !important;">
                            <i class="fas fa-search"></i> Diagnóstico CSS
                        </button>
                        <p class="small text-muted mt-2">Si estos botones funcionan, el problema es solo visual en el sidebar.</p>
                    </div>
                    <?php endif; ?>
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
                    <!-- Ajuste aquí: solo btn-close, sin btn-close-white -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
    <script src="js/usuarios-utils.js"></script>
    
    <?php if ($rol === 'administrador' || $rol === 'admin'): ?>
    <!-- JavaScript específico para administrador -->
    <script src="js/administrador.js"></script>
    <?php endif; ?>
    
    <?php if ($rol === 'fiscalizador'): ?>
    <!-- JavaScript específico para fiscalizador -->
    <script src="js/fiscalizador.js"></script>
    <script src="js/fiscalizador-actas.js"></script>
    <?php endif; ?>
    
    <?php if ($rol === 'ventanilla'): ?>
    <!-- JavaScript específico para ventanilla -->
    <script src="js/ventanilla.js"></script>
    <?php endif; ?>
    
    <?php if ($rol === 'inspector'): ?>
    <!-- JavaScript específico para inspector -->
    <script src="js/inspector.js"></script>
    <?php endif; ?>
    <script>
        // Variables globales para los archivos JS
        window.dashboardUserRole = '<?php echo $rol; ?>';
        window.dashboardUserName = '<?php echo htmlspecialchars($app->getUserName()); ?>';
        window.dashboardUserId = <?php echo $app->getUserId(); ?>;
        
        console.log(' Archivos JS cargados correctamente');
        console.log('Dashboard cargado para:', window.dashboardUserName, 'Rol:', window.dashboardUserRole);
        
        // Debug: Verificar elementos del DOM
        document.addEventListener('DOMContentLoaded', function() {
            console.log(' Verificando elementos del sidebar...');
            const submenuUsuarios = document.getElementById('submenu-usuarios');
            console.log('Submenu usuarios encontrado:', !!submenuUsuarios);
            
            const botonesGestionUsuarios = document.querySelectorAll('[onclick*="loadUsuariosList"], [onclick*="loadAprobarUsuarios"]');
            console.log('Botones de gestión de usuarios encontrados:', botonesGestionUsuarios.length);
            
            // Verificar si las funciones están disponibles
            console.log('Función loadUsuariosList disponible:', typeof window.loadUsuariosList);
            console.log('Función loadAprobarUsuarios disponible:', typeof window.loadAprobarUsuarios);
        });
    </script>

    <!-- ==================== CONTENEDOR DE NOTIFICACIONES ==================== -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;" id="toastContainer">
        <!-- Los toasts se agregarán dinámicamente aquí -->
    </div>

    <style>
        /* Estilos personalizados para notificaciones */
        .custom-toast {
            min-width: 350px;
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .custom-toast.toast-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .custom-toast.toast-error {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            color: white;
        }

        .custom-toast.toast-warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: #212529;
        }

        .custom-toast.toast-info {
            background: linear-gradient(135deg, #17a2b8 0%, #007bff 100%);
            color: white;
        }

        .custom-toast .toast-body {
            padding: 1rem;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .custom-toast .btn-close {
            filter: brightness(0) invert(1);
            margin: 0.5rem;
        }

        .custom-toast .toast-icon {
            font-size: 1.5rem;
            margin-right: 0.75rem;
        }

        .fade-in {
            animation: fadeInSlide 0.4s ease-out;
        }

        .fade-out {
            animation: fadeOutSlide 0.3s ease-in;
        }

        @keyframes fadeInSlide {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeOutSlide {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }
    </style>
</body>
</html>
