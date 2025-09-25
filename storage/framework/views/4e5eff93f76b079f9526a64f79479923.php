<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Panel de Control'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?php if(session('user_config.theme') === 'dark'): ?>
        <link href="<?php echo e(asset('css/dark-mode.css')); ?>" rel="stylesheet">
    <?php endif; ?>
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
<body class="<?php echo e(session('user_config.theme', 'light') === 'dark' ? 'dark-mode' : ''); ?>">
    <!-- Sidebar -->
    <nav class="sidebar sidebar-<?php echo e(auth()->user()->role ?? 'default'); ?>" id="sidebar">
        <?php if(auth()->guard()->check()): ?>
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo e(route('dashboard')); ?>">
        <?php else: ?>
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo e(route('dashboard')); ?>">
        <?php endif; ?>
            <div class="sidebar-brand-icon rotate-n-15">
                <i class="fas fa-road"></i>
            </div>
            <div class="sidebar-brand-text mx-3">DRTC Apurímac</div>
        </a>

        <hr class="sidebar-divider my-0" style="border-color: rgba(255, 255, 255, 0.1);">

        <ul class="sidebar-nav">
            <!-- Dashboard -->
            <li class="nav-item">
                <?php if(auth()->guard()->check()): ?>
                    <a class="nav-link" href="#" onclick="showContent('dashboard')">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                <?php endif; ?>
            </li>

            <?php if(auth()->guard()->check()): ?>
                <?php if(auth()->user()->role == 'administrador' || auth()->user()->isSuperAdmin()): ?>
                    <!-- Admin Menu -->
                    <hr class="sidebar-divider" style="border-color: rgba(255, 255, 255, 0.1);">
                    <div class="sidebar-heading" style="color: rgba(255, 255, 255, 0.5); font-size: 0.7rem; padding: 0 1.5rem;">
                        MANTENIMIENTOS
                    </div>

                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showContent('admin-inspectores')">
                            <i class="fas fa-fw fa-user-shield"></i>
                            <span>Mantenimiento Fiscal</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showContent('admin-conductores')">
                            <i class="fas fa-fw fa-id-card"></i>
                            <span>Mantenimiento Conductor</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showContent('admin-usuarios')">
                            <i class="fas fa-fw fa-users-cog"></i>
                            <span>Gestionar Usuarios</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showContent('admin-aprobar')">
                            <i class="fas fa-fw fa-user-check"></i>
                            <span>Aprobar Usuarios</span>
                            <?php
                                $pendingCount = \App\Models\User::where('status', 'pending')->count();
                            ?>
                            <?php if($pendingCount > 0): ?>
                                <span class="badge bg-warning text-dark ms-2"><?php echo e($pendingCount); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>

                <?php elseif(auth()->user()->role == 'fiscalizador'): ?>
                    <!-- Fiscalizador Menu -->
                    <hr class="sidebar-divider" style="border-color: rgba(255, 255, 255, 0.1);">
                    <div class="sidebar-heading" style="color: rgba(255, 255, 255, 0.5); font-size: 0.7rem; padding: 0 1.5rem;">
                        GESTIÓN DE ACTAS
                    </div>

                    <!-- Menu Principal con Dropdown -->
                    <li class="nav-item dropdown-hover">
                        <a class="nav-link dropdown-main" href="#" onclick="showContent('fiscal-actas')">
                            <i class="fas fa-fw fa-file-contract"></i>
                            <span>Actas Contra</span>
                            <i class="fas fa-angle-down dropdown-icon"></i>
                        </a>
                        <!-- Submenu desplegable -->
                        <ul class="dropdown-submenu">
                            <li>
                                <a href="#" class="submenu-item" onclick="event.preventDefault(); showContentModal('nueva-acta')">
                                    <i class="fas fa-plus-circle me-2"></i>
                                    <span>Nueva Acta</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="submenu-item" onclick="event.preventDefault(); showContentModal('editar-acta')">
                                    <i class="fas fa-edit me-2"></i>
                                    <span>Editar Acta</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="submenu-item" onclick="event.preventDefault(); showContentModal('eliminar-acta')">
                                    <i class="fas fa-trash-alt me-2"></i>
                                    <span>Eliminar Acta</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="submenu-item" onclick="event.preventDefault(); showContent('fiscal-consultar')">
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
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showContent('fiscal-carga')">
                            <i class="fas fa-fw fa-truck-loading"></i>
                            <span>Carga y Pasajero</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showContent('fiscal-empresas')">
                            <i class="fas fa-fw fa-building"></i>
                            <span>Empresas</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showContent('fiscal-calendario')">
                            <i class="fas fa-fw fa-calendar-alt"></i>
                            <span>Calendario</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showContent('fiscal-inspecciones')">
                            <i class="fas fa-fw fa-search"></i>
                            <span>Inspecciones</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showContent('fiscal-carga-paga')">
                            <i class="fas fa-fw fa-truck"></i>
                            <span>Carga Paga</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showContent('fiscal-consultar')">
                            <i class="fas fa-fw fa-question-circle"></i>
                            <span>Consultas</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showContent('fiscal-reportes')">
                            <i class="fas fa-fw fa-chart-bar"></i>
                            <span>Reportes</span>
                        </a>
                    </li>

                <?php elseif(auth()->user()->role == 'ventanilla'): ?>
                    <!-- Ventanilla Menu -->
                    <hr class="sidebar-divider" style="border-color: rgba(255, 255, 255, 0.1);">
                    <div class="sidebar-heading" style="color: rgba(255, 255, 255, 0.5); font-size: 0.7rem; padding: 0 1.5rem;">
                        ATENCIÓN
                    </div>

                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showContent('ventanilla-atencion')">
                            <i class="fas fa-fw fa-user-plus"></i>
                            <span>Nueva Atención</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showContent('ventanilla-tramites')">
                            <i class="fas fa-fw fa-file-alt"></i>
                            <span>Trámites</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showContent('ventanilla-consultar')">
                            <i class="fas fa-fw fa-search"></i>
                            <span>Consultar Estado</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showContent('ventanilla-cola')">
                            <i class="fas fa-fw fa-users"></i>
                            <span>Cola de Espera</span>
                        </a>
                    </li>
                <?php elseif(auth()->user()->role == 'inspector'): ?>
                    <!-- Inspector Menu -->
                    <hr class="sidebar-divider" style="border-color: rgba(255, 255, 255, 0.1);">
                    <div class="sidebar-heading" style="color: rgba(255, 255, 255, 0.5); font-size: 0.7rem; padding: 0 1.5rem;">
                        INSPECCIONES
                    </div>

                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showContent('inspector-acta')">
                            <i class="fas fa-fw fa-plus-circle"></i>
                            <span>Nueva Inspección</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showContent('inspector-inspecciones')">
                            <i class="fas fa-fw fa-list-check"></i>
                            <span>Mis Inspecciones</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showContent('inspector-vehiculos')">
                            <i class="fas fa-fw fa-car"></i>
                            <span>Vehículos</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showContent('inspector-reportes')">
                            <i class="fas fa-fw fa-chart-bar"></i>
                            <span>Reportes</span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Super Admin siempre visible para superadmins -->
                <?php if(auth()->user()->isSuperAdmin()): ?>
                    <hr class="sidebar-divider" style="border-color: rgba(255, 255, 255, 0.1);">
                    <div class="sidebar-heading" style="color: rgba(255, 255, 255, 0.5); font-size: 0.7rem; padding: 0 1.5rem;">
                        SUPER ADMIN
                    </div>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showContent('superadmin')">
                            <i class="fas fa-fw fa-shield-alt"></i>
                            <span>Panel Super Admin</span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Perfil siempre visible -->
                <hr class="sidebar-divider" style="border-color: rgba(255, 255, 255, 0.1);">
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="showContent('profile')">
                        <i class="fas fa-fw fa-user"></i>
                        <span>Mi Perfil</span>
                    </a>
                </li>
            <?php endif; ?>
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
                    <?php echo $__env->yieldContent('title', 'Panel de Control'); ?>
                </span>
            </div>

            <!-- Usuario en la esquina superior derecha -->
            <ul class="navbar-nav">
                <?php if(auth()->guard()->check()): ?>
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: #5a5c69; padding: 0.75rem;">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small font-weight-bold"><?php echo e(auth()->user()->name); ?></span>
                            <i class="fas fa-user-circle fa-lg" style="color: #858796;"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                            <a class="dropdown-item" href="<?php echo e(route('user.perfil')); ?>">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                Mi Perfil
                            </a>
                            <a class="dropdown-item" href="<?php echo e(route('user.configuracion')); ?>">
                                <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                Configuración
                            </a>
                            <?php if(auth()->user()->role === 'superadmin'): ?>
                                <a class="dropdown-item" href="<?php echo e(route('admin.super.index')); ?>" style="color: #e74c3c;">
                                    <i class="fas fa-shield-alt fa-sm fa-fw mr-2" style="color: #e74c3c;"></i>
                                    Panel Super Admin
                                </a>
                            <?php endif; ?>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="<?php echo e(route('logout')); ?>" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Page Content -->
        <div class="content-wrapper">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>

    <!-- Sistema de Notificaciones Flotantes -->
    <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 350px;"></div>

    <!-- Modales para Actas de Fiscalización -->
    
    <!-- Modal Nueva Acta -->
    <div class="floating-modal" id="modal-nueva-acta">
        <div class="modal-content-wrapper">
            <div class="modal-header-custom">
                <h5><i class="fas fa-plus-circle mr-2"></i>Nueva Acta de Fiscalización</h5>
                <button class="close-modal" onclick="cerrarModal('modal-nueva-acta')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body-custom">
                <form>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Fecha de Intervención</label>
                                <input type="date" class="form-control" name="fecha_intervencion" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Hora de Intervención</label>
                                <input type="time" class="form-control" name="hora_intervencion" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">DNI/Documento del Conductor</label>
                                <input type="text" class="form-control" name="dni_conductor" placeholder="Ingrese DNI" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Nombres y Apellidos</label>
                                <input type="text" class="form-control" name="nombres_conductor" placeholder="Se completará automáticamente" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Placa del Vehículo</label>
                                <input type="text" class="form-control" name="placa_vehiculo" placeholder="ABC-123" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Tipo de Vehículo</label>
                                <select class="form-control" name="tipo_vehiculo" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="automovil">Automóvil</option>
                                    <option value="camioneta">Camioneta</option>
                                    <option value="omnibus">Ómnibus</option>
                                    <option value="camion">Camión</option>
                                    <option value="motocicleta">Motocicleta</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Infracciones Cometidas</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="infr1" name="infracciones[]" value="exceso_velocidad">
                                    <label class="form-check-label" for="infr1">Exceso de Velocidad</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="infr2" name="infracciones[]" value="no_licencia">
                                    <label class="form-check-label" for="infr2">Conducir sin Licencia</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="infr3" name="infracciones[]" value="no_soat">
                                    <label class="form-check-label" for="infr3">Sin SOAT</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="infr4" name="infracciones[]" value="sobrecarga">
                                    <label class="form-check-label" for="infr4">Sobrecarga</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="infr5" name="infracciones[]" value="mal_estado">
                                    <label class="form-check-label" for="infr5">Vehículo en mal estado</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="infr6" name="infracciones[]" value="otros">
                                    <label class="form-check-label" for="infr6">Otros</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="form-label">Observaciones</label>
                        <textarea class="form-control" name="observaciones" rows="4" placeholder="Describa los detalles de la infracción..."></textarea>
                    </div>
                    
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary me-2" onclick="cerrarModal('modal-nueva-acta')">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>Guardar Acta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Acta -->
    <div class="floating-modal" id="modal-editar-acta">
        <div class="modal-content-wrapper">
            <div class="modal-header-custom">
                <h5><i class="fas fa-edit mr-2"></i>Editar Acta de Fiscalización</h5>
                <button class="close-modal" onclick="cerrarModal('modal-editar-acta')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body-custom">
                <div class="form-group mb-4">
                    <label class="form-label">Buscar Acta a Editar</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="buscar-editar" placeholder="Ingrese número de acta, DNI o placa">
                        <button class="btn btn-outline-primary" onclick="buscarActaEditar()">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
                
                <div id="resultado-editar" style="display: none;">
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle mr-2"></i>
                        Acta encontrada: <strong id="acta-numero-editar"></strong>
                    </div>
                    
                    <form>
                        <!-- Aquí iría el formulario de edición similar al de nueva acta -->
                        <div class="form-group mb-3">
                            <label class="form-label">Estado del Acta</label>
                            <select class="form-control">
                                <option value="pendiente">Pendiente</option>
                                <option value="procesada">Procesada</option>
                                <option value="pagada">Pagada</option>
                                <option value="anulada">Anulada</option>
                            </select>
                        </div>
                        
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary me-2" onclick="cancelarEdicion()">Cancelar</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save mr-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar Acta -->
    <div class="floating-modal" id="modal-eliminar-acta">
        <div class="modal-content-wrapper">
            <div class="modal-header-custom">
                <h5><i class="fas fa-trash-alt mr-2"></i>Eliminar Acta de Fiscalización</h5>
                <button class="close-modal" onclick="cerrarModal('modal-eliminar-acta')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body-custom">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>¡Advertencia!</strong> Esta acción es irreversible. Una vez eliminada, no se podrá recuperar la información.
                </div>
                
                <div class="form-group mb-4">
                    <label class="form-label">Buscar Acta a Eliminar</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="buscar-eliminar" placeholder="Ingrese número de acta, DNI o placa">
                        <button class="btn btn-outline-danger" onclick="buscarActaEliminar()">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
                
                <div id="resultado-eliminar" style="display: none;">
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle mr-2"></i>
                        Se encontró la siguiente acta. Verifique que sea la correcta antes de proceder.
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-body">
                            <h6 class="card-title">Información del Acta</h6>
                            <p><strong>Número:</strong> DRTC-APU-2024-001</p>
                            <p><strong>Conductor:</strong> Juan Pérez López</p>
                            <p><strong>Placa:</strong> ABC-123</p>
                            <p><strong>Fecha:</strong> 12/01/2024</p>
                            <p><strong>Estado:</strong> <span class="badge bg-warning">Pendiente</span></p>
                        </div>
                    </div>
                    
                    <form>
                        <div class="form-group mb-3">
                            <label class="form-label">Motivo de la Eliminación</label>
                            <textarea class="form-control" id="motivo-eliminacion" rows="3" placeholder="Describa el motivo por el cual se elimina esta acta..." required></textarea>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Código de Autorización</label>
                            <input type="password" class="form-control" id="codigo-autorizacion" placeholder="Código de autorización de supervisor" required>
                        </div>
                        
                        <div class="form-group mb-4">
                            <label class="form-label">Supervisor Autorizante</label>
                            <input type="text" class="form-control" id="supervisor-autorizante" placeholder="Nombre del supervisor que autoriza" required>
                        </div>
                        
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary me-2" onclick="cancelarEliminacion()">Cancelar</button>
                            <button type="button" class="btn btn-danger" onclick="confirmarEliminacion()">
                                <i class="fas fa-trash mr-2"></i>Eliminar Acta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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

    <?php echo $__env->yieldPushContent('scripts'); ?>
    <?php echo $__env->yieldContent('scripts'); ?>

    <!-- Scripts para modales flotantes -->
    <script>
        // Funciones para gestionar modales flotantes
        let modalZIndex = 9999; // Z-index base para modales
        
        function abrirModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                // Incrementar z-index para cada modal nuevo
                modalZIndex += 10;
                modal.style.zIndex = modalZIndex;
                
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
                
                // Lógica especial para modal de consultas
                if (modalId === 'modal-consultas') {
                    // Asegurar que el modal de consultas siempre esté encima
                    modalZIndex += 20;
                    modal.style.zIndex = modalZIndex;
                }
            }
        }

        function cerrarModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('show');
                
                // Resetear z-index al cerrar
                modal.style.zIndex = '';
                
                // Si no hay más modales abiertos, restaurar scroll del body
                const modalesAbiertos = document.querySelectorAll('.floating-modal.show');
                if (modalesAbiertos.length === 0) {
                    document.body.style.overflow = 'auto';
                    modalZIndex = 9999; // Resetear contador
                }
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
    <?php echo $__env->make('components.toast-notifications', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Scripts específicos de cada página -->
    <?php echo $__env->yieldContent('scripts'); ?>

    <!-- Script para mostrar mensajes flash como toasts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if(session('success')): ?>
                showToast('¡Éxito!', '<?php echo e(session('success')); ?>', 'success');
            <?php endif; ?>

            <?php if(session('error')): ?>
                showToast('Error', '<?php echo e(session('error')); ?>', 'error');
            <?php endif; ?>

            <?php if(session('warning')): ?>
                showToast('Advertencia', '<?php echo e(session('warning')); ?>', 'warning');
            <?php endif; ?>

            <?php if(session('info')): ?>
                showToast('Información', '<?php echo e(session('info')); ?>', 'info');
            <?php endif; ?>

            <?php if(session('status')): ?>
                showToast('Estado', '<?php echo e(session('status')); ?>', 'info');
            <?php endif; ?>

            // Toast especial con datos personalizados
            <?php if(session('toast')): ?>
                <?php $toast = session('toast'); ?>
                showToast(
                    '<?php echo e($toast['title'] ?? 'Notificación'); ?>', 
                    '<?php echo e($toast['message'] ?? ''); ?>', 
                    '<?php echo e($toast['type'] ?? 'info'); ?>',
                    <?php echo e($toast['duration'] ?? 5000); ?>

                );
            <?php endif; ?>
        });

        // Funciones globales para navegación de módulos - DESHABILITADAS
        // Ya no se usan, se restauró la navegación por URLs
        /*
        window.showModule = function(moduleId) {
            // Verificar si estamos en el dashboard
            if (window.location.pathname !== '/dashboard') {
                // Si no estamos en dashboard, navegar allí con el módulo
                window.location.href = '/dashboard?module=' + moduleId;
                return;
            }

            // Ocultar el dashboard principal
            document.querySelector('.container-fluid > .row').style.display = 'none';
            document.querySelectorAll('.container-fluid > .row').forEach(row => {
                row.style.display = 'none';
            });
            
            // Mostrar el contenedor de módulos
            const modulesContainer = document.getElementById('modules-container');
            if (modulesContainer) {
                modulesContainer.style.display = 'block';
                
                // Ocultar todos los módulos
                document.querySelectorAll('.module-content').forEach(module => {
                    module.style.display = 'none';
                });
                
                // Mostrar el módulo específico
                const targetModule = document.getElementById('module-' + moduleId);
                if (targetModule) {
                    targetModule.style.display = 'block';
                    
                    // Scroll hacia arriba
                    window.scrollTo(0, 0);
                    
                    // Actualizar el título de la página
                    const moduleTitle = targetModule.querySelector('h4').textContent;
                    document.title = `${moduleTitle} - DRTC Apurímac`;
                    
                    // Actualizar URL sin recargar la página
                    if (history.pushState) {
                        const newUrl = `/dashboard?module=${moduleId}`;
                        history.pushState({ module: moduleId }, '', newUrl);
                    }
                    
                    console.log(`Módulo cargado: ${moduleTitle}`);
                } else {
                    console.error('Módulo no encontrado: ' + moduleId);
                    // Si el módulo no existe, regresar al dashboard
                    hideModules();
                }
            } else {
                // Si no hay contenedor de módulos, navegar con URL
                window.location.href = '/dashboard?module=' + moduleId;
            }
        };

        window.hideModules = function() {
            const modulesContainer = document.getElementById('modules-container');
            if (modulesContainer) {
                // Ocultar el contenedor de módulos
                modulesContainer.style.display = 'none';
                
                // Mostrar el dashboard principal
                document.querySelectorAll('.container-fluid > .row').forEach(row => {
                    row.style.display = 'block';
                });
                
                // Actualizar URL
                if (history.pushState) {
                    history.pushState(null, '', '/dashboard');
                }
                
                // Restaurar título
                document.title = 'Dashboard - DRTC Apurímac';
            }
        };
        */
    </script>
    <?php echo $__env->make('partials.export-actas-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Sistema de contenido dinámico -->
    <script>
        // Sistema de contenido dinámico
        function showContent(section) {
            console.log('Cargando sección:', section);
            
            // Obtener el contenedor de contenido principal
            const contentWrapper = document.querySelector('.content-wrapper');
            if (!contentWrapper) {
                console.error('No se encontró el contenedor de contenido');
                return;
            }

            // Mostrar loader
            contentWrapper.innerHTML = `
                <div class="d-flex justify-content-center py-5">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <div class="ms-3">
                        <h5 class="text-muted">Cargando contenido...</h5>
                    </div>
                </div>
            `;

            // Simular carga y mostrar contenido según la sección
            setTimeout(() => {
                loadSectionContent(section);
                updateActiveNavigation(section);
            }, 500);
        }

        function loadSectionContent(section) {
            const contentWrapper = document.querySelector('.content-wrapper');
            let content = '';

            switch(section) {
                case 'dashboard':
                    content = getDashboardContent();
                    break;
                case 'admin-usuarios':
                    content = getAdminUsuariosContent();
                    break;
                case 'admin-inspectores':
                    content = getAdminInspectoresContent();
                    break;
                case 'admin-conductores':
                    content = getAdminConductoresContent();
                    break;
                case 'admin-aprobar':
                    content = getAdminAprobarContent();
                    break;
                case 'fiscal-actas':
                    content = getFiscalActasContent();
                    break;
                case 'fiscal-carga':
                    content = getFiscalCargaContent();
                    break;
                case 'fiscal-empresas':
                    content = getFiscalEmpresasContent();
                    break;
                case 'fiscal-calendario':
                    content = getFiscalCalendarioContent();
                    break;
                case 'fiscal-inspecciones':
                    content = getFiscalInspeccionesContent();
                    break;
                case 'fiscal-carga-paga':
                    content = getFiscalCargaPagaContent();
                    break;
                case 'fiscal-consultar':
                    content = getFiscalConsultarContent();
                    break;
                case 'fiscal-reportes':
                    content = getFiscalReportesContent();
                    break;
                case 'ventanilla-atencion':
                    content = getVentanillaAtencionContent();
                    break;
                case 'ventanilla-tramites':
                    content = getVentanillaTramitesContent();
                    break;
                case 'ventanilla-consultar':
                    content = getVentanillaConsultarContent();
                    break;
                case 'ventanilla-cola':
                    content = getVentanillaColaContent();
                    break;
                case 'inspector-acta':
                    content = getInspectorActaContent();
                    break;
                case 'inspector-inspecciones':
                    content = getInspectorInspeccionesContent();
                    break;
                case 'inspector-vehiculos':
                    content = getInspectorVehiculosContent();
                    break;
                case 'inspector-reportes':
                    content = getInspectorReportesContent();
                    break;
                case 'superadmin':
                    content = getSuperAdminContent();
                    break;
                case 'profile':
                    content = getProfileContent();
                    break;
                default:
                    content = '<div class="alert alert-warning">Sección no encontrada</div>';
            }

            contentWrapper.innerHTML = content;

            // Ejecutar scripts de inicialización si existen
            if (window[section + 'Init']) {
                window[section + 'Init']();
            }
        }

        function updateActiveNavigation(section) {
            // Remover todas las clases active
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });

            // Agregar clase active al enlace correspondiente
            const activeLink = document.querySelector(`[onclick="showContent('${section}')"]`);
            if (activeLink) {
                activeLink.classList.add('active');
            }
        }

        // Función para mostrar contenido en modal
        function showContentModal(contentType) {
            switch(contentType) {
                case 'nueva-acta':
                    abrirModal('modal-nueva-acta');
                    break;
                case 'editar-acta':
                    abrirModal('modal-editar-acta');
                    break;
                case 'eliminar-acta':
                    abrirModal('modal-eliminar-acta');
                    break;
            }
        }

        // Funciones de contenido para cada sección
        function getDashboardContent() {
            return `
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                                <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                            </div>
                            
                            <!-- Estadísticas principales -->
                            <div class="row">
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card border-left-primary shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Actas Pendientes</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">15</div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-file-contract fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card border-left-success shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Actas Procesadas</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">125</div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card border-left-info shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Usuarios Activos</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">8</div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card border-left-warning shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Inspecciones Hoy</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">3</div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-search fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Actividad reciente -->
                            <div class="row mt-4">
                                <div class="col-lg-6">
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-primary">Actividad Reciente</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center">
                                                <p class="text-muted">Panel de actividad reciente del sistema</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-6">
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-primary">Accesos Rápidos</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6 text-center mb-3">
                                                    <button class="btn btn-primary btn-circle btn-lg" onclick="showContent('fiscal-actas')">
                                                        <i class="fas fa-file-contract"></i>
                                                    </button>
                                                    <p class="text-xs mt-2">Nueva Acta</p>
                                                </div>
                                                <div class="col-6 text-center mb-3">
                                                    <button class="btn btn-success btn-circle btn-lg" onclick="showContent('fiscal-consultar')">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                    <p class="text-xs mt-2">Consultas</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function getSuperAdminContent() {
            return \`
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                                <h1 class="h3 mb-0" style="color: #e74c3c;">
                                    <i class="fas fa-shield-alt mr-2"></i>Panel Super Admin
                                </h1>
                            </div>
                            
                            <!-- Alerta de seguridad -->
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Área Restringida:</strong> Solo para administradores del sistema con permisos especiales.
                            </div>
                            
                            <!-- Herramientas de sistema -->
                            <div class="row">
                                <div class="col-lg-4 mb-4">
                                    <div class="card border-left-danger shadow h-100">
                                        <div class="card-header bg-danger text-white">
                                            <i class="fas fa-database mr-2"></i>Gestión de Base de Datos
                                        </div>
                                        <div class="card-body">
                                            <button class="btn btn-outline-danger btn-block mb-2" onclick="reiniciarIncrementos()">
                                                <i class="fas fa-redo mr-2"></i>Reiniciar Incrementos de Actas
                                            </button>
                                            <button class="btn btn-outline-danger btn-block mb-2" onclick="limpiarLogs()">
                                                <i class="fas fa-trash mr-2"></i>Limpiar Logs del Sistema
                                            </button>
                                            <button class="btn btn-outline-danger btn-block" onclick="optimizarBD()">
                                                <i class="fas fa-tools mr-2"></i>Optimizar Base de Datos
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-4 mb-4">
                                    <div class="card border-left-warning shadow h-100">
                                        <div class="card-header bg-warning text-dark">
                                            <i class="fas fa-users-cog mr-2"></i>Gestión de Usuarios
                                        </div>
                                        <div class="card-body">
                                            <button class="btn btn-outline-warning btn-block mb-2" onclick="crearUsuarioAdmin()">
                                                <i class="fas fa-user-plus mr-2"></i>Crear Usuario Admin
                                            </button>
                                            <button class="btn btn-outline-warning btn-block mb-2" onclick="verPasswordsUsuarios()">
                                                <i class="fas fa-eye mr-2"></i>Ver Contraseñas
                                            </button>
                                            <button class="btn btn-outline-warning btn-block" onclick="resetearPassword()">
                                                <i class="fas fa-key mr-2"></i>Resetear Contraseñas
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-4 mb-4">
                                    <div class="card border-left-info shadow h-100">
                                        <div class="card-header bg-info text-white">
                                            <i class="fas fa-terminal mr-2"></i>Console Terminal
                                        </div>
                                        <div class="card-body">
                                            <button class="btn btn-outline-info btn-block mb-2" onclick="abrirConsole()">
                                                <i class="fas fa-terminal mr-2"></i>Abrir Console
                                            </button>
                                            <button class="btn btn-outline-info btn-block mb-2" onclick="verLogsMovimientos()">
                                                <i class="fas fa-list-alt mr-2"></i>Ver Logs de Movimientos
                                            </button>
                                            <button class="btn btn-outline-info btn-block" onclick="ejecutarComandos()">
                                                <i class="fas fa-code mr-2"></i>Ejecutar Comandos
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Estadísticas del sistema -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card shadow">
                                        <div class="card-header">
                                            <h6 class="m-0 font-weight-bold text-primary">Estadísticas del Sistema</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3 text-center">
                                                    <h3 class="text-primary">\${Math.floor(Math.random() * 1000)}</h3>
                                                    <p class="text-muted">Total Actas</p>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <h3 class="text-success">\${Math.floor(Math.random() * 50)}</h3>
                                                    <p class="text-muted">Usuarios Activos</p>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <h3 class="text-info">\${Math.floor(Math.random() * 100)}</h3>
                                                    <p class="text-muted">Sesiones Hoy</p>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <h3 class="text-warning">\${Math.floor(Math.random() * 10)}</h3>
                                                    <p class="text-muted">Errores Sistema</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Console Modal -->
                <div class="modal fade" id="console-modal" tabindex="-1">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header bg-dark text-white">
                                <h5 class="modal-title">
                                    <i class="fas fa-terminal mr-2"></i>Console Terminal - Super Admin
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-0">
                                <div class="bg-dark text-light p-3" style="min-height: 400px; font-family: 'Courier New', monospace;">
                                    <div id="console-output">
                                        <div class="text-success">DRTC Apurímac - Super Admin Console v1.0</div>
                                        <div class="text-warning">Usuario: Brauliovh3 (Super Admin)</div>
                                        <div class="text-info">Sistema iniciado correctamente</div>
                                        <div class="mt-2">root@drtc-apurimac:~# <span class="blinking-cursor">|</span></div>
                                    </div>
                                    <input type="text" class="form-control bg-dark text-light border-0 mt-2" 
                                           id="console-input" placeholder="Ingrese comando..." 
                                           style="font-family: 'Courier New', monospace;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            \`;
        }

        // Funciones específicas del Super Admin
        function reiniciarIncrementos() {
            Swal.fire({
                title: '¿Reiniciar incrementos?',
                text: 'Esta acción reiniciará los contadores de actas. ¿Está seguro?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, reiniciar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Simular proceso
                    Swal.fire({
                        title: 'Procesando...',
                        html: 'Reiniciando incrementos de actas...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    setTimeout(() => {
                        Swal.fire('¡Completado!', 'Los incrementos han sido reiniciados correctamente', 'success');
                    }, 2000);
                }
            });
        }

        function abrirConsole() {
            const modal = new bootstrap.Modal(document.getElementById('console-modal'));
            modal.show();
            
            // Focus en el input cuando se abra
            document.getElementById('console-modal').addEventListener('shown.bs.modal', function() {
                document.getElementById('console-input').focus();
            });
        }

        function verPasswordsUsuarios() {
            showInfo('Función disponible solo en versión completa del sistema');
        }

        function limpiarLogs() {
            showWarning('Logs del sistema limpiados correctamente');
        }

        // Agregar más funciones de contenido según sea necesario...
        
        function getFiscalActasContent() {
            return \`
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <h1 class="h3 mb-4 text-gray-800">Gestión de Actas de Fiscalización</h1>
                            
                            <div class="row">
                                <div class="col-lg-3 col-md-6 mb-4">
                                    <div class="card bg-primary text-white shadow h-100 py-2 cursor-pointer" onclick="abrirModal('modal-nueva-acta')">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Nueva Acta</div>
                                                    <div class="h5 mb-0 font-weight-bold">Crear</div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-plus-circle fa-2x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-3 col-md-6 mb-4">
                                    <div class="card bg-success text-white shadow h-100 py-2 cursor-pointer" onclick="abrirModal('modal-editar-acta')">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Editar Acta</div>
                                                    <div class="h5 mb-0 font-weight-bold">Modificar</div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-edit fa-2x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-3 col-md-6 mb-4">
                                    <div class="card bg-danger text-white shadow h-100 py-2 cursor-pointer" onclick="abrirModal('modal-eliminar-acta')">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Eliminar Acta</div>
                                                    <div class="h5 mb-0 font-weight-bold">Anular</div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-trash-alt fa-2x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-3 col-md-6 mb-4">
                                    <div class="card bg-info text-white shadow h-100 py-2 cursor-pointer" onclick="showContent('fiscal-consultar')">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Consultas</div>
                                                    <div class="h5 mb-0 font-weight-bold">Buscar</div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-search fa-2x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tabla de actas recientes -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Actas Recientes</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Nº Acta</th>
                                                    <th>Fecha</th>
                                                    <th>Conductor</th>
                                                    <th>Placa</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>DRTC-APU-2024-001</td>
                                                    <td>12/01/2024</td>
                                                    <td>Juan Pérez</td>
                                                    <td>ABC-123</td>
                                                    <td><span class="badge bg-warning">Pendiente</span></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary">Ver</button>
                                                        <button class="btn btn-sm btn-success">Editar</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            \`;
        }

        // Funciones de contenido simples para otras secciones
        function getAdminUsuariosContent() {
            return '<div class="container-fluid"><h1>Gestión de Usuarios</h1><p>Contenido de gestión de usuarios...</p></div>';
        }
        
        function getAdminInspectoresContent() {
            return '<div class="container-fluid"><h1>Mantenimiento Fiscal</h1><p>Contenido de mantenimiento de fiscalizadores...</p></div>';
        }

        function getAdminConductoresContent() {
            return '<div class="container-fluid"><h1>Mantenimiento de Conductores</h1><p>Contenido de gestión de conductores...</p></div>';
        }

        function getAdminAprobarContent() {
            return '<div class="container-fluid"><h1>Aprobar Usuarios</h1><p>Lista de usuarios pendientes de aprobación...</p></div>';
        }

        function getFiscalCargaContent() {
            return '<div class="container-fluid"><h1>Carga y Pasajero</h1><p>Gestión de servicios de carga y pasajeros...</p></div>';
        }

        function getFiscalEmpresasContent() {
            return '<div class="container-fluid"><h1>Empresas de Transporte</h1><p>Gestión de empresas de transporte...</p></div>';
        }

        function getFiscalCalendarioContent() {
            return '<div class="container-fluid"><h1>Calendario de Fiscalización</h1><p>Calendario y programación de actividades...</p></div>';
        }

        function getFiscalInspeccionesContent() {
            return '<div class="container-fluid"><h1>Inspecciones</h1><p>Gestión de inspecciones de transporte...</p></div>';
        }

        function getFiscalCargaPagaContent() {
            return '<div class="container-fluid"><h1>Carga Paga</h1><p>Gestión de servicios de carga paga...</p></div>';
        }

        function getFiscalConsultarContent() {
            return '<div class="container-fluid"><h1>Consultas y Reportes</h1><p>Sistema de consultas del estado de actas...</p></div>';
        }

        function getFiscalReportesContent() {
            return '<div class="container-fluid"><h1>Reportes Estadísticos</h1><p>Reportes y estadísticas del sistema...</p></div>';
        }

        function getVentanillaAtencionContent() {
            return '<div class="container-fluid"><h1>Nueva Atención</h1><p>Atención al público y ciudadanos...</p></div>';
        }

        function getVentanillaTramitesContent() {
            return '<div class="container-fluid"><h1>Trámites</h1><p>Gestión de trámites administrativos...</p></div>';
        }

        function getVentanillaConsultarContent() {
            return '<div class="container-fluid"><h1>Consultar Estado</h1><p>Consulta del estado de trámites...</p></div>';
        }

        function getVentanillaColaContent() {
            return '<div class="container-fluid"><h1>Cola de Espera</h1><p>Gestión de la cola de atención...</p></div>';
        }

        function getInspectorActaContent() {
            return '<div class="container-fluid"><h1>Nueva Inspección</h1><p>Crear nueva acta de inspección...</p></div>';
        }

        function getInspectorInspeccionesContent() {
            return '<div class="container-fluid"><h1>Mis Inspecciones</h1><p>Lista de inspecciones realizadas...</p></div>';
        }

        function getInspectorVehiculosContent() {
            return '<div class="container-fluid"><h1>Vehículos Inspeccionados</h1><p>Registro de vehículos inspeccionados...</p></div>';
        }

        function getInspectorReportesContent() {
            return '<div class="container-fluid"><h1>Reportes de Inspector</h1><p>Reportes de actividad del inspector...</p></div>';
        }

        function getProfileContent() {
            return '<div class="container-fluid"><h1>Mi Perfil</h1><p>Información y configuración del perfil de usuario...</p></div>';
        }

        // Cargar contenido inicial cuando se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            // Solo cargar dashboard si estamos en la página principal
            if (window.location.pathname === '/dashboard' || window.location.pathname.endsWith('/dashboard')) {
                showContent('dashboard');
            }
        });
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Login-app\resources\views\layouts\dashboard.blade.php ENDPATH**/ ?>