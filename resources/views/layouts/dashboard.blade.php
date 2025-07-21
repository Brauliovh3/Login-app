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
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fc;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
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
            background: linear-gradient(180deg, #5e72e4 10%, #3454d1 100%);
        }

        .sidebar.fiscalizador {
            background: linear-gradient(180deg, #36b9cc 10%, #258391 100%);
        }

        .sidebar.ventanilla {
            background: linear-gradient(180deg, #f6c23e 10%, #dda20a 100%);
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
            background: linear-gradient(87deg, #5e72e4 0, #825ee4 100%);
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
                <i class="fas fa-laugh-wink"></i>
            </div>
            <div class="sidebar-brand-text mx-3">Sistema</div>
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
                        ADMINISTRACIÓN
                    </div>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.usuarios') ? 'active' : '' }}" href="{{ route('admin.usuarios') }}">
                            <i class="fas fa-fw fa-users"></i>
                            <span>Gestionar Usuarios</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reportes') ? 'active' : '' }}" href="{{ route('admin.reportes') }}">
                            <i class="fas fa-fw fa-chart-area"></i>
                            <span>Reportes</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.configuracion') ? 'active' : '' }}" href="{{ route('admin.configuracion') }}">
                            <i class="fas fa-fw fa-cog"></i>
                            <span>Configuración</span>
                        </a>
                    </li>

                @elseif(auth()->user()->role == 'fiscalizador')
                    <!-- Fiscalizador Menu -->
                    <hr class="sidebar-divider" style="border-color: rgba(255, 255, 255, 0.1);">
                    <div class="sidebar-heading" style="color: rgba(255, 255, 255, 0.5); font-size: 0.7rem; padding: 0 1.5rem;">
                        FISCALIZACIÓN
                    </div>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('fiscalizador.nueva-inspeccion') ? 'active' : '' }}" href="{{ route('fiscalizador.nueva-inspeccion') }}">
                            <i class="fas fa-fw fa-search-plus"></i>
                            <span>Nueva Inspección</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('fiscalizador.inspecciones') ? 'active' : '' }}" href="{{ route('fiscalizador.inspecciones') }}">
                            <i class="fas fa-fw fa-list-alt"></i>
                            <span>Mis Inspecciones</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('fiscalizador.reportes') ? 'active' : '' }}" href="{{ route('fiscalizador.reportes') }}">
                            <i class="fas fa-fw fa-file-alt"></i>
                            <span>Generar Reporte</span>
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

                <!-- General Menu -->
                <hr class="sidebar-divider" style="border-color: rgba(255, 255, 255, 0.1);">
                <div class="sidebar-heading" style="color: rgba(255, 255, 255, 0.5); font-size: 0.7rem; padding: 0 1.5rem;">
                    GENERAL
                </div>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('notifications.index') }}">
                        <i class="fas fa-fw fa-bell"></i>
                        <span>Notificaciones</span>
                        @if(auth()->user()->notifications()->where('read', false)->count() > 0)
                            <span class="notification-badge">
                                {{ auth()->user()->notifications()->where('read', false)->count() }}
                            </span>
                        @endif
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('session.info') }}">
                        <i class="fas fa-fw fa-info-circle"></i>
                        <span>Info de Sesión</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-fw fa-user"></i>
                        <span>Mi Perfil</span>
                    </a>
                </li>
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

            <!-- Espaciador para empujar todo hacia la derecha -->
            <div class="navbar-nav flex-grow-1"></div>

            <!-- Usuario y Notificaciones en la esquina superior derecha -->
            <ul class="navbar-nav">
                @auth
                    <!-- User Info primero -->
                    <li class="nav-item dropdown no-arrow mr-3">
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

                    <!-- Notifications después -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link d-flex align-items-center" href="#" id="alertsDropdown" role="button" onclick="toggleNotifications(event)" style="color: #5a5c69; padding: 0.75rem; position: relative; cursor: pointer;">
                            <i class="fas fa-bell fa-lg" style="color: #858796;"></i>
                            @if(auth()->user()->notifications()->where('read', false)->count() > 0)
                                <span class="badge badge-danger badge-counter position-absolute" style="top: 0.25rem; right: 0.25rem; background: #e74a3b; color: white; font-size: 0.7rem; min-width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    {{ auth()->user()->notifications()->where('read', false)->count() }}
                                </span>
                            @endif
                        </a>
                        <!-- Dropdown de notificaciones OCULTO por defecto -->
                        <div id="notificationsDropdown" class="dropdown-menu dropdown-menu-right shadow" style="display: none; position: absolute; right: 0; top: 100%; min-width: 22rem; max-width: 25rem; z-index: 1050; border: none; border-radius: 0.5rem; box-shadow: 0 0.5rem 2rem rgba(0, 0, 0, 0.2);">
                            <div class="dropdown-header bg-gradient-primary text-white py-3 px-3 m-0" style="border-radius: 0.5rem 0.5rem 0 0;">
                                <i class="fas fa-bell mr-2"></i>Centro de Notificaciones
                                <button type="button" class="btn-close btn-close-white float-end" onclick="closeNotifications()" style="font-size: 0.8rem;"></button>
                            </div>
                            <div class="notification-body" style="max-height: 400px; overflow-y: auto;">
                                @forelse(auth()->user()->notifications()->latest()->take(5)->get() as $notification)
                                    <a class="dropdown-item d-flex align-items-center py-3 border-bottom notification-item" href="#" style="white-space: normal; transition: background-color 0.2s;">
                                        <div class="mr-3 flex-shrink-0">
                                            <div class="icon-circle bg-primary d-flex align-items-center justify-content-center" style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background: linear-gradient(45deg, #4e73df, #224abe);">
                                                <i class="fas fa-bell text-white"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 min-width-0">
                                            <div class="small text-muted mb-1">{{ $notification->created_at->diffForHumans() }}</div>
                                            <div class="font-weight-bold text-dark mb-1" style="font-size: 0.9rem;">{{ Str::limit($notification->title, 45) }}</div>
                                            @if($notification->message)
                                                <div class="small text-muted" style="line-height: 1.3;">{{ Str::limit($notification->message, 70) }}</div>
                                            @endif
                                        </div>
                                        @if(!$notification->read)
                                            <div class="ml-2 flex-shrink-0">
                                                <span class="badge bg-primary badge-pill" style="font-size: 0.7rem;">Nuevo</span>
                                            </div>
                                        @endif
                                    </a>
                                @empty
                                    <div class="dropdown-item-text text-center py-5">
                                        <i class="fas fa-bell-slash fa-3x text-muted mb-3" style="opacity: 0.3;"></i>
                                        <p class="text-muted mb-0">No tienes notificaciones</p>
                                        <small class="text-muted">Te avisaremos cuando lleguen nuevas</small>
                                    </div>
                                @endforelse
                            </div>
                            @if(auth()->user()->notifications()->count() > 0)
                                <div class="dropdown-divider m-0"></div>
                                <a class="dropdown-item text-center py-3" href="{{ route('notifications.index')}}" style="background: #f8f9fc; color: #5a5c69; font-weight: 500; border-radius: 0 0 0.5rem 0.5rem;">
                                    <i class="fas fa-eye mr-2"></i>Ver todas las notificaciones
                                </a>
                            @endif
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggleButton = document.querySelector('.mobile-menu-toggle');
            
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !toggleButton.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });

        // Funciones para manejar las notificaciones
        function toggleNotifications(event) {
            event.preventDefault();
            event.stopPropagation();
            
            const dropdown = document.getElementById('notificationsDropdown');
            const isVisible = dropdown.style.display === 'block';
            
            if (isVisible) {
                closeNotifications();
            } else {
                openNotifications();
            }
        }

        function openNotifications() {
            const dropdown = document.getElementById('notificationsDropdown');
            dropdown.style.display = 'block';
            
            // Agregar clase para animación
            setTimeout(() => {
                dropdown.classList.add('show');
            }, 10);
        }

        function closeNotifications() {
            const dropdown = document.getElementById('notificationsDropdown');
            dropdown.classList.remove('show');
            
            setTimeout(() => {
                dropdown.style.display = 'none';
            }, 200);
        }

        // Cerrar notificaciones cuando se hace clic fuera
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('notificationsDropdown');
            const bellIcon = document.getElementById('alertsDropdown');
            
            if (dropdown && bellIcon) {
                // Si el dropdown está visible y el clic no es en el dropdown ni en la campanita
                if (dropdown.style.display === 'block' && 
                    !dropdown.contains(event.target) && 
                    !bellIcon.contains(event.target)) {
                    closeNotifications();
                }
            }
        });

        // Prevenir que el dropdown se cierre cuando se hace clic dentro de él
        document.addEventListener('DOMContentLoaded', function() {
            const dropdown = document.getElementById('notificationsDropdown');
            if (dropdown) {
                dropdown.addEventListener('click', function(event) {
                    event.stopPropagation();
                });
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
