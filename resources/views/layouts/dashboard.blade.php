<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de Control')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 250px;
            --topbar-height: 60px;
            --drtc-orange: #ff8c00;
            --drtc-dark-orange: #e67c00;
            --drtc-light-orange: #ffb84d;
            --drtc-orange-bg: #fff4e6;
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
            background: linear-gradient(180deg, var(--drtc-orange) 10%, var(--drtc-dark-orange) 100%);
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
        }

        .nav-link:hover {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-link.active {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-link i {
            width: 1.5rem;
            margin-right: 0.5rem;
        }

        /* Main content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        /* Top bar */
        .topbar {
            height: var(--topbar-height);
            background-color: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .topbar .navbar-nav .nav-link {
            color: #5a5c69 !important;
        }

        .topbar .navbar-nav .nav-item .nav-link {
            padding: 0.75rem 1rem;
        }

        /* Content area */
        .content-wrapper {
            padding: 1.5rem;
        }

        /* Cards */
        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border: none;
            border-radius: 0.35rem;
        }

        /* Role-specific colors */
        .sidebar.administrador {
            background: linear-gradient(180deg, var(--drtc-navy) 10%, #1e40af 100%);
        }

        .sidebar.fiscalizador {
            background: linear-gradient(180deg, var(--drtc-orange) 10%, var(--drtc-dark-orange) 100%);
        }

        .sidebar.ventanilla {
            background: linear-gradient(180deg, var(--drtc-light-orange) 10%, var(--drtc-orange) 100%);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(var(--sidebar-width) * -1);
            }

            .sidebar.show {
                margin-left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-menu-toggle {
                display: block !important;
            }
        }
        
        /* Prevenir scroll horizontal global */
        * {
            box-sizing: border-box !important;
        }
        
        .container, .container-fluid {
            overflow-x: hidden !important;
            max-width: 100% !important;
        }
        
        .row {
            overflow-x: hidden !important;
            max-width: 100% !important;
        }
        
        [class*="col-"] {
            overflow-x: hidden !important;
            max-width: 100% !important;
        }

        .mobile-menu-toggle {
            display: none;
        }

        /* Notifications */
        .notification-badge {
            position: absolute;
            top: 0.25rem;
            right: 0.25rem;
            background: #e74c3c;
            color: white;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 1rem;
        }

        /* Topbar mejoras */
        .topbar .navbar-nav .nav-link:hover {
            background-color: rgba(0, 0, 0, 0.05);
            border-radius: 0.35rem;
        }

        .topbar .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175);
            position: absolute !important;
            right: 0 !important;
            left: auto !important;
            z-index: 1055 !important;
            transform: none !important;
            will-change: auto !important;
        }
        
        /* Prevenir scroll horizontal en topbar */
        .topbar {
            overflow: visible !important;
        }
        
        .topbar .container {
            overflow: visible !important;
            max-width: 100% !important;
        }
        
        .topbar .navbar-nav {
            overflow: visible !important;
        }

        .animated--grow-in {
            animation: growIn 0.2s ease-in-out;
        }

        @keyframes growIn {
            0% {
                transform: scale(0.9);
                opacity: 0;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .badge-counter {
            font-size: 0.7rem;
            border: 2px solid #fff;
        }

        .dropdown-item:hover {
            background-color: #f8f9fc;
        }

        .bg-gradient-primary {
            background: linear-gradient(87deg, var(--drtc-orange) 0, var(--drtc-dark-orange) 100%);
        }
        
        .btn-primary {
            background-color: var(--drtc-orange);
            border-color: var(--drtc-orange);
        }
        
        .btn-primary:hover {
            background-color: var(--drtc-dark-orange);
            border-color: var(--drtc-dark-orange);
        }
        
        .text-primary {
            color: var(--drtc-orange) !important;
        }

        /* User info en topbar */
        .topbar .nav-link {
            transition: all 0.2s ease-in-out;
        }

        .topbar .nav-link:focus {
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(94, 114, 228, 0.25);
        }

        /* Estilos adicionales para notificaciones */
        .notification-item:hover {
            background-color: #f1f3f4 !important;
        }

        .notification-body {
            scrollbar-width: thin;
            scrollbar-color: #dee2e6 #f8f9fa;
        }

        .notification-body::-webkit-scrollbar {
            width: 6px;
        }

        .notification-body::-webkit-scrollbar-track {
            background: #f8f9fa;
        }

        .notification-body::-webkit-scrollbar-thumb {
            background-color: #dee2e6;
            border-radius: 3px;
        }

        /* Animación suave para el dropdown */
        .dropdown-menu {
            transition: opacity 0.2s ease-in-out, transform 0.2s ease-in-out;
            transform-origin: top right;
        }

        .dropdown-menu[style*="display: none"] {
            opacity: 0;
            transform: scale(0.95) translateY(-10px);
            pointer-events: none;
        }

        .dropdown-menu[style*="display: block"] {
            opacity: 1;
            transform: scale(1) translateY(0);
            pointer-events: auto;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar sidebar-{{ auth()->user()->role ?? 'default' }}" id="sidebar">
        @auth
            @if(auth()->user()->role == 'administrador')
                <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
            @elseif(auth()->user()->role == 'fiscalizador')
                <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('fiscalizador.dashboard') }}">
            @elseif(auth()->user()->role == 'ventanilla')
                <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('ventanilla.dashboard') }}">
            @else
                <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
            @endif
        @else
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        @endauth
            <div class="sidebar-brand-icon rotate-n-15">
                <i class="fas fa-road"></i>
            </div>
            <div class="sidebar-brand-text mx-3">DRTC Apurímac</div>
        </a>

        <hr class="sidebar-divider my-0" style="border-color: rgba(255, 255, 255, 0.1);">

        <ul class="sidebar-nav">
            <!-- Dashboard -->
            <li class="nav-item">
                @auth
                    @if(auth()->user()->role == 'administrador')
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-fw fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    @elseif(auth()->user()->role == 'fiscalizador')
                        <a class="nav-link {{ request()->routeIs('fiscalizador.dashboard') ? 'active' : '' }}" href="{{ route('fiscalizador.dashboard') }}">
                            <i class="fas fa-fw fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    @elseif(auth()->user()->role == 'ventanilla')
                        <a class="nav-link {{ request()->routeIs('ventanilla.dashboard') ? 'active' : '' }}" href="{{ route('ventanilla.dashboard') }}">
                            <i class="fas fa-fw fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    @endif
                @endauth
            </li>

            @auth
                @if(auth()->user()->role == 'administrador')
                    <!-- Admin Menu -->
                    <hr class="sidebar-divider" style="border-color: rgba(255, 255, 255, 0.1);">
                    <div class="sidebar-heading" style="color: rgba(255, 255, 255, 0.5); font-size: 0.7rem; padding: 0 1.5rem;">
                        MANTENIMIENTOS
                    </div>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.mantenimiento.fiscal') ? 'active' : '' }}" href="{{ route('admin.mantenimiento.fiscal') }}">
                            <i class="fas fa-fw fa-user-shield"></i>
                            <span>Mantenimiento Fiscal</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.mantenimiento.conductor') ? 'active' : '' }}" href="{{ route('admin.mantenimiento.conductor') }}">
                            <i class="fas fa-fw fa-id-card"></i>
                            <span>Mantenimiento Conductor</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.mantenimiento.usuario') ? 'active' : '' }}" href="{{ route('admin.mantenimiento.usuario') }}">
                            <i class="fas fa-fw fa-users"></i>
                            <span>Mantenimiento Usuario</span>
                        </a>
                    </li>

                @elseif(auth()->user()->role == 'fiscalizador')
                    <!-- Fiscalizador Menu -->
                    <hr class="sidebar-divider" style="border-color: rgba(255, 255, 255, 0.1);">
                    <div class="sidebar-heading" style="color: rgba(255, 255, 255, 0.5); font-size: 0.7rem; padding: 0 1.5rem;">
                        GESTIÓN DE ACTAS
                    </div>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('fiscalizador.actas-contra') ? 'active' : '' }}" href="{{ route('fiscalizador.actas-contra') }}">
                            <i class="fas fa-fw fa-file-contract"></i>
                            <span>Actas Contra</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('fiscalizador.carga-paga') ? 'active' : '' }}" href="{{ route('fiscalizador.carga-paga') }}">
                            <i class="fas fa-fw fa-truck-loading"></i>
                            <span>Carga y Paga</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('fiscalizador.empresas') ? 'active' : '' }}" href="{{ route('fiscalizador.empresas') }}">
                            <i class="fas fa-fw fa-building"></i>
                            <span>Empresas</span>
                        </a>
                    </li>

                    <hr class="sidebar-divider" style="border-color: rgba(255, 255, 255, 0.1);">
                    <div class="sidebar-heading" style="color: rgba(255, 255, 255, 0.5); font-size: 0.7rem; padding: 0 1.5rem;">
                        CONSULTAS Y REPORTES
                    </div>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('fiscalizador.consultas') ? 'active' : '' }}" href="{{ route('fiscalizador.consultas') }}">
                            <i class="fas fa-fw fa-search"></i>
                            <span>Consultas</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('fiscalizador.calendario') ? 'active' : '' }}" href="{{ route('fiscalizador.calendario') }}">
                            <i class="fas fa-fw fa-calendar-alt"></i>
                            <span>Calendario</span>
                        </a>
                    </li>

                @elseif(auth()->user()->role == 'ventanilla')
                    <!-- Ventanilla Menu -->
                    <hr class="sidebar-divider" style="border-color: rgba(255, 255, 255, 0.1);">
                    <div class="sidebar-heading" style="color: rgba(255, 255, 255, 0.5); font-size: 0.7rem; padding: 0 1.5rem;">
                        ATENCIÓN
                    </div>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('ventanilla.nueva-atencion') ? 'active' : '' }}" href="{{ route('ventanilla.nueva-atencion') }}">
                            <i class="fas fa-fw fa-user-plus"></i>
                            <span>Nueva Atención</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('ventanilla.tramites') ? 'active' : '' }}" href="{{ route('ventanilla.tramites') }}">
                            <i class="fas fa-fw fa-file-alt"></i>
                            <span>Trámites</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('ventanilla.consultar') ? 'active' : '' }}" href="{{ route('ventanilla.consultar') }}">
                            <i class="fas fa-fw fa-search"></i>
                            <span>Consultar Estado</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('ventanilla.cola-espera') ? 'active' : '' }}" href="{{ route('ventanilla.cola-espera') }}">
                            <i class="fas fa-fw fa-users"></i>
                            <span>Cola de Espera</span>
                        </a>
                    </li>
                @endif
            @endauth
        </ul>

        <hr class="sidebar-divider d-none d-md-block" style="border-color: rgba(255, 255, 255, 0.1);">
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <nav class="navbar navbar-expand topbar mb-4 static-top" style="background: #fff; border-bottom: 1px solid #e3e6f0; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);">
            <button class="btn btn-link d-md-none rounded-circle mr-3 mobile-menu-toggle" onclick="toggleSidebar()">
                <i class="fa fa-bars"></i>
            </button>

            <!-- Título de la página actual -->
            <div class="navbar-nav flex-grow-1">
                <span class="navbar-text text-muted fs-5 fw-light">
                    @yield('title', 'Panel de Control')
                </span>
            </div>

            <!-- Usuario en la esquina superior derecha -->
            <ul class="navbar-nav">
                @auth
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: #5a5c69; padding: 0.75rem;">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small font-weight-bold">{{ auth()->user()->name }}</span>
                            <i class="fas fa-user-circle fa-lg" style="color: #858796;"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                Mi Perfil
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                Configuración
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </li>
                @endauth
            </ul>
        </nav>

        <!-- Page Content -->
        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>

    <!-- Sistema de Notificaciones Flotantes -->
    <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 350px;"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        // Sistema de Notificaciones Flotantes
        function showToast(title, message, type = 'success', duration = 5000) {
            const toastContainer = document.getElementById('toast-container');
            const toastId = 'toast-' + Date.now();
            
            const icons = {
                'success': 'fas fa-check-circle',
                'error': 'fas fa-exclamation-triangle', 
                'warning': 'fas fa-exclamation-circle',
                'info': 'fas fa-info-circle'
            };
            
            const colors = {
                'success': 'var(--drtc-orange)',
                'error': '#dc3545',
                'warning': '#ffc107',
                'info': '#17a2b8'
            };
            
            const toast = document.createElement('div');
            toast.id = toastId;
            toast.className = 'toast-notification';
            toast.innerHTML = `
                <div style="
                    background: white;
                    border-left: 4px solid ${colors[type]};
                    border-radius: 8px;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
                    margin-bottom: 10px;
                    padding: 16px;
                    display: flex;
                    align-items: flex-start;
                    animation: slideInRight 0.3s ease-out;
                    position: relative;
                    max-width: 100%;
                ">
                    <div style="color: ${colors[type]}; margin-right: 12px; margin-top: 2px;">
                        <i class="${icons[type]}" style="font-size: 18px;"></i>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: 600; color: #333; margin-bottom: 4px; font-size: 14px;">
                            ${title}
                        </div>
                        <div style="color: #666; font-size: 13px; line-height: 1.4;">
                            ${message}
                        </div>
                    </div>
                    <button onclick="closeToast('${toastId}')" style="
                        background: none;
                        border: none;
                        color: #999;
                        font-size: 16px;
                        cursor: pointer;
                        padding: 0;
                        margin-left: 8px;
                        width: 20px;
                        height: 20px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    ">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            toastContainer.appendChild(toast);
            
            // Auto-cerrar después del tiempo especificado
            setTimeout(() => {
                closeToast(toastId);
            }, duration);
        }
        
        function closeToast(toastId) {
            const toast = document.getElementById(toastId);
            if (toast) {
                toast.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }
        }
        
        // Función global para mostrar notificaciones
        window.showNotification = function(title, message, type = 'success') {
            showToast(title, message, type);
        };
        
        // Función global para mostrar notificaciones de éxito
        window.showSuccess = function(message) {
            showToast('¡Operación Exitosa!', message, 'success');
        };
        
        // Función global para mostrar notificaciones de error
        window.showError = function(message) {
            showToast('Error', message, 'error');
        };
        
        // Función global para mostrar notificaciones de advertencia
        window.showWarning = function(message) {
            showToast('Advertencia', message, 'warning');
        };
        
        // Función global para mostrar notificaciones de información
        window.showInfo = function(message) {
            showToast('Información', message, 'info');
        };
    </script>
    
    <style>
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        .toast-notification:hover {
            transform: translateX(-5px);
            transition: transform 0.2s ease;
        }
    </style>

    @yield('scripts')
</body>
</html>
