<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Login')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --drtc-orange: #ff8c00;
            --drtc-dark-orange: #e67c00;
            --drtc-light-orange: #ffb84d;
            --drtc-orange-bg: #fff4e6;
            --drtc-navy: #1e3a8a;
        }
        
        body {
            background: linear-gradient(135deg, var(--drtc-orange) 0%, var(--drtc-dark-orange) 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .auth-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(255, 140, 0, 0.3);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            backdrop-filter: blur(10px);
            border: 2px solid var(--drtc-light-orange);
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .auth-header h2 {
            color: var(--drtc-navy);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .auth-header .fa-shield-alt {
            color: var(--drtc-orange) !important;
        }
        
        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .form-control {
            height: 50px;
            padding: 12px 20px;
            border: 2px solid var(--drtc-light-orange);
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--drtc-orange);
            box-shadow: 0 0 0 0.2rem rgba(255, 140, 0, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--drtc-orange), var(--drtc-dark-orange));
            border: none;
            height: 50px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--drtc-dark-orange), var(--drtc-orange));
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 140, 0, 0.4);
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .password-toggle {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 18px;
        }
        
        .password-toggle:hover {
            color: #333;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            height: 50px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        /* Checkbox personalizado */
        .form-check-input {
            width: 1.2em;
            height: 1.2em;
            margin-right: 0.5rem;
            border: 2px solid #667eea;
            border-radius: 4px;
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        .form-check-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .form-check-label {
            cursor: pointer;
            user-select: none;
        }
        
        .auth-links {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e1e5e9;
        }
        
        .auth-links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .auth-links a:hover {
            color: #764ba2;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: red;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Corregir dropdown que se corta */
        .navbar {
            position: relative;
            overflow: visible !important;
            width: 100% !important;
            max-width: 100vw !important;
        }
        
        .navbar .container {
            max-width: 100% !important;
            padding-left: 15px !important;
            padding-right: 15px !important;
        }
        
        .dropdown-menu {
            position: absolute !important;
            top: 100% !important;
            right: 0 !important;
            left: auto !important;
            z-index: 1055 !important;
            min-width: 200px;
            padding: 0.5rem 0;
            margin: 0.125rem 0 0;
            font-size: 0.875rem;
            color: #212529;
            text-align: left;
            list-style: none;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0,0,0,.15);
            border-radius: 0.375rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            transform: none !important;
        }
        
        .dropdown-menu.show {
            display: block !important;
        }
        
        .navbar .dropdown {
            position: static;
        }
        
        .dropdown-menu-end {
            right: 0 !important;
            left: auto !important;
        }
        
        .dropdown-item {
            display: block;
            width: 100%;
            padding: 0.5rem 1rem;
            clear: both;
            font-weight: 400;
            color: #212529;
            text-align: inherit;
            text-decoration: none;
            white-space: nowrap;
            background-color: transparent;
            border: 0;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
        }
        
        .dropdown-item:hover,
        .dropdown-item:focus {
            color: #1e2125;
            background-color: #e9ecef;
        }
        
        .dropdown-divider {
            height: 0;
            margin: 0.5rem 0;
            overflow: hidden;
            border-top: 1px solid #dee2e6;
        }
        
        .dropdown-header {
            display: block;
            padding: 0.5rem 1rem;
            margin-bottom: 0;
            font-size: 0.875rem;
            color: #6c757d;
            white-space: nowrap;
        }
        
        /* Eliminar barras verticales */
        .navbar-nav {
            flex-direction: row !important;
        }
        
        .nav-item {
            border-right: none !important;
        }
        
        .nav-item::after {
            display: none !important;
        }
        
        /* Prevenir scroll horizontal cuando se abre dropdown */
        html {
            overflow-x: hidden !important;
        }
        
        body {
            overflow-x: hidden !important;
            position: relative;
        }
        
        .navbar .container {
            overflow: visible !important;
        }
        
        /* Asegurar que el dropdown no cause overflow */
        .dropdown-menu {
            right: 0 !important;
            left: auto !important;
            transform: none !important;
            will-change: auto !important;
        }
        
        /* Prevenir que elementos salgan del viewport */
        * {
            max-width: 100%;
            box-sizing: border-box;
        }
        
        /* CSS global para prevenir overflow horizontal */
        html {
            overflow-x: hidden !important;
            max-width: 100vw !important;
        }
        
        .container, .container-fluid {
            max-width: 100% !important;
            overflow-x: hidden !important;
            padding-left: 15px !important;
            padding-right: 15px !important;
        }
        
        .row {
            margin-left: 0 !important;
            margin-right: 0 !important;
            max-width: 100% !important;
        }
        
        [class*="col-"] {
            padding-left: 7.5px !important;
            padding-right: 7.5px !important;
            max-width: 100% !important;
            overflow-x: hidden !important;
        }
        
        /* Prevenir elementos que se salgan del viewport */
        * {
            box-sizing: border-box !important;
        }
        
        .main, main {
            overflow-x: hidden !important;
            max-width: 100% !important;
        }
    </style>
</head>
<body>
    @auth
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="position: relative; overflow: visible;">
        <div class="container" style="position: relative;">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-shield-alt"></i> Sistema
            </a>
            
            <div class="navbar-nav ms-auto" style="position: relative;">
                <div class="nav-item dropdown" style="position: relative;">
                    <a class="nav-link dropdown-toggle position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <span id="notification-badge" class="notification-badge d-none">0</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" style="width: 300px; position: absolute; top: 100%; right: 0; z-index: 1055;">
                        <li><h6 class="dropdown-header">Notificaciones</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('notifications.index') }}">Ver todas</a></li>
                    </ul>
                </div>
                
                <div class="nav-item dropdown" style="position: relative;">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i> {{ auth()->user()->name }}
                        <span class="badge bg-secondary">{{ ucfirst(auth()->user()->role) }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" style="position: absolute; top: 100%; right: 0; z-index: 1055; min-width: 200px;">
                        <li><a class="dropdown-item" href="{{ route('notifications.index') }}">
                            <i class="fas fa-bell"></i> Notificaciones
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <main class="@auth container mt-4 @else container-fluid @endauth" style="position: relative; z-index: 1;">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Toggle password visibility
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Actualizar contador de notificaciones
        @auth
        function updateNotificationCount() {
            fetch('{{ route("notifications.unread-count") }}')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notification-badge');
                    if (badge && data.count > 0) {
                        badge.textContent = data.count;
                        badge.classList.remove('d-none');
                    } else if (badge) {
                        badge.classList.add('d-none');
                    }
                });
        }

        // Actualizar cada 30 segundos
        updateNotificationCount();
        setInterval(updateNotificationCount, 30000);
        @endauth
        
        // Corregir posicionamiento de dropdowns
        document.addEventListener('DOMContentLoaded', function() {
            // Asegurar que los dropdowns se muestren correctamente
            const dropdownElements = document.querySelectorAll('.dropdown-toggle');
            dropdownElements.forEach(function(element) {
                element.addEventListener('click', function(e) {
                    e.preventDefault();
                    const dropdownMenu = this.nextElementSibling;
                    const isShown = dropdownMenu.classList.contains('show');
                    
                    // Cerrar otros dropdowns
                    document.querySelectorAll('.dropdown-menu.show').forEach(function(menu) {
                        menu.classList.remove('show');
                    });
                    
                    // Toggle el dropdown actual
                    if (!isShown) {
                        dropdownMenu.classList.add('show');
                        
                        // Ajustar posición si es necesario
                        const rect = dropdownMenu.getBoundingClientRect();
                        const viewportWidth = window.innerWidth;
                        
                        if (rect.right > viewportWidth) {
                            dropdownMenu.style.right = '0';
                            dropdownMenu.style.left = 'auto';
                        }
                    }
                });
            });
            
            // Cerrar dropdowns al hacer click fuera
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown-menu.show').forEach(function(menu) {
                        menu.classList.remove('show');
                    });
                }
            });
        });
    </script>
    
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    @stack('scripts')
    @yield('scripts')
</body>
</html>
