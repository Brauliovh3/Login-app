<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: /Login-app/public/');
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
    die("Error de conexión: " . $e->getMessage());
}

// Obtener información del usuario
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header('Location: /Login-app/public/');
    exit();
}

$userRole = strtolower($user['role']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DRTC Apurímac - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-width: 250px;
            --topbar-height: 60px;
            --drtc-primary: #2c3e50;
            --drtc-secondary: #34495e;
            --drtc-accent: #3498db;
            --drtc-success: #27ae60;
            --drtc-warning: #f39c12;
            --drtc-danger: #e74c3c;
            --drtc-light: #ecf0f1;
            --drtc-dark: #2c3e50;
            --drtc-orange: #ff8c00;
            --drtc-dark-orange: #e67e22;
            --drtc-light-orange: #fff4e6;
            --drtc-navy: #1e3a8a;
        }

        html {
            overflow-x: hidden !important;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fc;
            overflow-x: hidden !important;
            position: relative;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #ff8c00 10%, #e67e22 100%);
            z-index: 1000;
            transition: all 0.3s;
        }

        .sidebar .sidebar-brand {
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar .sidebar-brand:hover {
            color: white;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-item {
            position: relative;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: rgba(255, 255, 255, 0.8) !important;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
        }

        .nav-link:hover {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            transform: translateX(5px);
            transition: all 0.3s ease;
        }

        .nav-link.active {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            border-left: 3px solid #ff8c00;
        }

        .nav-icon {
            width: 20px;
            margin-right: 12px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        .topbar {
            height: var(--topbar-height);
            background-color: white;
            border-bottom: 1px solid #e3e6f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .content-wrapper {
            padding: 2rem;
        }

        /* Cards */
        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border-radius: 0.75rem;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #e3e6f0;
            border-radius: 0.75rem 0.75rem 0 0 !important;
        }

        /* Stats Cards */
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }

        .stats-card.orange {
            background: linear-gradient(135deg, #ff8c00 0%, #e67e22 100%);
        }

        .stats-card.blue {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        }

        .stats-card.green {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
        }

        .stats-card.red {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stats-label {
            font-size: 1rem;
            opacity: 0.9;
        }

        /* Usuario info en sidebar */
        .user-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.1);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .user-details {
            color: white;
            margin-left: 10px;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .user-role {
            font-size: 0.75rem;
            opacity: 0.8;
            text-transform: uppercase;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar.mobile-open {
                width: var(--sidebar-width);
            }
        }

        /* Loading spinner */
        .loading {
            display: none;
            text-align: center;
            padding: 2rem;
        }

        .spinner-border {
            color: var(--drtc-orange);
        }

        /* Content sections */
        .content-section {
            display: none;
        }

        .content-section.active {
            display: block;
        }

        /* Welcome section */
        .welcome-header {
            background: linear-gradient(135deg, var(--drtc-orange), var(--drtc-dark-orange));
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }

        .welcome-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .welcome-subtitle {
            opacity: 0.9;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <a class="sidebar-brand" href="#">
            <i class="fas fa-road nav-icon"></i>
            DRTC Apurímac
        </a>
        
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a class="nav-link active" href="#" data-section="dashboard">
                    <i class="fas fa-tachometer-alt nav-icon"></i>
                    Dashboard
                </a>
            </li>
            
            <?php if ($userRole === 'administrador'): ?>
            <li class="nav-item">
                <a class="nav-link" href="#" data-section="usuarios">
                    <i class="fas fa-users nav-icon"></i>
                    Gestión de Usuarios
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-section="reportes">
                    <i class="fas fa-chart-bar nav-icon"></i>
                    Reportes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-section="configuracion">
                    <i class="fas fa-cog nav-icon"></i>
                    Configuración
                </a>
            </li>
            <?php elseif ($userRole === 'fiscalizador'): ?>
            <li class="nav-item">
                <a class="nav-link" href="#" data-section="fiscalizacion">
                    <i class="fas fa-clipboard-check nav-icon"></i>
                    Fiscalización
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-section="actas">
                    <i class="fas fa-file-alt nav-icon"></i>
                    Actas
                </a>
            </li>
            <?php elseif ($userRole === 'ventanilla'): ?>
            <li class="nav-item">
                <a class="nav-link" href="#" data-section="tramites">
                    <i class="fas fa-clipboard-list nav-icon"></i>
                    Trámites
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-section="consultas">
                    <i class="fas fa-search nav-icon"></i>
                    Consultas
                </a>
            </li>
            <?php elseif ($userRole === 'inspector'): ?>
            <li class="nav-item">
                <a class="nav-link" href="#" data-section="inspecciones">
                    <i class="fas fa-eye nav-icon"></i>
                    Inspecciones
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-section="vehiculos">
                    <i class="fas fa-car nav-icon"></i>
                    Vehículos
                </a>
            </li>
            <?php endif; ?>
            
            <li class="nav-item">
                <a class="nav-link" href="#" data-section="perfil">
                    <i class="fas fa-user nav-icon"></i>
                    Mi Perfil
                </a>
            </li>
        </ul>

        <!-- User Info -->
        <div class="user-info">
            <div class="d-flex align-items-center">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($user['nombre'], 0, 1)); ?>
                </div>
                <div class="user-details">
                    <div class="user-name"><?php echo htmlspecialchars($user['nombre']); ?></div>
                    <div class="user-role"><?php echo htmlspecialchars($user['role']); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <h4 class="mb-0">Panel de Control - <?php echo ucfirst($user['role']); ?></h4>
            <div>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </div>
        </div>

        <!-- Content -->
        <div class="content-wrapper">
            <!-- Loading Spinner -->
            <div class="loading" id="loading">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando contenido...</p>
            </div>

            <!-- Dashboard Section -->
            <div class="content-section active" id="dashboard-section">
                <div class="welcome-header">
                    <div class="welcome-title">Bienvenido, <?php echo htmlspecialchars($user['nombre']); ?>!</div>
                    <div class="welcome-subtitle">Panel de control del sistema DRTC Apurímac</div>
                </div>

                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="stats-card orange">
                            <div class="stats-number" id="total-usuarios">0</div>
                            <div class="stats-label">Total Usuarios</div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="stats-card blue">
                            <div class="stats-number" id="total-tramites">0</div>
                            <div class="stats-label">Trámites Activos</div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="stats-card green">
                            <div class="stats-number" id="total-actas">0</div>
                            <div class="stats-label">Actas Generadas</div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="stats-card red">
                            <div class="stats-number" id="total-pendientes">0</div>
                            <div class="stats-label">Pendientes</div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Actividad Reciente</h5>
                            </div>
                            <div class="card-body">
                                <div id="recent-activity">
                                    <!-- Activity will be loaded via AJAX -->
                                    <p class="text-muted">Cargando actividad reciente...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other Sections -->
            <div class="content-section" id="usuarios-section">
                <h2>Gestión de Usuarios</h2>
                <p>Aquí irá el contenido de gestión de usuarios...</p>
            </div>

            <div class="content-section" id="reportes-section">
                <h2>Reportes</h2>
                <p>Aquí irán los reportes del sistema...</p>
            </div>

            <div class="content-section" id="configuracion-section">
                <h2>Configuración</h2>
                <p>Aquí irá la configuración del sistema...</p>
            </div>

            <div class="content-section" id="fiscalizacion-section">
                <h2>Fiscalización</h2>
                <p>Aquí irá el módulo de fiscalización...</p>
            </div>

            <div class="content-section" id="actas-section">
                <h2>Actas</h2>
                <p>Aquí irá el módulo de actas...</p>
            </div>

            <div class="content-section" id="tramites-section">
                <h2>Trámites</h2>
                <p>Aquí irá el módulo de trámites...</p>
            </div>

            <div class="content-section" id="consultas-section">
                <h2>Consultas</h2>
                <p>Aquí irá el módulo de consultas...</p>
            </div>

            <div class="content-section" id="inspecciones-section">
                <h2>Inspecciones</h2>
                <p>Aquí irá el módulo de inspecciones...</p>
            </div>

            <div class="content-section" id="vehiculos-section">
                <h2>Vehículos</h2>
                <p>Aquí irá el módulo de vehículos...</p>
            </div>

            <div class="content-section" id="perfil-section">
                <h2>Mi Perfil</h2>
                <div class="card">
                    <div class="card-body">
                        <h5>Información Personal</h5>
                        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($user['nombre']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong>Role:</strong> <?php echo ucfirst(htmlspecialchars($user['role'])); ?></p>
                        <p><strong>DNI:</strong> <?php echo htmlspecialchars($user['dni']); ?></p>
                        <p><strong>Fecha de registro:</strong> <?php echo date('d/m/Y', strtotime($user['created_at'])); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navigation functionality
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-link[data-section]');
            const contentSections = document.querySelectorAll('.content-section');
            const loading = document.getElementById('loading');

            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const targetSection = this.dataset.section;
                    
                    // Update active nav link
                    navLinks.forEach(nl => nl.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Show loading
                    contentSections.forEach(section => section.classList.remove('active'));
                    loading.style.display = 'block';
                    
                    // Simulate loading time
                    setTimeout(() => {
                        loading.style.display = 'none';
                        document.getElementById(targetSection + '-section').classList.add('active');
                    }, 500);
                });
            });

            // Load dashboard stats
            loadDashboardStats();
            
            // Auto-refresh stats every 30 seconds
            setInterval(loadDashboardStats, 30000);
        });

        function loadDashboardStats() {
            fetch('dashboard_api.php?action=stats')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('total-usuarios').textContent = data.stats.usuarios || 0;
                        document.getElementById('total-tramites').textContent = data.stats.tramites || 0;
                        document.getElementById('total-actas').textContent = data.stats.actas || 0;
                        document.getElementById('total-pendientes').textContent = data.stats.pendientes || 0;
                    }
                })
                .catch(error => {
                    console.error('Error loading stats:', error);
                });
        }

        // Mobile menu toggle
        function toggleMobileMenu() {
            document.querySelector('.sidebar').classList.toggle('mobile-open');
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            const sidebar = document.querySelector('.sidebar');
            const isMobile = window.innerWidth <= 768;
            
            if (isMobile && !sidebar.contains(e.target) && sidebar.classList.contains('mobile-open')) {
                sidebar.classList.remove('mobile-open');
            }
        });
    </script>
</body>
</html>