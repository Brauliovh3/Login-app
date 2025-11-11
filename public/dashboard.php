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
            exit; // CRÍTICO: Salir inmediatamente después de manejar API
        }
        
        $this->authenticateUser();
    }
    
    private function getActasByRole() {
        try {
            if (!$this->tableExists('actas')) {
                return ['success' => true, 'actas' => [], 'stats' => ['total_actas' => 0]];
            }

            // Determinar columnas disponibles de forma segura
            $hasAnioActa = $this->columnExists('actas', 'anio_acta');
            $hasPlacaVehiculo = $this->columnExists('actas', 'placa_vehiculo');
            $hasNombres = $this->columnExists('actas', 'nombres_conductor');
            $hasApellidos = $this->columnExists('actas', 'apellidos_conductor');
            $hasNombrePlano = $this->columnExists('actas', 'nombre_conductor');
            $hasMonto = $this->columnExists('actas', 'monto_multa');
            $hasEstado = $this->columnExists('actas', 'estado');
            $hasCreatedAt = $this->columnExists('actas', 'created_at');
            $hasFechaIntervencion = $this->columnExists('actas', 'fecha_intervencion');
            $hasFiscalizador = $this->columnExists('actas', 'fiscalizador_id');
            $hasCodigoInfraccion = $this->columnExists('actas', 'codigo_infraccion');
            $hasNumeroActa = $this->columnExists('actas', 'numero_acta');
            $hasPlaca = $this->columnExists('actas', 'placa');

            // Construir los campos del SELECT con fallbacks
            $select = [
                'id',
                ($hasNumeroActa ? 'numero_acta' : "'' AS numero_acta"),
                ($hasAnioActa 
                    ? 'anio_acta' 
                    : ($hasFechaIntervencion ? "YEAR(COALESCE(fecha_intervencion, NOW())) AS anio_acta" : 'YEAR(NOW()) AS anio_acta')),
                ($hasPlaca ? 'placa' : "'' AS placa"),
                ($hasPlacaVehiculo ? 'placa_vehiculo' : "placa AS placa_vehiculo"),
                ($hasNombres || $hasApellidos
                    ? "CONCAT(COALESCE(apellidos_conductor, ''), ' ', COALESCE(nombres_conductor, '')) AS nombre_conductor"
                    : ($hasNombrePlano ? 'nombre_conductor' : "'' AS nombre_conductor")),
                ($hasNombres ? 'nombres_conductor' : "NULL AS nombres_conductor"),
                ($hasApellidos ? 'apellidos_conductor' : "NULL AS apellidos_conductor"),
                'ruc_dni',
                ($hasCodigoInfraccion ? 'COALESCE(codigo_infraccion, \"N/A\") as codigo_infraccion' : "'N/A' AS codigo_infraccion"),
                ($hasMonto ? 'COALESCE(monto_multa, 0) as monto_multa' : '0 AS monto_multa'),
                ($hasEstado ? 'estado' : '0 AS estado'),
                ($hasEstado
                    ? "CASE WHEN estado = 0 THEN 'pendiente' WHEN estado = 1 THEN 'procesada' WHEN estado = 2 THEN 'anulada' WHEN estado = 3 THEN 'pagada' ELSE 'pendiente' END AS estado_texto"
                    : "'pendiente' AS estado_texto"),
                ($hasFechaIntervencion ? 'fecha_intervencion' : 'NULL AS fecha_intervencion'),
                ($hasCreatedAt 
                    ? 'created_at' 
                    : ($hasFechaIntervencion ? 'fecha_intervencion AS created_at' : 'NOW() AS created_at')),
                ($hasFechaIntervencion ? 'fecha_intervencion AS fecha_acta' : 'NOW() AS fecha_acta'),
                ($hasFiscalizador ? 'fiscalizador_id' : 'NULL AS fiscalizador_id')
            ];

            $sql = "SELECT " . implode(",\n                ", $select) . "\nFROM actas\nORDER BY id DESC\nLIMIT 200";
            
            $stmt = $this->pdo->query($sql);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            error_log('[DASH] getActasByRole filas=' . count($rows));
            
            return [
                'success' => true,
                'actas' => $rows,
                'stats' => ['total_actas' => count($rows)],
                'debug' => ['sql' => $sql]
            ];
        } catch (Exception $e) {
            error_log('Error en getActasByRole: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage(), 'actas' => [], 'stats' => ['total_actas' => 0]];
        }
    }

    private function getActasRaw() {
        try {
            $dbName = $this->pdo->query('SELECT DATABASE() AS db')->fetch(PDO::FETCH_ASSOC)['db'] ?? null;
            $actasExists = $this->tableExists('actas');
            
            // Intentar siempre contar y seleccionar, aunque tableExists falle (por colación/permisos)
            $count = 0; $rows = [];
            try {
                $countStmt = $this->pdo->query('SELECT COUNT(*) AS c FROM actas');
                $count = (int)($countStmt->fetch(PDO::FETCH_ASSOC)['c'] ?? 0);
            } catch (Exception $e) {
                error_log('COUNT actas error: ' . $e->getMessage());
            }
            try {
                $stmt = $this->pdo->query("SELECT * FROM actas ORDER BY id DESC LIMIT 200");
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            } catch (Exception $e) {
                error_log('SELECT actas error: ' . $e->getMessage());
            }
            
            error_log('[DASH] getActasRaw db=' . $dbName . ' existe_actas=' . ($actasExists? '1':'0') . ' count=' . $count);
            return [
                'success' => true,
                'db' => $dbName,
                'table_exists' => $actasExists,
                'count' => $count,
                'actas' => $rows
            ];
        } catch (Exception $e) {
            error_log('Error en getActasRaw: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage(), 'actas' => []];
        }
    }

    private function columnExists($table, $column) {
        try {
            $stmt = $this->pdo->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
            $stmt->execute([$column]);
            return $stmt->fetch() ? true : false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function tableExists($table) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) AS c FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
            $stmt->execute([$table]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && (int)$row['c'] > 0) { return true; }
            // Fallback con SHOW TABLES sin parámetros (evita escapes sobre '?')
            $table = str_replace(['`', '%', '_'], ['``', '\\%', '\\_'], $table);
            $res = $this->pdo->query("SHOW TABLES LIKE '" . $table . "'");
            return $res && $res->fetch() ? true : false;
        } catch (Exception $e) {
            error_log('tableExists error: ' . $e->getMessage());
            return false;
        }
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
        
        $api = $_GET['api'];
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Permitir lectura pública mínima para listar actas desde el panel
        $publicReadApis = ['actas', 'actas-admin', 'actas-raw', 'codigos-infracciones'];
        if (!isset($_SESSION['user_id']) && $method === 'GET' && in_array($api, $publicReadApis, true)) {
            switch ($api) {
                case 'actas':
                case 'actas-admin':
                case 'actas-raw':
                    echo json_encode($this->getActasRaw());
                    return;
                case 'codigos-infracciones':
                    echo json_encode($this->getCodigosInfracciones());
                    return;
            }
        }
        
        // Verificar que el usuario esté autenticado para el resto de APIs
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'No autenticado']);
            exit; // Salir inmediatamente
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
                    echo json_encode($this->getActasByRole());
                    break;

                case 'actas-raw':
                    echo json_encode($this->getActasRaw());
                    break;

                case 'infracciones':
                    echo json_encode($this->getInfracciones());
                    break;

                case 'carga-pasajeros':
                    if ($method === 'GET') {
                        echo json_encode($this->getCargaPasajeros());
                    } elseif ($method === 'POST') {
                        echo json_encode($this->createCargaPasajero());
                    }
                    break;

                case 'dashboard-stats':
                    if ($this->userRole === 'ventanilla') {
                        echo json_encode($this->getDashboardStatsVentanilla());
                    } else {
                        echo json_encode($this->getDashboardStats());
                    }
                    break;

                case 'actas-admin':
                    echo json_encode($this->getActasAdmin());
                    break;

                case 'guardar_acta':
                    if ($method === 'POST') {
                        echo json_encode($this->saveActa());
                    } else {
                        http_response_code(405);
                        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                    }
                    break;
                    
                case 'eliminar_acta':
                    if ($method === 'DELETE' || $method === 'POST') {
                        $data = json_decode(file_get_contents('php://input'), true);
                        if (!$data) {
                            $data = $_POST;
                        }
                        $actaId = $data['id'] ?? null;
                        if (!$actaId) {
                            echo json_encode(['success' => false, 'message' => 'ID de acta requerido']);
                        } else {
                            echo json_encode($this->deleteActa($actaId));
                        }
                    } else {
                        http_response_code(405);
                        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                    }
                    break;
                    
                case 'notifications':
                    echo json_encode($this->getUserNotifications());
                    break;
                    
                case 'codigos-infracciones':
                    echo json_encode($this->getCodigosInfracciones());
                    break;
                    
                case 'anular-acta':
                    if ($method === 'POST') {
                        echo json_encode($this->anularActa());
                    } else {
                        http_response_code(405);
                        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                    }
                    break;
                    
                case 'acta-details':
                    if ($method === 'GET') {
                        $actaId = $_GET['id'] ?? null;
                        if (!$actaId) {
                            echo json_encode(['success' => false, 'message' => 'ID de acta requerido']);
                        } else {
                            echo json_encode($this->getActaDetails($actaId));
                        }
                    } else {
                        http_response_code(405);
                        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                    }
                    break;
                    
                case 'update-acta':
                    if ($method === 'POST') {
                        $data = json_decode(file_get_contents('php://input'), true);
                        $actaId = $data['acta_id'] ?? null;
                        if (!$actaId) {
                            echo json_encode(['success' => false, 'message' => 'ID de acta requerido']);
                        } else {
                            echo json_encode($this->updateActa($actaId));
                        }
                    } else {
                        http_response_code(405);
                        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                    }
                    break;
                    
                case 'consultar-documento':
                    if ($method === 'POST') {
                        $input = json_decode(file_get_contents('php://input'), true);
                        $documento = $input['documento'] ?? null;
                        echo json_encode($this->consultarDocumento($documento));
                    } else {
                        http_response_code(405);
                        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                    }
                    break;
                    
                case 'consultar-actas':
                    if ($method === 'POST') {
                        $input = json_decode(file_get_contents('php://input'), true);
                        echo json_encode($this->consultarActasPorCriterio($input));
                    } else {
                        http_response_code(405);
                        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                    }
                    break;
                    
                case 'consultar-vehiculo':
                    if ($method === 'POST') {
                        $input = json_decode(file_get_contents('php://input'), true);
                        $placa = $input['placa'] ?? null;
                        echo json_encode($this->consultarVehiculoPorPlaca($placa));
                    } else {
                        http_response_code(405);
                        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                    }
                    break;
                    
                case 'consultar-conductor':
                    if ($method === 'POST') {
                        $input = json_decode(file_get_contents('php://input'), true);
                        echo json_encode($this->consultarConductorPorDocumento($input));
                    } else {
                        http_response_code(405);
                        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                    }
                    break;
                    
                case 'atender-cliente':
                    if ($method === 'POST') {
                        $input = json_decode(file_get_contents('php://input'), true);
                        echo json_encode($this->atenderCliente($input));
                    } else {
                        http_response_code(405);
                        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                    }
                    break;
                    
                case 'cancelar-cliente':
                    if ($method === 'POST') {
                        $input = json_decode(file_get_contents('php://input'), true);
                        echo json_encode($this->cancelarCliente($input));
                    } else {
                        http_response_code(405);
                        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                    }
                    break;
                    
                case 'procesar-tramite':
                    if ($method === 'POST') {
                        $input = json_decode(file_get_contents('php://input'), true);
                        echo json_encode($this->procesarTramite($input));
                    } else {
                        http_response_code(405);
                        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                    }
                    break;
                    
                case 'detalle-tramite':
                    if ($method === 'GET') {
                        $id = $_GET['id'] ?? null;
                        echo json_encode($this->getDetalleTramite($id));
                    } else {
                        http_response_code(405);
                        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                    }
                    break;
                    
                case 'eliminar-carga-pasajero':
                    if ($method === 'DELETE' || $method === 'POST') {
                        $data = json_decode(file_get_contents('php://input'), true);
                        if (!$data) { $data = $_POST; }
                        $id = $data['id'] ?? null;
                        if (!$id) {
                            echo json_encode(['success' => false, 'message' => 'ID requerido']);
                        } else {
                            echo json_encode($this->deleteCargaPasajero($id));
                        }
                    } else {
                        http_response_code(405);
                        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                    }
                    break;
                    
                case 'registrar-atencion':
                    if ($method === 'POST') {
                        echo json_encode($this->registrarAtencion());
                    } else {
                        http_response_code(405);
                        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                    }
                    break;
                    
                case 'cola-espera':
                    if ($method === 'GET') {
                        echo json_encode($this->getColaEspera());
                    } elseif ($method === 'POST') {
                        echo json_encode($this->agregarColaEspera());
                    } else {
                        http_response_code(405);
                        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                    }
                    break;
                    
                case 'tramites':
                    if ($method === 'GET') {
                        echo json_encode($this->getTramites());
                    } elseif ($method === 'POST') {
                        echo json_encode($this->registrarTramite());
                    } else {
                        http_response_code(405);
                        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                    }
                    break;
                    
                case 'actualizar-carga-pasajero':
                    if ($method === 'PUT' || $method === 'POST') {
                        echo json_encode($this->updateCargaPasajero());
                    } else {
                        http_response_code(405);
                        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                    }
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
        exit; // Asegurar que siempre salga después de manejar API
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
                    background: linear-gradient(135deg, #ff8c00 0%, #e67e22 100%);
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
                    background: linear-gradient(135deg, #ff8c00, #e67e22);
                    color: white;
                    padding: 15px 80px 30px 80px;
                    text-align: center;
                    flex: 0 0 auto;
                }

                .login-header .logo-img {
                    max-width: 80px !important;
                    height: auto;
                    border-radius: 8px;
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
                    padding: 50px 70px;
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
                    color: #ff8c00;
                }
                .form-control:focus {
                    border-color: #ff8c00;
                    box-shadow: 0 0 0 0.3rem rgba(255, 140, 0, 0.25);
                }
                .form-label {
                    font-size: 16px;
                    font-weight: 600;
                    margin-bottom: 8px;
                    color: #2c3e50;
                }
                .btn-login {
                    background: linear-gradient(135deg, #ff8c00, #e67e22);
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
                    background: linear-gradient(135deg, #e67e22, #d68910);
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
                            <div class="login-container" style="max-width: 500px; width: 100%; margin: 0 auto;">
                                <div class="login-header">
                                     <div class="logo-container mb-3">
                                         <img src="images/logo.png" alt="Logo Sistema" class="logo-img mx-auto d-block">
                                     </div>
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
                                        <div style="max-width: 700px; min-width: 400px; width: 100%;">
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
                                                        <button type="submit" class="btn btn-warning btn-login" style="font-size: 1.15rem; padding: 0.75rem 0; border-radius: 2rem; background: linear-gradient(135deg, #ff8c00, #e67e22) !important; border-color: #ff8c00 !important;">
                                                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                                                        </button>
                                                    </div>
                                                    <div class="text-center">
                                                        <button type="button" id="showRegisterBtn" class="btn btn-link" style="font-size: 1rem;" onclick="showRegisterForm()">¿No tienes cuenta? <b>Registrarse</b></button>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="card p-4 shadow-lg border-0 d-none" id="registerCard" style="border-radius: 18px; margin: 0 auto; max-width: 700px;">
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
                                                        <button type="submit" class="btn btn-warning btn-login" id="registerSubmit" style="font-size: 1.15rem; padding: 0.75rem 0; border-radius: 2rem; background: linear-gradient(135deg, #ff8c00, #e67e22) !important; border-color: #ff8c00 !important;">
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
                    // Estadísticas específicas para ventanilla
                    $stmt = $this->pdo->query("SELECT COUNT(*) as hoy FROM atenciones WHERE DATE(fecha_atencion) = CURDATE()");
                    $atencionesHoy = $stmt->fetch()['hoy'] ?? 0;
                    
                    $stmt = $this->pdo->query("SELECT COUNT(*) as completados FROM tramites WHERE estado = 'completado' AND DATE(fecha_registro) = CURDATE()");
                    $completados = $stmt->fetch()['completados'] ?? 0;
                    
                    $stmt = $this->pdo->query("SELECT COUNT(*) as cola FROM cola_espera WHERE estado = 'esperando'");
                    $cola = $stmt->fetch()['cola'] ?? 0;
                    
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
    
    
    private function getActaDetails($actaId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT a.*, 
                       u.name as user_name,
                       CASE 
                           WHEN a.estado = 0 THEN 'pendiente'
                           WHEN a.estado = 1 THEN 'procesada' 
                           WHEN a.estado = 2 THEN 'anulada'
                           WHEN a.estado = 3 THEN 'pagada'
                           ELSE 'pendiente'
                       END AS estado_texto
                FROM actas a 
                LEFT JOIN usuarios u ON a.fiscalizador_id = u.id
                WHERE a.id = ?
            ");
            $stmt->execute([$actaId]);
            $acta = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($acta) {
                // Usar el estado convertido a texto
                $acta['estado'] = $acta['estado_texto'];
                
                // Asegurar campos necesarios para la vista
                $acta['placa'] = $acta['placa'] ?? $acta['placa_vehiculo'] ?? 'N/A';
                $acta['conductor_nombre'] = trim(($acta['nombres_conductor'] ?? '') . ' ' . ($acta['apellidos_conductor'] ?? '')) ?: 'N/A';
                $acta['nombre_conductor'] = $acta['conductor_nombre'];
                $acta['descripcion'] = $acta['descripcion_infraccion'] ?? $acta['descripcion_hechos'] ?? 'Sin descripción';
                $acta['monto'] = $acta['monto_multa'] ?? '0.00';
                
                // Asegurar que el código de infracción esté disponible
                if (empty($acta['codigo_infraccion'])) {
                    $acta['codigo_infraccion'] = 'N/A';
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
        // LOG PARA DEBUG - TIMESTAMP: 2024-10-14 12:01
        error_log("=== NUEVO SAVEACTA INICIADO ===");
        
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) { $data = $_POST; }
            error_log("Datos recibidos: " . json_encode($data));

            // Insert directo y único en 'actas'
            if (!$this->tableExists('actas')) {
                // Crear tabla 'actas' mínima compatible
                $this->pdo->exec(
                    "CREATE TABLE IF NOT EXISTS `actas` (\n".
                    "  `id` INT NOT NULL AUTO_INCREMENT,\n".
                    "  `numero_acta` VARCHAR(50) NULL,\n".
                    "  `codigo_ds` VARCHAR(50) NULL,\n".
                    "  `lugar_intervencion` VARCHAR(150) NULL,\n".
                    "  `fecha_intervencion` DATE NULL,\n".
                    "  `hora_intervencion` TIME NULL,\n".
                    "  `inspector_responsable` VARCHAR(100) NULL,\n".
                    "  `tipo_servicio` VARCHAR(50) NULL,\n".
                    "  `tipo_agente` VARCHAR(50) NULL,\n".
                    "  `placa` VARCHAR(20) NULL,\n".
                    "  `placa_vehiculo` VARCHAR(20) NULL,\n".
                    "  `razon_social` VARCHAR(150) NULL,\n".
                    "  `ruc_dni` VARCHAR(20) NULL,\n".
                    "  `nombre_conductor` VARCHAR(150) NULL,\n".
                    "  `licencia` VARCHAR(50) NULL,\n".
                    "  `codigo_infraccion` VARCHAR(50) NULL,\n".
                    "  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,\n".
                    "  PRIMARY KEY (`id`)\n".
                    ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
                );
            }

            // Obtener columnas reales de 'actas' con metadatos
            $colsStmt = $this->pdo->query("SHOW COLUMNS FROM `actas`");
            $colsMeta = $colsStmt->fetchAll(PDO::FETCH_ASSOC);
            $colsArr = array_map(fn($r) => $r['Field'], $colsMeta);
            $colsSet = array_flip($colsArr);

            // Generar número de acta
            $actaInfo = $this->generarNumeroActa();
            
            // Mapeo fijo formulario -> columnas de 'actas'
            $mapping = [
                'ruc_dni' => $data['ruc_dni'] ?? null,
                'razon_social' => $data['razon_social'] ?? null,
                'placa_vehiculo' => $data['placa_vehiculo'] ?? ($data['placa'] ?? null),
                'placa' => $data['placa'] ?? ($data['placa_vehiculo'] ?? null),
                'tipo_agente' => $data['tipo_agente'] ?? null,
                'tipo_servicio' => $data['tipo_servicio'] ?? null,
                'apellidos_conductor' => $data['apellidos_conductor'] ?? null,
                'nombres_conductor' => $data['nombres_conductor'] ?? null,
                'licencia' => $data['licencia_conductor'] ?? ($data['licencia'] ?? null),
                'lugar_intervencion' => $data['lugar_intervencion'] ?? null,
                'provincia' => $data['provincia'] ?? null,
                'distrito' => $data['distrito'] ?? null,
                'fecha_intervencion' => $data['fecha_intervencion'] ?? date('Y-m-d'),
                'hora_intervencion' => $data['hora_intervencion'] ?? date('H:i:s'),
                'inspector_responsable' => $data['inspector_responsable'] ?? null,
                'codigo_infraccion' => $data['codigo_infraccion'] ?? ($data['informe'] ?? null),
                'descripcion_infraccion' => $data['descripcion_infraccion'] ?? null,
                'numero_acta' => $actaInfo['numero_acta'],
                'anio_acta' => $actaInfo['anio_acta'],
                'fiscalizador_id' => $_SESSION['user_id'] ?? null, // ID del usuario que crea el acta
                'user_id' => $_SESSION['user_id'] ?? null, // Mantener compatibilidad
            ];

            // Filtrar por columnas existentes
            $insertCols = [];
            $insertVals = [];
            foreach ($mapping as $col => $val) {
                if (isset($colsSet[$col])) { $insertCols[] = $col; $insertVals[] = $val; }
            }

            // Satisfacer columnas NOT NULL sin default obligatorias
            $requiredDefaults = [
                'numero_acta' => function() use ($mapping) { return $mapping['numero_acta'] ?? $this->generarNumeroActa()['numero_acta']; },
                'anio_acta' => function() use ($mapping) { return $mapping['anio_acta'] ?? date('Y'); },
                'codigo_ds' => function() { return ''; },
                'placa' => function() use ($mapping) { return $mapping['placa'] ?? ($mapping['placa_vehiculo'] ?? ''); },
                'fecha_intervencion' => function() use ($mapping) { return $mapping['fecha_intervencion'] ?? date('Y-m-d'); },
                'hora_intervencion' => function() use ($mapping) { return $mapping['hora_intervencion'] ?? date('H:i:s'); },
                'lugar_intervencion' => function() use ($mapping) { return $mapping['lugar_intervencion'] ?? ''; },
                'tipo_servicio' => function() use ($mapping) { return $mapping['tipo_servicio'] ?? ''; },
                'tipo_agente' => function() use ($mapping) { return $mapping['tipo_agente'] ?? ''; },
                'inspector_responsable' => function() use ($mapping) { return $mapping['inspector_responsable'] ?? ''; },
                'razon_social' => function() use ($mapping) { return $mapping['razon_social'] ?? ''; },
                'ruc_dni' => function() use ($mapping) { return $mapping['ruc_dni'] ?? ''; },
                'nombres_conductor' => function() use ($mapping) { return $mapping['nombres_conductor'] ?? ''; },
                'apellidos_conductor' => function() use ($mapping) { return $mapping['apellidos_conductor'] ?? ''; },
                'licencia' => function() use ($mapping) { return $mapping['licencia'] ?? ''; },
                'codigo_infraccion' => function() use ($mapping) { return $mapping['codigo_infraccion'] ?? ''; },
                'created_at' => function() { return date('Y-m-d H:i:s'); },
            ];

            foreach ($colsMeta as $colInfo) {
                $field = $colInfo['Field'];
                $isNullable = strtoupper($colInfo['Null']) !== 'NO' ? true : false;
                $hasDefault = $colInfo['Default'] !== null;
                $isAutoInc = strpos(strtolower($colInfo['Extra']), 'auto_increment') !== false;
                if ($isAutoInc) continue; // p.ej. id
                if (in_array($field, $insertCols, true)) continue; // ya seteado
                if (!$isNullable && !$hasDefault && isset($colsSet[$field]) && isset($requiredDefaults[$field])) {
                    $val = $requiredDefaults[$field]();
                    $insertCols[] = $field;
                    $insertVals[] = $val;
                }
            }

            if (empty($insertCols)) {
                return ['success' => false, 'message' => 'Esquema de actas incompatible (sin columnas válidas)'];
            }

            $placeholders = rtrim(str_repeat('?,', count($insertCols)), ',');
            $sql = "INSERT INTO `actas` (" . implode(',', $insertCols) . ") VALUES ($placeholders)";
            error_log("SQL Query: $sql");
            error_log("Columns: " . implode(',', $insertCols));
            error_log("Params: " . json_encode($insertVals));

            $stmt = $this->pdo->prepare($sql);
            $ok = $stmt->execute($insertVals);
            if (!$ok) { throw new Exception('Fallo insert en actas'); }

            return ['success' => true, 'message' => 'Acta guardada correctamente', 'acta_id' => $this->pdo->lastInsertId(), 'tabla' => 'actas'];

        } catch (Exception $e) {
            error_log("Error en saveActa(): " . $e->getMessage());
            return ['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()];
        }
    }
    
    private function updateActa($actaId) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                return ['success' => false, 'message' => 'Datos no válidos'];
            }
            
            // Construir la consulta dinámicamente basada en los campos disponibles
            $updateFields = [];
            $params = [];
            
            // Mapear campos del formulario a columnas de la base de datos
            $fieldMapping = [
                'placa' => 'placa',
                'placa_vehiculo' => 'placa_vehiculo', 
                'conductor_nombre' => 'nombres_conductor',
                'nombres_conductor' => 'nombres_conductor',
                'apellidos_conductor' => 'apellidos_conductor',
                'ruc_dni' => 'ruc_dni',
                'monto' => 'monto_multa',
                'monto_multa' => 'monto_multa',
                'descripcion' => 'descripcion_infraccion',
                'descripcion_hechos' => 'descripcion_hechos',
                'lugar_intervencion' => 'lugar_intervencion',
                'fecha_intervencion' => 'fecha_intervencion',
                'hora_intervencion' => 'hora_intervencion',
                'inspector_responsable' => 'inspector_responsable',
                'tipo_servicio' => 'tipo_servicio',
                'tipo_agente' => 'tipo_agente',
                'razon_social' => 'razon_social',
                'licencia' => 'licencia',
                'codigo_infraccion' => 'codigo_infraccion'
            ];
            
            foreach ($fieldMapping as $inputField => $dbField) {
                if (isset($data[$inputField])) {
                    $updateFields[] = "$dbField = ?";
                    $params[] = $data[$inputField];
                }
            }
            
            // Manejar el estado por separado (convertir texto a número)
            if (isset($data['estado'])) {
                $estadoNumerico = $this->convertirEstadoANumero($data['estado']);
                $updateFields[] = "estado = ?";
                $params[] = $estadoNumerico;
            }
            
            if (empty($updateFields)) {
                return ['success' => false, 'message' => 'No hay campos para actualizar'];
            }
            
            // Agregar timestamp de actualización si la columna existe
            if ($this->columnExists('actas', 'updated_at')) {
                $updateFields[] = "updated_at = NOW()";
            }
            
            $params[] = $actaId;
            
            $sql = "UPDATE actas SET " . implode(', ', $updateFields) . " WHERE id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($params);
            
            if ($result && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Acta actualizada correctamente'];
            } else {
                return ['success' => false, 'message' => 'No se pudo actualizar el acta o no se encontró'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()];
        }
    }
    
    private function convertirEstadoANumero($estadoTexto) {
        $estados = [
            'pendiente' => 0,
            'procesada' => 1,
            'procesado' => 1,
            'anulada' => 2,
            'anulado' => 2,
            'pagada' => 3,
            'pagado' => 3
        ];
        
        $estadoLower = strtolower($estadoTexto);
        return $estados[$estadoLower] ?? 0;
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
            // Verificar si la tabla existe, si no, crearla
            if (!$this->tableExists('infracciones')) {
                $this->createInfraccionesTable();
            }
            
            // Obtener estadísticas detalladas de infracciones
            $statsQuery = "
                SELECT 
                    COUNT(*) as total_infracciones,
                    SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) as activas,
                    SUM(CASE WHEN estado = 'inactivo' THEN 1 ELSE 0 END) as inactivas,
                    SUM(CASE WHEN gravedad = 'Leve' THEN 1 ELSE 0 END) as leves,
                    SUM(CASE WHEN gravedad = 'Grave' THEN 1 ELSE 0 END) as graves,
                    SUM(CASE WHEN gravedad = 'Muy grave' THEN 1 ELSE 0 END) as muy_graves,
                    SUM(CASE WHEN aplica_sobre = 'Transportista' THEN 1 ELSE 0 END) as transportista,
                    SUM(CASE WHEN aplica_sobre = 'Conductor' THEN 1 ELSE 0 END) as conductor,
                    SUM(CASE WHEN clase_pago = 'Pecuniaria' THEN 1 ELSE 0 END) as pecuniarias,
                    SUM(CASE WHEN clase_pago = 'No pecuniaria' THEN 1 ELSE 0 END) as no_pecuniarias
                FROM infracciones
            ";
            
            $statsStmt = $this->pdo->query($statsQuery);
            $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
            
            // Obtener infracciones con información completa
            $stmt = $this->pdo->query("
                SELECT 
                    id,
                    codigo_infraccion,
                    aplica_sobre,
                    reglamento,
                    norma_modificatoria,
                    infraccion as descripcion,
                    clase_pago,
                    sancion,
                    tipo,
                    medida_preventiva,
                    gravedad,
                    otros_responsables_otros_beneficios,
                    estado,
                    created_at,
                    updated_at,
                    CASE 
                        WHEN sancion LIKE '%UIT%' THEN 
                            CASE 
                                WHEN sancion LIKE '1 UIT%' THEN 5150.00
                                WHEN sancion LIKE '0.5 UIT%' THEN 2575.00
                                WHEN sancion LIKE '0.25 UIT%' THEN 1287.50
                                WHEN sancion LIKE '0.1 UIT%' THEN 515.00
                                ELSE 0.00
                            END
                        ELSE 0.00
                    END as monto_base_uit
                FROM infracciones 
                ORDER BY 
                    CASE gravedad 
                        WHEN 'Muy grave' THEN 1
                        WHEN 'Grave' THEN 2
                        WHEN 'Leve' THEN 3
                    END,
                    codigo_infraccion 
                LIMIT 200
            ");
            $infracciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtener infracciones más frecuentes (simulado)
            $frecuentesQuery = "
                SELECT 
                    codigo_infraccion,
                    infraccion as descripcion,
                    gravedad,
                    sancion,
                    COUNT(*) as frecuencia
                FROM infracciones i
                LEFT JOIN actas a ON i.codigo_infraccion = a.codigo_infraccion
                WHERE i.estado = 'activo'
                GROUP BY i.id, i.codigo_infraccion, i.infraccion, i.gravedad, i.sancion
                ORDER BY frecuencia DESC, i.codigo_infraccion
                LIMIT 10
            ";
            
            $frecuentesStmt = $this->pdo->query($frecuentesQuery);
            $frecuentes = $frecuentesStmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'success' => true, 
                'infracciones' => $infracciones,
                'estadisticas' => $stats,
                'mas_frecuentes' => $frecuentes,
                'total' => count($infracciones)
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function createInfraccionesTable() {
        try {
            // Crear tabla completa de infracciones
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS `infracciones` (
                    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `codigo_infraccion` varchar(255) NOT NULL,
                    `aplica_sobre` varchar(255) NOT NULL,
                    `reglamento` varchar(255) NOT NULL,
                    `norma_modificatoria` varchar(255) NOT NULL,
                    `infraccion` text NOT NULL,
                    `clase_pago` enum('Pecuniaria','No pecuniaria') NOT NULL,
                    `sancion` varchar(255) NOT NULL,
                    `tipo` enum('Infracción') NOT NULL DEFAULT 'Infracción',
                    `medida_preventiva` text,
                    `gravedad` enum('Leve','Grave','Muy grave') NOT NULL,
                    `otros_responsables_otros_beneficios` text,
                    `estado` varchar(255) NOT NULL DEFAULT 'activo',
                    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `infracciones_codigo_infraccion_unique` (`codigo_infraccion`),
                    INDEX `idx_gravedad` (`gravedad`),
                    INDEX `idx_aplica_sobre` (`aplica_sobre`),
                    INDEX `idx_estado` (`estado`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            
            // Insertar catálogo completo de infracciones del RENAT
            $this->pdo->exec("
                INSERT IGNORE INTO `infracciones` (
                    `codigo_infraccion`, `aplica_sobre`, `reglamento`, `norma_modificatoria`, 
                    `infraccion`, `clase_pago`, `sancion`, `medida_preventiva`, `gravedad`, 
                    `otros_responsables_otros_beneficios`
                ) VALUES
                -- INFRACCIONES MUY GRAVES
                ('F.1', 'Transportista', 'RENAT', 'D.S. N° 017-2009-MTC', 'Prestar servicio de transporte público sin contar con autorización', 'Pecuniaria', '1 UIT', 'Internamiento del vehículo', 'Muy grave', 'Responsabilidad solidaria del propietario'),
                ('F.2', 'Transportista', 'RENAT', 'D.S. N° 017-2009-MTC', 'Bloqueo o interrupción del libre tránsito en las vías', 'Pecuniaria', '1 UIT', 'Internamiento del vehículo', 'Muy grave', NULL),
                ('F.3', 'Conductor', 'RENAT', 'D.S. N° 017-2009-MTC', 'Participar en bloqueo de vías o impedir el libre tránsito', 'Pecuniaria', '1 UIT', 'Suspensión de licencia por 3 años', 'Muy grave', NULL),
                ('F.4-a', 'Transportista', 'RENAT', 'D.S. N° 017-2009-MTC', 'Negarse a entregar información o documentación requerida', 'Pecuniaria', '1 UIT', NULL, 'Muy grave', NULL),
                ('F.4-b', 'Transportista', 'RENAT', 'D.S. N° 017-2009-MTC', 'Brindar intencionalmente información no conforme con la realidad', 'Pecuniaria', '1 UIT', NULL, 'Muy grave', NULL),
                ('F.5', 'Transportista', 'RENAT', 'D.S. N° 017-2009-MTC', 'Contratar servicios de transportista no autorizado', 'Pecuniaria', '1 UIT', NULL, 'Muy grave', 'Responsabilidad solidaria'),
                ('F.6-a', 'Conductor', 'RENAT', 'D.S. N° 017-2009-MTC', 'Negarse a entregar información o documentación al inspector', 'Pecuniaria', '1 UIT', 'Suspensión de licencia por 1 año', 'Muy grave', NULL),
                ('F.6-b', 'Conductor', 'RENAT', 'D.S. N° 017-2009-MTC', 'Brindar información falsa al inspector de transporte', 'Pecuniaria', '1 UIT', 'Suspensión de licencia por 1 año', 'Muy grave', NULL),
                ('F.7', 'Conductor', 'RENAT', 'D.S. N° 017-2009-MTC', 'Atentar contra la integridad física del inspector', 'Pecuniaria', '1 UIT', 'Cancelación definitiva de licencia', 'Muy grave', 'Denuncia penal'),
                
                -- INFRACCIONES GRAVES
                ('I.1-a', 'Transportista', 'RENAT', 'D.S. N° 017-2009-MTC', 'No portar manifiesto de usuarios o relación de pasajeros', 'Pecuniaria', '0.5 UIT', NULL, 'Grave', NULL),
                ('I.1-b', 'Transportista', 'RENAT', 'D.S. N° 017-2009-MTC', 'No portar hoja de ruta del servicio', 'Pecuniaria', '0.5 UIT', NULL, 'Grave', NULL),
                ('I.1-c', 'Transportista', 'RENAT', 'D.S. N° 017-2009-MTC', 'No portar guía de remisión del transportista', 'Pecuniaria', '0.5 UIT', NULL, 'Grave', NULL),
                ('I.1-d', 'Transportista', 'RENAT', 'D.S. N° 017-2009-MTC', 'No portar documento de habilitación vehicular vigente', 'Pecuniaria', '0.5 UIT', 'Retención del vehículo', 'Grave', NULL),
                ('I.1-e', 'Transportista', 'RENAT', 'D.S. N° 017-2009-MTC', 'No portar certificado de Inspección Técnica Vehicular vigente', 'Pecuniaria', '0.5 UIT', 'Retención del vehículo', 'Grave', NULL),
                ('I.1-f', 'Transportista', 'RENAT', 'D.S. N° 017-2009-MTC', 'No portar certificado SOAT vigente', 'Pecuniaria', '0.5 UIT', 'Retención del vehículo', 'Grave', NULL),
                ('I.2-a', 'Transportista', 'RENAT', 'D.S. N° 017-2009-MTC', 'No exhibir modalidad de servicio y razón social', 'Pecuniaria', '0.5 UIT', NULL, 'Grave', NULL),
                ('I.2-b', 'Transportista', 'RENAT', 'D.S. N° 017-2009-MTC', 'No colocar tarifas y ruta visible para usuarios', 'Pecuniaria', '0.5 UIT', NULL, 'Grave', NULL),
                ('I.3', 'Transportista', 'RENAT', 'D.S. N° 017-2009-MTC', 'Prestar servicio fuera del ámbito geográfico autorizado', 'Pecuniaria', '0.5 UIT', NULL, 'Grave', NULL),
                ('I.4', 'Conductor', 'RENAT', 'D.S. N° 017-2009-MTC', 'Conducir sin licencia de conducir vigente', 'Pecuniaria', '0.5 UIT', 'Retención del vehículo', 'Grave', NULL),
                
                -- INFRACCIONES LEVES
                ('L.1', 'Conductor', 'RENAT', 'D.S. N° 017-2009-MTC', 'Exceder los límites de velocidad establecidos', 'Pecuniaria', '0.25 UIT', NULL, 'Leve', 'Descuento del 50% por pago inmediato'),
                ('L.2', 'Conductor', 'RENAT', 'D.S. N° 017-2009-MTC', 'No respetar las señales de tránsito', 'Pecuniaria', '0.25 UIT', NULL, 'Leve', 'Descuento del 50% por pago inmediato'),
                ('L.3', 'Transportista', 'RENAT', 'D.S. N° 017-2009-MTC', 'Vehículo en mal estado de conservación o limpieza', 'Pecuniaria', '0.1 UIT', NULL, 'Leve', NULL),
                ('L.4', 'Conductor', 'RENAT', 'D.S. N° 017-2009-MTC', 'No usar cinturón de seguridad', 'Pecuniaria', '0.1 UIT', NULL, 'Leve', 'Descuento del 50% por pago inmediato'),
                ('L.5', 'Transportista', 'RENAT', 'D.S. N° 017-2009-MTC', 'Documentación desactualizada o incompleta', 'Pecuniaria', '0.1 UIT', NULL, 'Leve', NULL)
            ");
            
        } catch (Exception $e) {
            error_log('Error creating infracciones table: ' . $e->getMessage());
        }
    }
    
    private function getCargaPasajeros() {
        try {
            // Verificar si la tabla existe y tiene la estructura correcta
            if (!$this->tableExists('carga_pasajeros')) {
                // Crear tabla real de carga y pasajeros
                $this->pdo->exec("
                    CREATE TABLE IF NOT EXISTS `carga_pasajeros` (
                        `id` INT NOT NULL AUTO_INCREMENT,
                        `tipo` ENUM('carga', 'pasajero') NOT NULL,
                        `descripcion` VARCHAR(255) NULL,
                        `peso_cantidad` VARCHAR(100) NULL,
                        `origen` VARCHAR(150) NULL,
                        `destino` VARCHAR(150) NULL,
                        `fecha` DATE NULL,
                        `estado` ENUM('pendiente', 'en_transito', 'entregado', 'cancelado') DEFAULT 'pendiente',
                        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                ");
                
                // Insertar algunos datos de ejemplo reales
                $this->pdo->exec("
                    INSERT INTO `carga_pasajeros` (`tipo`, `descripcion`, `peso_cantidad`, `origen`, `destino`, `fecha`, `estado`) VALUES
                    ('carga', 'Transporte de mercancías varias', '2.5 toneladas', 'Lima', 'Abancay', CURDATE(), 'en_transito'),
                    ('pasajero', 'Servicio interprovincial', '45 pasajeros', 'Abancay', 'Cusco', CURDATE(), 'pendiente'),
                    ('carga', 'Productos agrícolas', '1.8 toneladas', 'Andahuaylas', 'Lima', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'entregado'),
                    ('pasajero', 'Ruta urbana', '30 pasajeros', 'Centro', 'Periférico', CURDATE(), 'en_transito')
                ");
            } else {
                // Verificar si la tabla tiene la estructura antigua y migrarla
                $hasNewStructure = $this->columnExists('carga_pasajeros', 'tipo') && 
                                   $this->columnExists('carga_pasajeros', 'descripcion') && 
                                   $this->columnExists('carga_pasajeros', 'peso_cantidad');
                
                if (!$hasNewStructure) {
                    // Migrar estructura antigua a nueva
                    $this->pdo->exec("
                        ALTER TABLE `carga_pasajeros` 
                        ADD COLUMN `tipo` ENUM('carga', 'pasajero') DEFAULT 'carga' AFTER `id`,
                        ADD COLUMN `descripcion` VARCHAR(255) NULL AFTER `tipo`,
                        ADD COLUMN `peso_cantidad` VARCHAR(100) NULL AFTER `descripcion`,
                        ADD COLUMN `origen` VARCHAR(150) NULL AFTER `peso_cantidad`,
                        ADD COLUMN `destino` VARCHAR(150) NULL AFTER `origen`,
                        ADD COLUMN `fecha` DATE NULL AFTER `destino`,
                        MODIFY COLUMN `estado` ENUM('pendiente', 'en_transito', 'entregado', 'cancelado') DEFAULT 'pendiente'
                    ");
                    
                    // Migrar datos existentes si los hay
                    $this->pdo->exec("
                        UPDATE `carga_pasajeros` SET 
                        `descripcion` = COALESCE(`informe`, 'Registro migrado'),
                        `peso_cantidad` = 'N/A',
                        `origen` = 'Por definir',
                        `destino` = 'Por definir',
                        `fecha` = CURDATE(),
                        `tipo` = 'carga'
                        WHERE `descripcion` IS NULL
                    ");
                }
            }
            
            $stmt = $this->pdo->query("
                SELECT 
                    id,
                    tipo,
                    descripcion,
                    peso_cantidad,
                    origen,
                    destino,
                    fecha,
                    estado,
                    created_at
                FROM carga_pasajeros 
                ORDER BY created_at DESC 
                LIMIT 100
            ");
            $cargaPasajeros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'carga_pasajeros' => $cargaPasajeros];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function getActasAdmin() {
        try {
            if ($this->userRole !== 'administrador' && $this->userRole !== 'admin') {
                return ['success' => false, 'message' => 'Acceso denegado'];
            }

            $stmt = $this->pdo->query("SELECT * FROM actas ORDER BY id DESC LIMIT 200");
            $actas = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            
            return [
                'success' => true, 
                'actas' => $actas,
                'stats' => ['total_actas' => count($actas)]
            ];
            
        } catch (Exception $e) {
            error_log('Error en getActasAdmin: ' . $e->getMessage());
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
    

    
    private function registrarAtencion() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) { $data = $_POST; }
            
            // Crear tabla de atenciones si no existe
            $this->createAtencionesTables();
            
            $stmt = $this->pdo->prepare("
                INSERT INTO atenciones (tipo_consulta, documento_cliente, nombre_cliente, telefono_cliente, descripcion, atendido_por, fecha_atencion, estado) 
                VALUES (?, ?, ?, ?, ?, ?, NOW(), 'pendiente')
            ");
            
            $result = $stmt->execute([
                $data['tipo_consulta'] ?? '',
                $data['documento'] ?? '',
                $data['nombre'] ?? '',
                $data['telefono'] ?? '',
                $data['descripcion'] ?? '',
                $this->userName
            ]);
            
            if ($result) {
                // Actualizar estadísticas
                $this->updateDailyStats('atenciones');
                return ['success' => true, 'message' => 'Atención registrada correctamente', 'id' => $this->pdo->lastInsertId()];
            } else {
                return ['success' => false, 'message' => 'Error al registrar atención'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    private function getColaEspera() {
        try {
            $this->createAtencionesTables();
            
            $stmt = $this->pdo->query("
                SELECT *, 
                       TIMESTAMPDIFF(MINUTE, hora_llegada, NOW()) as tiempo_espera_min
                FROM cola_espera 
                WHERE estado IN ('esperando', 'atendiendo') 
                ORDER BY hora_llegada ASC
            ");
            $cola = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Agregar información de tiempo de espera formateada
            foreach ($cola as &$cliente) {
                $minutos = $cliente['tiempo_espera_min'];
                if ($minutos >= 60) {
                    $horas = floor($minutos / 60);
                    $mins = $minutos % 60;
                    $cliente['tiempo_espera_texto'] = "{$horas}h {$mins}m";
                } else {
                    $cliente['tiempo_espera_texto'] = "{$minutos}m";
                }
            }
            
            return ['success' => true, 'cola' => $cola, 'total' => count($cola)];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function agregarColaEspera() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) { $data = $_POST; }
            
            $this->createAtencionesTables();
            
            // Generar número de ticket más inteligente
            $stmt = $this->pdo->query("
                SELECT COUNT(*) + 1 as siguiente 
                FROM cola_espera 
                WHERE DATE(hora_llegada) = CURDATE()
            ");
            $siguiente = $stmt->fetch()['siguiente'];
            $numeroTicket = 'V' . date('ymd') . str_pad($siguiente, 3, '0', STR_PAD_LEFT);
            
            $stmt = $this->pdo->prepare("
                INSERT INTO cola_espera (numero_ticket, nombre_cliente, documento_cliente, tipo_consulta, hora_llegada, estado, ventanilla) 
                VALUES (?, ?, ?, ?, NOW(), 'esperando', ?)
            ");
            
            $result = $stmt->execute([
                $numeroTicket,
                $data['nombre'] ?? '',
                $data['documento'] ?? '',
                $data['tipo_consulta'] ?? '',
                $this->userName
            ]);
            
            if ($result) {
                return [
                    'success' => true, 
                    'message' => 'Cliente agregado a la cola', 
                    'ticket' => $numeroTicket,
                    'posicion' => $siguiente,
                    'tiempo_estimado' => $siguiente * 15 . ' minutos'
                ];
            } else {
                return ['success' => false, 'message' => 'Error al agregar cliente a la cola'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    private function getTramites() {
        try {
            $this->createAtencionesTables();
            
            // Obtener trámites con información adicional
            $stmt = $this->pdo->query("
                SELECT *, 
                       DATEDIFF(NOW(), fecha_registro) as dias_transcurridos,
                       CASE 
                           WHEN estado = 'completado' THEN 'Completado'
                           WHEN estado = 'proceso' THEN 'En Proceso'
                           WHEN estado = 'rechazado' THEN 'Rechazado'
                           ELSE 'Pendiente'
                       END as estado_texto
                FROM tramites 
                ORDER BY 
                    CASE estado 
                        WHEN 'pendiente' THEN 1
                        WHEN 'proceso' THEN 2
                        WHEN 'completado' THEN 3
                        WHEN 'rechazado' THEN 4
                    END,
                    fecha_registro DESC 
                LIMIT 100
            ");
            $tramites = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtener estadísticas
            $statsStmt = $this->pdo->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'proceso' THEN 1 ELSE 0 END) as en_proceso,
                    SUM(CASE WHEN estado = 'completado' THEN 1 ELSE 0 END) as completados,
                    SUM(CASE WHEN DATE(fecha_registro) = CURDATE() THEN 1 ELSE 0 END) as hoy
                FROM tramites
            ");
            $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'success' => true, 
                'tramites' => $tramites, 
                'total' => count($tramites),
                'stats' => $stats
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function registrarTramite() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) { $data = $_POST; }
            
            $this->createAtencionesTables();
            
            // Generar número de trámite más inteligente
            $stmt = $this->pdo->query("
                SELECT COUNT(*) + 1 as siguiente 
                FROM tramites 
                WHERE YEAR(fecha_registro) = YEAR(CURDATE())
            ");
            $siguiente = $stmt->fetch()['siguiente'];
            $numeroTramite = 'DRTC-' . date('Y') . '-' . str_pad($siguiente, 5, '0', STR_PAD_LEFT);
            
            // Calcular fecha estimada de finalización
            $diasEstimados = $this->getDiasEstimadosTramite($data['tipo_tramite'] ?? '');
            $fechaEstimada = date('Y-m-d', strtotime("+{$diasEstimados} days"));
            
            $stmt = $this->pdo->prepare("
                INSERT INTO tramites (
                    numero_tramite, tipo_tramite, documento_solicitante, nombre_solicitante, 
                    telefono_solicitante, observaciones, registrado_por, fecha_registro, 
                    fecha_estimada_finalizacion, estado
                ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, 'pendiente')
            ");
            
            $result = $stmt->execute([
                $numeroTramite,
                $data['tipo_tramite'] ?? '',
                $data['documento'] ?? '',
                $data['nombre'] ?? '',
                $data['telefono'] ?? '',
                $data['observaciones'] ?? '',
                $this->userName,
                $fechaEstimada
            ]);
            
            if ($result) {
                // Actualizar estadísticas
                $this->updateDailyStats('tramites');
                
                return [
                    'success' => true, 
                    'message' => 'Trámite registrado correctamente', 
                    'numero' => $numeroTramite,
                    'fecha_estimada' => $fechaEstimada,
                    'dias_estimados' => $diasEstimados
                ];
            } else {
                return ['success' => false, 'message' => 'Error al registrar trámite'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
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
                case 'infracciones':
                    $stmt = $this->pdo->prepare("SELECT * FROM infracciones ORDER BY codigo_infraccion");
                    $stmt->execute();
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
                case 'carga-pasajeros':
                    $stmt = $this->pdo->prepare("SELECT * FROM carga_pasajeros ORDER BY created_at DESC");
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
                LEFT JOIN usuarios u ON a.fiscalizador_id = u.id 
                WHERE a.fiscalizador_id = ? 
                ORDER BY a.created_at DESC
            ");
            
            $stmt->execute([$fiscalizadorId]);
            $actas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // El estado ya es ENUM('Pendiente', 'Aprobado', 'Anulado'), no necesita conversión
            
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
    
    private function getCodigosInfracciones() {
        // Códigos basados en los seeders - sin necesidad de tablas adicionales
        $codigos = [
            // F.1 - Muy Grave
            ['codigo' => 'F.1', 'descripcion' => 'Prestar servicio de transporte sin autorización', 'gravedad' => 'Muy grave'],
            
            // F.2 - Muy Grave
            ['codigo' => 'F.2', 'descripcion' => 'Bloqueo o interrupción del libre tránsito', 'gravedad' => 'Muy grave'],
            
            // F.3 - Muy Grave
            ['codigo' => 'F.3', 'descripcion' => 'Conductor participa en bloqueo de vías', 'gravedad' => 'Muy grave'],
            
            // F.4 - Muy Grave (con subcategorías)
            ['codigo' => 'F.4-a', 'descripcion' => 'Negarse a entregar información o documentación', 'gravedad' => 'Muy grave'],
            ['codigo' => 'F.4-b', 'descripcion' => 'Brindar intencionalmente información no conforme', 'gravedad' => 'Muy grave'],
            ['codigo' => 'F.4-c', 'descripcion' => 'Actos de simulación o suplantación', 'gravedad' => 'Muy grave'],
            
            // F.5 - Muy Grave (con subcategorías)
            ['codigo' => 'F.5-a', 'descripcion' => 'Contratar transportista no autorizado', 'gravedad' => 'Muy grave'],
            ['codigo' => 'F.5-b', 'descripcion' => 'Usar vía pública para carga/descarga habitual', 'gravedad' => 'Muy grave'],
            ['codigo' => 'F.5-c', 'descripcion' => 'No exigir autorización especial para carga excedida', 'gravedad' => 'Muy grave'],
            
            // F.6 - Muy Grave (con subcategorías)
            ['codigo' => 'F.6-a', 'descripcion' => 'Conductor niega entregar información', 'gravedad' => 'Muy grave'],
            ['codigo' => 'F.6-b', 'descripcion' => 'Conductor brinda información falsa', 'gravedad' => 'Muy grave'],
            ['codigo' => 'F.6-c', 'descripcion' => 'Realizar maniobras evasivas con vehículo', 'gravedad' => 'Muy grave'],
            ['codigo' => 'F.6-d', 'descripcion' => 'Conductor en actos de simulación o suplantación', 'gravedad' => 'Muy grave'],
            
            // F.7 - Muy Grave
            ['codigo' => 'F.7', 'descripcion' => 'Atentar contra integridad del inspector', 'gravedad' => 'Muy grave'],
            
            // F.8 - Muy Grave
            ['codigo' => 'F.8', 'descripcion' => 'Circular en emergencia incumpliendo restricciones', 'gravedad' => 'Muy grave'],
            
            // I.1 - Grave (con subcategorías)
            ['codigo' => 'I.1-a', 'descripcion' => 'No portar manifiesto de usuarios', 'gravedad' => 'Grave'],
            ['codigo' => 'I.1-b', 'descripcion' => 'No portar hoja de ruta', 'gravedad' => 'Grave'],
            ['codigo' => 'I.1-c', 'descripcion' => 'No portar guía de remisión del transportista', 'gravedad' => 'Grave'],
            ['codigo' => 'I.1-d', 'descripcion' => 'No portar documento de habilitación del vehículo', 'gravedad' => 'Grave'],
            ['codigo' => 'I.1-e', 'descripcion' => 'No portar certificado de Inspección Técnica Vehicular', 'gravedad' => 'Grave'],
            ['codigo' => 'I.1-f', 'descripcion' => 'No portar certificado SOAT', 'gravedad' => 'Grave'],
            
            // I.2 - Grave (con subcategorías)
            ['codigo' => 'I.2-a', 'descripcion' => 'No exhibir modalidad de servicio y razón social', 'gravedad' => 'Grave'],
            ['codigo' => 'I.2-b', 'descripcion' => 'No colocar tarifas y ruta visible para usuarios', 'gravedad' => 'Grave'],
        ];
        
        return [
            'success' => true,
            'codigos' => $codigos,
            'total' => count($codigos)
        ];
    }
    
    private function anularActa() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['acta_id']) || !isset($data['motivo_anulacion'])) {
                return ['success' => false, 'message' => 'Datos incompletos'];
            }
            
            $actaId = $data['acta_id'];
            $motivo = trim($data['motivo_anulacion']);
            
            if (strlen($motivo) < 10) {
                return ['success' => false, 'message' => 'El motivo debe tener al menos 10 caracteres'];
            }
            
            // Verificar que el acta existe
            $stmt = $this->pdo->prepare("SELECT estado FROM actas WHERE id = ?");
            $stmt->execute([$actaId]);
            $acta = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$acta) {
                return ['success' => false, 'message' => 'Acta no encontrada'];
            }
            
            // Verificar si ya está anulada (estado = 2)
            if ($acta['estado'] == 2) {
                return ['success' => false, 'message' => 'El acta ya está anulada'];
            }
            
            // Crear columna motivo_anulacion si no existe
            if (!$this->columnExists('actas', 'motivo_anulacion')) {
                $this->pdo->exec("ALTER TABLE actas ADD COLUMN motivo_anulacion TEXT NULL");
            }
            
            // Actualizar acta a estado Anulado (2) con motivo
            $updateFields = ['estado = ?', 'motivo_anulacion = ?'];
            $params = [2, $motivo]; // 2 = anulada
            
            // Agregar campos adicionales si existen en la tabla
            if ($this->columnExists('actas', 'fecha_anulacion')) {
                $updateFields[] = 'fecha_anulacion = NOW()';
            }
            if ($this->columnExists('actas', 'anulado_por')) {
                $updateFields[] = 'anulado_por = ?';
                $params[] = $_SESSION['user_id'];
            }
            if ($this->columnExists('actas', 'updated_at')) {
                $updateFields[] = 'updated_at = NOW()';
            }
            
            $params[] = $actaId;
            
            $sql = "UPDATE actas SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return [
                'success' => true,
                'message' => 'Acta anulada correctamente'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al anular acta: ' . $e->getMessage()
            ];
        }
    }
    
    private function getNextActaNumber() {
        try {
            $anioActual = date('Y');
            
            // Buscar el último número de acta del año actual
            $stmt = $this->pdo->prepare("
                SELECT MAX(CAST(numero_acta AS UNSIGNED)) as ultimo_numero 
                FROM actas 
                WHERE anio_acta = ?
            ");
            $stmt->execute([$anioActual]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $ultimoNumero = $resultado['ultimo_numero'] ?? 0;
            return $ultimoNumero + 1;
            
        } catch (Exception $e) {
            return 1;
        }
    }
    
    private function generarNumeroActa() {
        try {
            $anioActual = date('Y');
            $numeroActa = $this->getNextActaNumber();
            
            // Formatear con 4 dígitos: 0001, 0002, etc.
            $numeroFormateado = str_pad($numeroActa, 4, '0', STR_PAD_LEFT);
            
            return [
                'numero_acta' => $numeroFormateado,
                'anio_acta' => $anioActual
            ];
            
        } catch (Exception $e) {
            // Fallback en caso de error
            return [
                'numero_acta' => str_pad(1, 4, '0', STR_PAD_LEFT),
                'anio_acta' => date('Y')
            ];
        }
    }
    
    // ==================== MÉTODOS PARA VENTANILLA ====================
    
    private function consultarDocumento($documento) {
        try {
            if (!$documento) {
                return ['success' => false, 'message' => 'Documento requerido'];
            }
            
            // Buscar en conductores
            $stmt = $this->pdo->prepare("SELECT * FROM conductores WHERE dni = ? OR numero_licencia = ?");
            $stmt->execute([$documento, $documento]);
            $conductor = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Buscar actas relacionadas
            $stmt = $this->pdo->prepare("SELECT * FROM actas WHERE ruc_dni = ? ORDER BY fecha_intervencion DESC LIMIT 10");
            $stmt->execute([$documento]);
            $actas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'conductor' => $conductor,
                'actas' => $actas,
                'total_actas' => count($actas)
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    private function consultarActasPorCriterio($criterios) {
        try {
            $tipo = $criterios['tipo_busqueda'] ?? '';
            $valor = $criterios['valor_busqueda'] ?? '';
            
            if (!$tipo || !$valor) {
                return ['success' => false, 'message' => 'Criterios de búsqueda incompletos'];
            }
            
            $sql = "SELECT *, 
                CONCAT(COALESCE(apellidos_conductor, ''), ' ', COALESCE(nombres_conductor, '')) AS nombre_conductor
                FROM actas WHERE ";
            $params = [];
            
            switch ($tipo) {
                case 'numero_acta':
                    $sql .= "numero_acta LIKE ?";
                    $params[] = "%{$valor}%";
                    break;
                case 'placa':
                    $sql .= "(placa LIKE ? OR placa_vehiculo LIKE ?)";
                    $params[] = "%{$valor}%";
                    $params[] = "%{$valor}%";
                    break;
                case 'documento':
                    $sql .= "ruc_dni LIKE ?";
                    $params[] = "%{$valor}%";
                    break;
                default:
                    return ['success' => false, 'message' => 'Tipo de búsqueda no válido'];
            }
            
            $sql .= " ORDER BY fecha_intervencion DESC LIMIT 50";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $actas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'actas' => $actas,
                'total' => count($actas),
                'criterio' => $tipo,
                'valor' => $valor
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    private function consultarVehiculoPorPlaca($placa) {
        try {
            if (!$placa) {
                return ['success' => false, 'message' => 'Placa requerida'];
            }
            
            // Buscar vehículo
            $stmt = $this->pdo->prepare("SELECT * FROM vehiculos WHERE placa LIKE ?");
            $stmt->execute(["%{$placa}%"]);
            $vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Buscar actas del vehículo
            $stmt = $this->pdo->prepare("SELECT * FROM actas WHERE placa LIKE ? OR placa_vehiculo LIKE ? ORDER BY fecha_intervencion DESC LIMIT 10");
            $stmt->execute(["%{$placa}%", "%{$placa}%"]);
            $actas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'vehiculo' => $vehiculo,
                'actas' => $actas,
                'total_actas' => count($actas)
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    private function consultarConductorPorDocumento($criterios) {
        try {
            $tipo = $criterios['tipo_busqueda'] ?? '';
            $valor = $criterios['valor_busqueda'] ?? '';
            
            if (!$tipo || !$valor) {
                return ['success' => false, 'message' => 'Criterios de búsqueda incompletos'];
            }
            
            $sql = "SELECT * FROM conductores WHERE ";
            $params = [];
            
            switch ($tipo) {
                case 'dni':
                    $sql .= "dni LIKE ?";
                    $params[] = "%{$valor}%";
                    break;
                case 'licencia':
                    $sql .= "numero_licencia LIKE ?";
                    $params[] = "%{$valor}%";
                    break;
                default:
                    return ['success' => false, 'message' => 'Tipo de búsqueda no válido'];
            }
            
            $sql .= " LIMIT 10";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $conductores = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Si encontramos conductores, buscar sus actas
            $actasRelacionadas = [];
            if (!empty($conductores)) {
                foreach ($conductores as $conductor) {
                    $stmt = $this->pdo->prepare("SELECT * FROM actas WHERE ruc_dni = ? OR licencia = ? ORDER BY fecha_intervencion DESC LIMIT 5");
                    $stmt->execute([$conductor['dni'], $conductor['numero_licencia']]);
                    $actas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $actasRelacionadas[$conductor['id']] = $actas;
                }
            }
            
            return [
                'success' => true,
                'conductores' => $conductores,
                'actas_relacionadas' => $actasRelacionadas,
                'total' => count($conductores)
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    // ==================== FUNCIONES AUXILIARES PARA VENTANILLA ====================
    
    private function createAtencionesTables() {
        // Crear tabla de atenciones
        if (!$this->tableExists('atenciones')) {
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS `atenciones` (
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `tipo_consulta` VARCHAR(100) NULL,
                    `documento_cliente` VARCHAR(20) NULL,
                    `nombre_cliente` VARCHAR(150) NULL,
                    `telefono_cliente` VARCHAR(20) NULL,
                    `descripcion` TEXT NULL,
                    `atendido_por` VARCHAR(100) NULL,
                    `fecha_atencion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                    `estado` ENUM('pendiente', 'atendido', 'cerrado') DEFAULT 'pendiente',
                    `tiempo_atencion` INT NULL COMMENT 'Tiempo en minutos',
                    PRIMARY KEY (`id`),
                    INDEX `idx_fecha` (`fecha_atencion`),
                    INDEX `idx_estado` (`estado`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }
        
        // Crear tabla de cola de espera
        if (!$this->tableExists('cola_espera')) {
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS `cola_espera` (
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `numero_ticket` VARCHAR(20) NULL,
                    `nombre_cliente` VARCHAR(150) NULL,
                    `documento_cliente` VARCHAR(20) NULL,
                    `tipo_consulta` VARCHAR(100) NULL,
                    `hora_llegada` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                    `hora_atencion` TIMESTAMP NULL,
                    `estado` ENUM('esperando', 'atendiendo', 'atendido', 'cancelado') DEFAULT 'esperando',
                    `ventanilla` VARCHAR(50) NULL,
                    `prioridad` ENUM('normal', 'alta', 'urgente') DEFAULT 'normal',
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `uk_ticket` (`numero_ticket`),
                    INDEX `idx_estado` (`estado`),
                    INDEX `idx_fecha` (`hora_llegada`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }
        
        // Crear tabla de trámites
        if (!$this->tableExists('tramites')) {
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS `tramites` (
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `numero_tramite` VARCHAR(30) NULL,
                    `tipo_tramite` VARCHAR(100) NULL,
                    `documento_solicitante` VARCHAR(20) NULL,
                    `nombre_solicitante` VARCHAR(150) NULL,
                    `telefono_solicitante` VARCHAR(20) NULL,
                    `observaciones` TEXT NULL,
                    `estado` ENUM('pendiente', 'proceso', 'completado', 'rechazado', 'suspendido') DEFAULT 'pendiente',
                    `fecha_registro` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                    `fecha_estimada_finalizacion` DATE NULL,
                    `fecha_finalizacion` TIMESTAMP NULL,
                    `registrado_por` VARCHAR(100) NULL,
                    `procesado_por` VARCHAR(100) NULL,
                    `costo` DECIMAL(10,2) NULL DEFAULT 0.00,
                    `documentos_requeridos` JSON NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `uk_numero` (`numero_tramite`),
                    INDEX `idx_estado` (`estado`),
                    INDEX `idx_fecha` (`fecha_registro`),
                    INDEX `idx_tipo` (`tipo_tramite`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }
        
        // Crear tabla de estadísticas diarias
        if (!$this->tableExists('estadisticas_diarias')) {
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS `estadisticas_diarias` (
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `fecha` DATE NOT NULL,
                    `tipo` VARCHAR(50) NOT NULL,
                    `cantidad` INT NOT NULL DEFAULT 0,
                    `usuario` VARCHAR(100) NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `uk_fecha_tipo_usuario` (`fecha`, `tipo`, `usuario`),
                    INDEX `idx_fecha` (`fecha`),
                    INDEX `idx_tipo` (`tipo`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }
    }
    
    private function getDiasEstimadosTramite($tipoTramite) {
        $diasEstimados = [
            'licencia_conducir' => 15,
            'renovacion_licencia' => 7,
            'duplicado_licencia' => 5,
            'tarjeta_propiedad' => 10,
            'cambio_propietario' => 12,
            'placa_vehicular' => 8,
            'revision_tecnica' => 3,
            'certificado_no_adeudo' => 2
        ];
        
        return $diasEstimados[$tipoTramite] ?? 10;
    }
    
    private function updateDailyStats($tipo) {
        try {
            $fecha = date('Y-m-d');
            $usuario = $this->userName;
            
            $stmt = $this->pdo->prepare("
                INSERT INTO estadisticas_diarias (fecha, tipo, cantidad, usuario) 
                VALUES (?, ?, 1, ?) 
                ON DUPLICATE KEY UPDATE cantidad = cantidad + 1
            ");
            
            $stmt->execute([$fecha, $tipo, $usuario]);
        } catch (Exception $e) {
            // Log error but don't fail the main operation
            error_log('Error updating daily stats: ' . $e->getMessage());
        }
    }
    
    private function getDashboardStatsVentanilla() {
        try {
            $fecha = date('Y-m-d');
            
            // Atenciones de hoy
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count 
                FROM atenciones 
                WHERE DATE(fecha_atencion) = ?
            ");
            $stmt->execute([$fecha]);
            $atencionesHoy = $stmt->fetch()['count'];
            
            // Cola de espera actual
            $stmt = $this->pdo->query("
                SELECT COUNT(*) as count 
                FROM cola_espera 
                WHERE estado IN ('esperando', 'atendiendo')
            ");
            $colaEspera = $stmt->fetch()['count'];
            
            // Trámites completados hoy
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count 
                FROM tramites 
                WHERE DATE(fecha_registro) = ? AND estado = 'completado'
            ");
            $stmt->execute([$fecha]);
            $tramitesCompletados = $stmt->fetch()['count'];
            
            // Tiempo promedio de atención
            $stmt = $this->pdo->prepare("
                SELECT AVG(tiempo_atencion) as promedio 
                FROM atenciones 
                WHERE DATE(fecha_atencion) = ? AND tiempo_atencion IS NOT NULL
            ");
            $stmt->execute([$fecha]);
            $tiempoPromedio = round($stmt->fetch()['promedio'] ?? 15);
            
            return [
                'success' => true,
                'stats' => [
                    'atenciones_hoy' => $atencionesHoy,
                    'cola_espera' => $colaEspera,
                    'tramites_completados' => $tramitesCompletados,
                    'tiempo_promedio' => $tiempoPromedio
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => true,
                'stats' => [
                    'atenciones_hoy' => 0,
                    'cola_espera' => 0,
                    'tramites_completados' => 0,
                    'tiempo_promedio' => 15
                ]
            ];
        }
    }
    
    // ==================== FUNCIONES ADICIONALES PARA VENTANILLA ====================
    
    private function atenderCliente($data) {
        try {
            $clienteId = $data['id'] ?? null;
            if (!$clienteId) {
                return ['success' => false, 'message' => 'ID de cliente requerido'];
            }
            
            $this->createAtencionesTables();
            
            // Actualizar estado del cliente en cola
            $stmt = $this->pdo->prepare("
                UPDATE cola_espera 
                SET estado = 'atendiendo', 
                    hora_atencion = NOW(),
                    ventanilla = ?
                WHERE id = ?
            ");
            
            $result = $stmt->execute([$this->userName, $clienteId]);
            
            if ($result) {
                // Registrar inicio de atención
                $stmt = $this->pdo->prepare("
                    INSERT INTO atenciones (tipo_consulta, documento_cliente, nombre_cliente, atendido_por, fecha_atencion, estado)
                    SELECT tipo_consulta, documento_cliente, nombre_cliente, ?, NOW(), 'atendido'
                    FROM cola_espera WHERE id = ?
                ");
                $stmt->execute([$this->userName, $clienteId]);
                
                return ['success' => true, 'message' => 'Cliente siendo atendido'];
            } else {
                return ['success' => false, 'message' => 'Error al actualizar estado del cliente'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    private function cancelarCliente($data) {
        try {
            $clienteId = $data['id'] ?? null;
            if (!$clienteId) {
                return ['success' => false, 'message' => 'ID de cliente requerido'];
            }
            
            $this->createAtencionesTables();
            
            // Actualizar estado a cancelado
            $stmt = $this->pdo->prepare("
                UPDATE cola_espera 
                SET estado = 'cancelado'
                WHERE id = ?
            ");
            
            $result = $stmt->execute([$clienteId]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Cliente removido de la cola'];
            } else {
                return ['success' => false, 'message' => 'Error al cancelar cliente'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    private function procesarTramite($data) {
        try {
            $tramiteId = $data['id'] ?? null;
            if (!$tramiteId) {
                return ['success' => false, 'message' => 'ID de trámite requerido'];
            }
            
            $this->createAtencionesTables();
            
            // Actualizar estado del trámite
            $stmt = $this->pdo->prepare("
                UPDATE tramites 
                SET estado = 'proceso',
                    procesado_por = ?
                WHERE id = ?
            ");
            
            $result = $stmt->execute([$this->userName, $tramiteId]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Trámite marcado como en proceso'];
            } else {
                return ['success' => false, 'message' => 'Error al procesar trámite'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    private function getDetalleTramite($id) {
        try {
            if (!$id) {
                return ['success' => false, 'message' => 'ID de trámite requerido'];
            }
            
            $this->createAtencionesTables();
            
            $stmt = $this->pdo->prepare("
                SELECT *,
                       DATEDIFF(NOW(), fecha_registro) as dias_transcurridos,
                       CASE 
                           WHEN fecha_estimada_finalizacion IS NOT NULL THEN
                               DATEDIFF(fecha_estimada_finalizacion, NOW())
                           ELSE NULL
                       END as dias_restantes
                FROM tramites 
                WHERE id = ?
            ");
            
            $stmt->execute([$id]);
            $tramite = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($tramite) {
                return ['success' => true, 'tramite' => $tramite];
            } else {
                return ['success' => false, 'message' => 'Trámite no encontrado'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    // ==================== FUNCIONES PARA CARGA Y PASAJEROS ====================
    
    private function createCargaPasajero() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) { $data = $_POST; }
            
            // Validar campos requeridos
            if (empty($data['tipo']) || empty($data['descripcion']) || empty($data['origen']) || empty($data['destino'])) {
                return ['success' => false, 'message' => 'Campos requeridos: tipo, descripción, origen, destino'];
            }
            
            // Asegurar que la tabla existe con la estructura correcta
            $this->getCargaPasajeros(); // Esto creará la tabla si no existe
            
            $stmt = $this->pdo->prepare("
                INSERT INTO carga_pasajeros (tipo, descripcion, peso_cantidad, origen, destino, fecha, estado, created_at) 
                VALUES (?, ?, ?, ?, ?, CURDATE(), ?, NOW())
            ");
            
            $result = $stmt->execute([
                $data['tipo'],
                $data['descripcion'],
                $data['peso_cantidad'] ?? null,
                $data['origen'],
                $data['destino'],
                $data['estado'] ?? 'pendiente'
            ]);
            
            if ($result) {
                return [
                    'success' => true, 
                    'message' => 'Registro creado correctamente',
                    'id' => $this->pdo->lastInsertId()
                ];
            } else {
                return ['success' => false, 'message' => 'Error al crear el registro'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    private function updateCargaPasajero() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) { $data = $_POST; }
            
            if (empty($data['id'])) {
                return ['success' => false, 'message' => 'ID requerido'];
            }
            
            $updateFields = [];
            $params = [];
            
            // Campos que se pueden actualizar
            $allowedFields = ['tipo', 'descripcion', 'peso_cantidad', 'origen', 'destino', 'estado'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (empty($updateFields)) {
                return ['success' => false, 'message' => 'No hay campos para actualizar'];
            }
            
            $updateFields[] = 'updated_at = NOW()';
            $params[] = $data['id'];
            
            $sql = "UPDATE carga_pasajeros SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($params);
            
            if ($result && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Registro actualizado correctamente'];
            } else {
                return ['success' => false, 'message' => 'No se pudo actualizar el registro o no se encontró'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    private function deleteCargaPasajero($id) {
        try {
            if (!$id) {
                return ['success' => false, 'message' => 'ID requerido'];
            }
            
            $stmt = $this->pdo->prepare("DELETE FROM carga_pasajeros WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Registro eliminado correctamente'];
            } else {
                return ['success' => false, 'message' => 'No se pudo eliminar el registro o no se encontró'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
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
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/dashboard.css" rel="stylesheet">
    <link href="css/infracciones-dashboard.css" rel="stylesheet">
    <?php if ($rol === 'ventanilla'): ?>
    <link href="css/ventanilla-simple.css" rel="stylesheet">
    <?php endif; ?>
    <style>
        :root {
            --primary-color: #ff8c00;
            --secondary-color: #e67e22;
            --success-color: #27ae60;
            --warning-color: #ff8c00;
            --danger-color: #e74c3c;
            --dark-color: #e67e22;
            --light-color: #ecf0f1;
        }

        .logo-img {
            height: auto;
            border-radius: 8px;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 10px;
            width: 100%;
            display: flex;
            justify-content: center;
        }


        .sidebar-header .logo-img {
            max-width: 70px;
        }

        .navbar-brand .logo-img {
            max-width: 70px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            padding-top: 70px;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
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
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
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
            color: var(--primary-color);
            border-left-color: var(--primary-color);
        }

        .sidebar-link.active {
            background-color: #fff3e0;
            color: var(--primary-color);
            border-left-color: var(--primary-color);
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
            border-left: 3px solid var(--primary-color);
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
            color: var(--primary-color);
            padding-left: 50px;
        }

        .sidebar-sublink.active {
            background-color: var(--primary-color);
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
            border-left: 4px solid var(--primary-color);
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
            background-color: var(--primary-color);
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
            <a class="navbar-brand" href="#"><img src="images/logo.png" alt="Logo Sistema" class="logo-img me-2 d-none d-md-inline">Sistema de Gestión</a>
            
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
            <div class="logo-container mb-3">
                <img src="images/logo.png" alt="Logo Sistema" class="logo-img mx-auto d-block">
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
                <ul class="sidebar-submenu" id="submenu-usuarios" style="display: none;">
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
                <ul class="sidebar-submenu" id="submenu-actas" style="display: none;">
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="javascript:void(0)" onclick="loadActasList()">
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
                <a class="sidebar-link sidebar-toggle" href="#" onclick="toggleSubmenuAlt('carga-pasajeros', event); return false;">
                    <i class="fas fa-users-cog"></i> Gestión de Carga y Pasajeros
                    <i class="fas fa-chevron-down sidebar-arrow"></i>
                </a>
                <ul class="sidebar-submenu" id="submenu-carga-pasajeros" style="display: none;">
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="javascript:void(0)" onclick="loadCargaPasajerosList()">
                            <i class="fas fa-list"></i> Lista de Registros
                        </a>
                    </li>
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="javascript:void(0)" onclick="loadCrearCargaPasajero()">
                            <i class="fas fa-plus-circle"></i> Nuevo Registro
                        </a>
                    </li>
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="javascript:void(0)" onclick="loadEstadisticasCarga()">
                            <i class="fas fa-chart-pie"></i> Estadísticas
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
            <!-- Menú Híbrido para Ventanilla -->
            <li class="sidebar-item">
                <a class="sidebar-link" href="javascript:void(0)" onclick="loadNuevaAtencion()">
                    <i class="fas fa-plus-circle"></i> Nueva Atención
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="javascript:void(0)" onclick="loadColaEspera()">
                    <i class="fas fa-clock"></i> Cola de Espera
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link sidebar-toggle" href="#" onclick="toggleSubmenu('tramites', event)">
                    <i class="fas fa-folder-open"></i> Trámites
                </a>
                <ul class="sidebar-submenu" id="submenu-tramites">
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="javascript:void(0)" onclick="loadTramites()">
                            <i class="fas fa-plus"></i> Nuevo Trámite
                        </a>
                    </li>
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="javascript:void(0)" onclick="tramitesPendientes()">
                            <i class="fas fa-list"></i> Pendientes
                        </a>
                    </li>
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="javascript:void(0)" onclick="historialTramites()">
                            <i class="fas fa-history"></i> Historial
                        </a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link sidebar-toggle" href="#" onclick="toggleSubmenu('consultas', event)">
                    <i class="fas fa-search"></i> Consultas
                </a>
                <ul class="sidebar-submenu" id="submenu-consultas">
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="javascript:void(0)" onclick="loadConsultas()">
                            <i class="fas fa-globe"></i> Portal Público
                        </a>
                    </li>
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="javascript:void(0)" onclick="loadConsultasActas()">
                            <i class="fas fa-file-alt"></i> Actas
                        </a>
                    </li>
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="javascript:void(0)" onclick="consultarVehiculos()">
                            <i class="fas fa-car"></i> Vehículos
                        </a>
                    </li>
                    <li class="sidebar-subitem">
                        <a class="sidebar-sublink" href="javascript:void(0)" onclick="consultarConductores()">
                            <i class="fas fa-id-card"></i> Conductores
                        </a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="javascript:void(0)" onclick="mostrarInfoDRTC()">
                    <i class="fas fa-info-circle"></i> Información DRTC
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
                        <div class="card bg-warning text-white">
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
                        <div class="card bg-warning text-white">
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
                        <div class="card bg-warning text-white">
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
    <script src="js/infracciones-dashboard.js"></script>
    
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
    <script src="js/ventanilla-simple.js"></script>
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
