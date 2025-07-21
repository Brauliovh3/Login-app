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
        <nav class="navbar navbar-expand topbar mb-4 static-top">
            <button class="btn btn-link d-md-none rounded-circle mr-3 mobile-menu-toggle" onclick="toggleSidebar()">
                <i class="fa fa-bars"></i>
            </button>

            <ul class="navbar-nav ml-auto">
                @auth
                    <!-- Notifications -->
                    <li class="nav-item dropdown no-arrow mx-1">
                        <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell fa-fw"></i>
                            @if(auth()->user()->notifications()->where('read', false)->count() > 0)
                                <span class="badge badge-danger badge-counter">
                                    {{ auth()->user()->notifications()->where('read', false)->count() }}
                                </span>
                            @endif
                        </a>
                        <div class="dropdown-list dropdown-menu dropdown-menu-right shadow">
                            <h6 class="dropdown-header">Centro de Notificaciones</h6>
                            @forelse(auth()->user()->notifications()->latest()->take(3)->get() as $notification)
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-file-alt text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">{{ $notification->created_at->format('F d, Y') }}</div>
                                        <span class="font-weight-bold">{{ $notification->title }}</span>
                                    </div>
                                </a>
                            @empty
                                <a class="dropdown-item text-center small text-gray-500" href="#">No hay notificaciones</a>
                            @endforelse
                            <a class="dropdown-item text-center small text-gray-500" href="{{ route('notifications.index') }}">Ver todas las notificaciones</a>
                        </div>
                    </li>

                    <!-- User Info -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ auth()->user()->name }}</span>
                            <i class="fas fa-user-circle fa-fw"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
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
    </script>

    @yield('scripts')
</body>
</html>
