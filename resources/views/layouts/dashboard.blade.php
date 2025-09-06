<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel de Control')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @if(session('user_config.theme') === 'dark')
        <link href="{{ asset('css/dark-mode.css') }}" rel="stylesheet">
    @endif
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
        /* Botones personalizados para un look más formal */
        .btn-primary {
            background: linear-gradient(135deg, #ff8c00, #e67e22) !important;
            border: none !important;
            border-radius: 8px;
            transition: all 0.3s ease;
            color: white !important;
            font-weight: 500;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #e67e22, #d35400) !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 140, 0, 0.4);
            color: white !important;
        }

        .btn-primary:focus,
        .btn-primary:active {
            background: linear-gradient(135deg, #ff8c00, #e67e22) !important;
            color: white !important;
            box-shadow: 0 0 0 0.2rem rgba(255, 140, 0, 0.25);
        }

        .btn-success {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            border: none;
            border-radius: 8px;
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--accent-gray), #95a5a6);
            border: none;
            border-radius: 8px;
        }

        .btn-danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            border: none;
            border-radius: 8px;
        }

        /* Cards más elegantes */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, #ff8c00, #e67e22);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            border: none;
        }

        /* Tabla más elegante */
        .table {
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead th {
            background: linear-gradient(135deg, var(--light-gray), #ecf0f1);
            color: var(--dark-text);
            border: none;
            font-weight: 600;
        }

        .table tbody tr:hover {
            background-color: rgba(44, 62, 80, 0.05);
        }

        /* Modales más elegantes */
        .modal-content {
            border: none;
            border-radius: 12px;
        }

        .modal-header {
            background: linear-gradient(135deg, #ff8c00, #e67e22);
            color: white;
            border-radius: 12px 12px 0 0;
            border: none;
        }

        /* Iconos especiales naranjas */
        .icon-orange {
            color: #ff8c00 !important;
        }

        .text-orange {
            color: #ff8c00 !important;
        }

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
<body class="{{ session('user_config.theme', 'light') === 'dark' ? 'dark-mode' : '' }}">
    <!-- Sidebar -->
    <nav class="sidebar sidebar-{{ auth()->user()->role ?? 'default' }}" id="sidebar">
        @auth
            @if(auth()->user()->role == 'administrador')
                <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
            @elseif(auth()->user()->role == 'fiscalizador')
                <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('fiscalizador.dashboard') }}">
            @elseif(auth()->user()->role == 'ventanilla')
                <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('ventanilla.dashboard') }}">
            @elseif(auth()->user()->role == 'inspector')
                <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('inspector.dashboard') }}">
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
                    @elseif(auth()->user()->role == 'inspector')
                        <a class="nav-link {{ request()->routeIs('inspector.dashboard') ? 'active' : '' }}" href="{{ route('inspector.dashboard') }}">
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
                        <a class="nav-link {{ request()->routeIs('users.index') ? 'active': '' }}" href="{{ route('users.index')}}" >
                            <i class="fas fa-fw fa-users-cog"></i>
                            <span>Gestionar Usuarios</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.approval') ? 'active': '' }}" href="{{ route('admin.users.approval')}}" >
                            <i class="fas fa-fw fa-user-check"></i>
                            <span>Aprobar Usuarios</span>
                            @php
                                $pendingCount = \App\Models\User::where('status', 'pending')->count();
                            @endphp
                            @if($pendingCount > 0)
                                <span class="badge bg-warning text-dark ms-2">{{ $pendingCount }}</span>
                            @endif
                        </a>
                    </li>

                @elseif(auth()->user()->role == 'fiscalizador')
                    <!-- Fiscalizador Menu -->
                    <hr class="sidebar-divider" style="border-color: rgba(255, 255, 255, 0.1);">
                    <div class="sidebar-heading" style="color: rgba(255, 255, 255, 0.5); font-size: 0.7rem; padding: 0 1.5rem;">
                        GESTIÓN DE ACTAS
                    </div>

                    <!-- Menu Principal con Dropdown -->
                    <li class="nav-item dropdown-hover">
                        <a class="nav-link dropdown-main {{ request()->routeIs('fiscalizador.actas-contra') ? 'active' : '' }}" href="{{ route('fiscalizador.actas-contra') }}">
                            <i class="fas fa-fw fa-file-contract"></i>
                            <span>Actas Contra</span>
                            <i class="fas fa-angle-down dropdown-icon"></i>
                        </a>
                        <!-- Submenu desplegable -->
                        <ul class="dropdown-submenu">
                            <li>
                                <a href="#" class="submenu-item" onclick="event.preventDefault(); navegarYAbrirModal('{{ route('fiscalizador.actas-contra') }}', 'modal-nueva-acta')">
                                    <i class="fas fa-plus-circle me-2"></i>
                                    <span>Nueva Acta</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="submenu-item" onclick="event.preventDefault(); navegarYAbrirModal('{{ route('fiscalizador.actas-contra') }}', 'modal-editar-acta')">
                                    <i class="fas fa-edit me-2"></i>
                                    <span>Editar Acta</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="submenu-item" onclick="event.preventDefault(); navegarYAbrirModal('{{ route('fiscalizador.actas-contra') }}', 'modal-eliminar-acta')">
                                    <i class="fas fa-trash-alt me-2"></i>
                                    <span>Eliminar Acta</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="submenu-item" onclick="event.preventDefault(); navegarYAbrirModal('{{ route('fiscalizador.actas-contra') }}', 'modal-consultas')">
                                    <i class="fas fa-search me-2"></i>
                                    <span>Consultas y Reportes</span>
                                </a>
                            </li>
                        </ul>
                    </li>


                    <hr class="sidebar-divider" style="border-color: rgba(255, 255, 255, 0.1);">
                    <div class="sidebar-heading" style="color: rgba(255, 255, 255, 0.5); font-size: 0.7rem; padding: 0 1.5rem;">
                        OTROS MÓDULOS
                    </div>

                    <!-- Menú profesional y desplegable para Carga y Pasajero -->
                    <li class="nav-item dropdown-hover">
                        <a class="nav-link {{ request()->routeIs('carga-pasajero.*') ? 'active' : '' }}" href="{{ route('carga-pasajero.index') }}">
                            <i class="fas fa-fw fa-truck-loading"></i>
                            <span>Carga y Pasajero</span>
                        </a>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('fiscalizador.empresas') ? 'active' : '' }}" href="{{ route('fiscalizador.empresas') }}">
                            <i class="fas fa-fw fa-building"></i>
                            <span>Empresas</span>
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
                @elseif(auth()->user()->role == 'inspector')
                    <!-- Inspector Menu -->
                    <hr class="sidebar-divider" style="border-color: rgba(255, 255, 255, 0.1);">
                    <div class="sidebar-heading" style="color: rgba(255, 255, 255, 0.5); font-size: 0.7rem; padding: 0 1.5rem;">
                        INSPECCIONES
                    </div>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('inspector.nueva-inspeccion') ? 'active' : '' }}" href="{{ route('inspector.nueva-inspeccion') }}">
                            <i class="fas fa-fw fa-plus-circle"></i>
                            <span>Nueva Inspección</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('inspector.inspecciones') ? 'active' : '' }}" href="{{ route('inspector.inspecciones') }}">
                            <i class="fas fa-fw fa-list-check"></i>
                            <span>Mis Inspecciones</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('inspector.vehiculos') ? 'active' : '' }}" href="{{ route('inspector.vehiculos') }}">
                            <i class="fas fa-fw fa-car"></i>
                            <span>Vehículos</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('inspector.reportes') ? 'active' : '' }}" href="{{ route('inspector.reportes') }}">
                            <i class="fas fa-fw fa-chart-bar"></i>
                            <span>Reportes</span>
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
                            <a class="dropdown-item" href="{{ route('user.perfil') }}">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                Mi Perfil
                            </a>
                            <a class="dropdown-item" href="{{ route('user.configuracion') }}">
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
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
                'success': '#ff8c00',
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
    
    <script>
        // Configurar jQuery para usar el token CSRF en todas las peticiones AJAX
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
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

        /* Estilos para dropdown con hover */
        .dropdown-hover {
            position: relative;
        }

        .dropdown-main {
            position: relative;
            display: flex !important;
            align-items: center;
            justify-content: space-between;
            text-decoration: none !important;
        }

        .dropdown-main:hover {
            text-decoration: none !important;
        }

        .dropdown-icon {
            margin-left: auto;
            font-size: 0.8rem;
            transition: transform 0.3s ease;
            opacity: 0.6;
        }

        .dropdown-hover:hover .dropdown-icon {
            transform: rotate(180deg);
            opacity: 1;
        }

        .dropdown-submenu {
            position: absolute;
            top: 0;
            left: 100%;
            background: rgba(0, 0, 0, 0.95);
            border-radius: 8px;
            min-width: 240px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
            opacity: 0;
            visibility: hidden;
            transform: translateX(-10px);
            transition: all 0.3s ease;
            z-index: 1000;
            margin: 0;
            padding: 8px 0;
            list-style: none;
            border: 2px solid rgba(255, 140, 0, 0.4);
            backdrop-filter: blur(10px);
        }

        .dropdown-hover:hover .dropdown-submenu {
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
        }

        .submenu-item {
            display: block;
            padding: 14px 18px;
            color: rgba(255, 255, 255, 0.9) !important;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-weight: 500;
        }

        .submenu-item:last-child {
            border-bottom: none;
        }

        .submenu-item:hover {
            background: linear-gradient(135deg, #ff8c00, #e67e22);
            color: white !important;
            transform: translateX(5px);
            text-decoration: none;
            box-shadow: inset 0 0 20px rgba(255, 255, 255, 0.1);
        }

        .submenu-item i {
            width: 22px;
            text-align: center;
            color: #ff8c00;
            margin-right: 8px;
        }

        .submenu-item:hover i {
            color: white;
        }

        /* Prevenir que el hover del dropdown interfiera con el clic */
        .dropdown-main span {
            pointer-events: none;
        }

        .dropdown-main i:not(.dropdown-icon) {
            pointer-events: none;
        }

        /* Animación para el submenu */
        @keyframes slideInSubmenu {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .dropdown-hover:hover .dropdown-submenu {
            animation: slideInSubmenu 0.3s ease-out;
        }

        /* Responsive para dropdown */
        @media (max-width: 768px) {
            .dropdown-submenu {
                position: static;
                opacity: 1;
                visibility: visible;
                transform: none;
                box-shadow: none;
                background: rgba(0, 0, 0, 0.2);
                border-radius: 0;
                margin-top: 8px;
                border: none;
                border-left: 3px solid var(--drtc-orange);
            }
            
            .dropdown-icon {
                display: none;
            }
        }

        /* Estilos para modales flotantes de pantalla completa */
        .floating-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.8);
            z-index: 9999;
            display: none;
            overflow-y: auto;
            padding: 20px;
            box-sizing: border-box;
        }

        .floating-modal.show {
            display: block;
            animation: fadeInModal 0.3s ease-out;
        }

        .floating-modal .modal-content-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideInModal 0.3s ease-out;
            min-height: calc(100vh - 40px);
            position: relative;
        }

        .floating-modal .modal-header-custom {
            background: linear-gradient(135deg, #ff8c00, #e67e22);
            color: white;
            padding: 20px 30px;
            border-radius: 15px 15px 0 0;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .floating-modal .modal-body-custom {
            padding: 30px;
            max-height: calc(100vh - 140px);
            overflow-y: auto;
        }

        .floating-modal .close-modal {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 24px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .floating-modal .close-modal:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        @keyframes fadeInModal {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideInModal {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Responsive para modales */
        @media (max-width: 768px) {
            .floating-modal {
                padding: 10px;
            }
            
            .floating-modal .modal-content-wrapper {
                min-height: calc(100vh - 20px);
            }
            
            .floating-modal .modal-header-custom {
                padding: 15px 20px;
            }
            
            .floating-modal .modal-body-custom {
                padding: 20px;
                max-height: calc(100vh - 120px);
            }
        }
    </style>

    @stack('scripts')
    @yield('scripts')

    <!-- Scripts para modales flotantes -->
    <script>
        // Funciones para gestionar modales flotantes
        function abrirModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
                
                // Auto-llenar fecha y hora en modales de nueva acta
                if (modalId === 'modal-nueva-acta') {
                    const ahora = new Date();
                    const fecha = ahora.toISOString().split('T')[0];
                    const hora = ahora.toTimeString().split(' ')[0].substring(0, 5);
                    
                    const fechaInput = modal.querySelector('input[name="fecha_intervencion"]');
                    const horaInput = modal.querySelector('input[name="hora_intervencion"]');
                    
                    if (fechaInput) fechaInput.value = fecha;
                    if (horaInput) horaInput.value = hora;
                }
            }
        }

        function cerrarModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        }

        // Cerrar modal al hacer clic fuera del contenido
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('floating-modal')) {
                const modalId = e.target.id;
                cerrarModal(modalId);
            }
        });

        // Cerrar modal con tecla ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modalesAbiertos = document.querySelectorAll('.floating-modal.show');
                modalesAbiertos.forEach(modal => {
                    cerrarModal(modal.id);
                });
            }
        });

        // Funciones específicas para las funcionalidades de fiscalización
        function buscarActaEditar() {
            const criterio = document.getElementById('buscar-editar').value.trim();
            if (!criterio) {
                showWarning('Por favor ingrese un criterio de búsqueda');
                return;
            }
            
            // Simular búsqueda (aquí iría la lógica AJAX real)
            setTimeout(() => {
                document.getElementById('resultado-editar').style.display = 'block';
                document.getElementById('acta-numero-editar').textContent = 'DRTC-APU-2024-' + Math.floor(Math.random() * 1000).toString().padStart(3, '0');
                showSuccess('Acta encontrada y cargada para edición');
            }, 1000);
        }

        function buscarActaEliminar() {
            const criterio = document.getElementById('buscar-eliminar').value.trim();
            if (!criterio) {
                showWarning('Por favor ingrese un criterio de búsqueda');
                return;
            }
            
            // Simular búsqueda (aquí iría la lógica AJAX real)
            setTimeout(() => {
                document.getElementById('resultado-eliminar').style.display = 'block';
                showInfo('Acta encontrada. Revise los datos antes de proceder.');
            }, 1000);
        }

        function ejecutarConsulta() {
            // Simular consulta: si hay una tabla de resultados, calcular totales a partir de las filas visibles
            document.getElementById('resumen-consulta').style.display = 'block';
            try {
                const table = document.querySelector('#tabla-resultados') || document.querySelector('table');
                let total = 0, procesadas = 0, pendientes = 0, anuladas = 0;
                if (table) {
                    const rows = Array.from(table.querySelectorAll('tbody tr'))
                        .filter(r => r.querySelectorAll('td').length > 0 && r.querySelector('td').getAttribute('colspan') === null);
                    total = rows.length;
                    rows.forEach(r => {
                        const estadoCell = r.querySelectorAll('td')[7];
                        const estadoText = estadoCell ? estadoCell.textContent.trim().toLowerCase() : '';
                        if (estadoText.includes('complet') || estadoText.includes('pagad') || estadoText.includes('proces')) procesadas++;
                        else if (estadoText.includes('pend')) pendientes++;
                        else if (estadoText.includes('anul')) anuladas++;
                    });
                }
                // actualizar los elementos del modal/consulta si existen
                const setTextIf = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = String(val); };
                setTextIf('total-actas', total);
                setTextIf('actas-procesadas-modal', procesadas);
                setTextIf('actas-pendientes-modal', pendientes);
                setTextIf('actas-anuladas-modal', anuladas);

                // también mantener sincronizados los dashboard cards si están presentes
                setTextIf('dashboard-actas-pendientes', pendientes);
                setTextIf('dashboard-actas-pagadas', procesadas);
                setTextIf('dashboard-actas-en-cobranza', document.getElementById('dashboard-actas-en-cobranza') ? document.getElementById('dashboard-actas-en-cobranza').textContent : document.getElementById('dashboard-actas-en-cobranza'));
                setTextIf('dashboard-actas-anuladas', anuladas);

                showSuccess('Consulta ejecutada correctamente');
            } catch (e) {
                console.warn('No se pudieron calcular totales dinámicos:', e);
                // fallback a valores neutros
                const ids = ['total-actas','actas-procesadas-modal','actas-pendientes-modal','actas-anuladas-modal','dashboard-actas-pendientes','dashboard-actas-pagadas','dashboard-actas-en-cobranza','dashboard-actas-anuladas'];
                ids.forEach(i => { const el = document.getElementById(i); if (el) el.textContent = '0'; });
                showInfo('Consulta ejecutada (valores por defecto)');
            }
        }

        function exportarExcel() {
            // use shared helper - export the first visible table on the page
            exportTableToCSV(null, 'dashboard-export.csv');
        }

        function exportarPDF() {
            // use shared helper - export the first visible table on the page to PDF
            exportTableToPDF(null, 'dashboard-export.pdf');
        }

        function generarReporte() {
            showInfo('Generando reporte estadístico...');
            // Aquí iría la lógica de generación de reportes
        }

        function cancelarEdicion() {
            document.getElementById('resultado-editar').style.display = 'none';
            document.getElementById('buscar-editar').value = '';
            showInfo('Edición cancelada');
        }

        function cancelarEliminacion() {
            document.getElementById('resultado-eliminar').style.display = 'none';
            document.getElementById('buscar-eliminar').value = '';
            showInfo('Operación de eliminación cancelada');
        }

        function confirmarEliminacion() {
            const motivo = document.getElementById('motivo-eliminacion').value;
            const codigo = document.getElementById('codigo-autorizacion').value;
            const supervisor = document.getElementById('supervisor-autorizante').value;
            
            if (!motivo || !codigo || !supervisor) {
                showError('Todos los campos son obligatorios para proceder con la eliminación');
                return;
            }
            
            // Mostrar confirmación adicional
            if (confirm('¿Está COMPLETAMENTE SEGURO de que desea eliminar esta acta? Esta acción es IRREVERSIBLE.')) {
                showSuccess('Acta eliminada correctamente del sistema');
                setTimeout(() => {
                    cerrarModal('modal-eliminar-acta');
                }, 2000);
            }
        }

        // Función para manejar navegación con modales automáticos
        function navegarYAbrirModal(url, modalId) {
            // Si ya estamos en la página correcta, solo abrir el modal
            if (window.location.pathname.includes('actas-contra')) {
                setTimeout(() => {
                    abrirModal(modalId);
                }, 100);
            } else {
                // Si no, navegar primero y luego abrir el modal
                window.location.href = url + '?modal=' + modalId;
            }
        }

        // Detectar si hay un modal en la URL al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const modalToOpen = urlParams.get('modal');
            
            if (modalToOpen) {
                setTimeout(() => {
                    abrirModal(modalToOpen);
                    // Limpiar el parámetro de la URL sin recargar
                    const newUrl = window.location.pathname;
                    window.history.replaceState({}, document.title, newUrl);
                }, 500);
            }
        });
    </script>

    <!-- Toast Notifications Component -->
    @include('components.toast-notifications')

    <!-- Scripts específicos de cada página -->
    @yield('scripts')

    <!-- Script para mostrar mensajes flash como toasts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showToast('¡Éxito!', '{{ session('success') }}', 'success');
            @endif

            @if(session('error'))
                showToast('Error', '{{ session('error') }}', 'error');
            @endif

            @if(session('warning'))
                showToast('Advertencia', '{{ session('warning') }}', 'warning');
            @endif

            @if(session('info'))
                showToast('Información', '{{ session('info') }}', 'info');
            @endif

            @if(session('status'))
                showToast('Estado', '{{ session('status') }}', 'info');
            @endif

            // Toast especial con datos personalizados
            @if(session('toast'))
                @php $toast = session('toast'); @endphp
                showToast(
                    '{{ $toast['title'] ?? 'Notificación' }}', 
                    '{{ $toast['message'] ?? '' }}', 
                    '{{ $toast['type'] ?? 'info' }}',
                    {{ $toast['duration'] ?? 5000 }}
                );
            @endif
        });
    </script>
    @include('partials.export-actas-scripts')
</body>
</html>
