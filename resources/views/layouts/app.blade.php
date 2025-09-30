<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            background: linear-gradient(135deg, #ff8c00 0%, #e67e22 35%, #d35400 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(255, 140, 0, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(30, 58, 138, 0.2) 0%, transparent 50%);
            z-index: -1;
        }
        
        /* Asegurar que el container del login ocupe toda la pantalla */
        .container-fluid {
            padding: 0;
            margin: 0;
            min-height: 100vh;
            width: 100vw;
            max-width: 100%;
            overflow-x: hidden;
        }
        
        /* Row para centrar el login */
        .row.justify-content-center.align-items-center.min-vh-100 {
            min-height: 100vh;
            margin: 0;
            padding: 15px;
            width: 100%;
            max-width: 100%;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
        }
        
        .auth-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(255, 140, 0, 0.3);
            padding: 2rem;
            width: 100%;
            max-width: 420px;
            backdrop-filter: blur(10px);
            border: 2px solid var(--drtc-light-orange);
            margin: 0 auto;
        }
        
        /* Responsividad mejorada para auth-container */
        @media (max-width: 576px) {
            .auth-container {
                padding: 1.5rem;
                max-width: 95%;
                border-radius: 12px;
            }
        }
        
        @media (min-width: 576px) and (max-width: 768px) {
            .auth-container {
                padding: 2rem;
                max-width: 450px;
                border-radius: 15px;
            }
        }
        
        @media (min-width: 768px) and (max-width: 992px) {
            .auth-container {
                padding: 2.5rem;
                max-width: 480px;
                border-radius: 18px;
            }
        }
        
        @media (min-width: 992px) and (max-width: 1200px) {
            .auth-container {
                padding: 3rem;
                max-width: 520px;
                border-radius: 20px;
            }
        }
        
        @media (min-width: 1200px) {
            .auth-container {
                padding: 3.5rem;
                max-width: 560px;
                border-radius: 22px;
            }
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .auth-header h2 {
            color: var(--drtc-navy);
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 1.5rem;
        }
        
        .auth-header h5 {
            font-size: 1.1rem;
            margin-bottom: 0;
        }
        
        .auth-header p {
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        .auth-header .fa-shield-alt {
            color: var(--drtc-orange) !important;
        }
        
        /* Logo circular responsivo */
        .logo-container {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        
        .logo-circle {
            background: linear-gradient(135deg, var(--drtc-orange), var(--drtc-dark-orange));
            border-radius: 50%;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(255, 140, 0, 0.3);
            transition: all 0.3s ease;
        }
        
        .logo-circle:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(255, 140, 0, 0.4);
        }
        
        /* Estilos para el logo oficial */
        .logo-oficial {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }
        
        /* Responsividad para elementos del header */
        @media (max-width: 576px) {
            .auth-header {
                margin-bottom: 1.5rem;
            }
            .auth-header h2 {
                font-size: 1.3rem;
            }
            .auth-header h5 {
                font-size: 1rem;
            }
            .auth-header p {
                font-size: 0.85rem;
            }
            .logo-circle {
                width: 70px;
                height: 70px;
                font-size: 28px;
            }
            .logo-container {
                margin-bottom: 1rem;
            }
        }
        
        @media (min-width: 576px) and (max-width: 768px) {
            .auth-header h2 {
                font-size: 1.6rem;
            }
            .auth-header h5 {
                font-size: 1.15rem;
            }
            .auth-header p {
                font-size: 0.95rem;
            }
            .logo-circle {
                width: 85px;
                height: 85px;
                font-size: 34px;
            }
        }
        
        @media (min-width: 768px) and (max-width: 992px) {
            .auth-header h2 {
                font-size: 1.75rem;
            }
            .auth-header h5 {
                font-size: 1.25rem;
            }
            .auth-header p {
                font-size: 1rem;
            }
            .logo-circle {
                width: 90px;
                height: 90px;
                font-size: 36px;
            }
            .logo-container {
                margin-bottom: 1.75rem;
            }
        }
        
        @media (min-width: 992px) and (max-width: 1200px) {
            .auth-header h2 {
                font-size: 1.9rem;
            }
            .auth-header h5 {
                font-size: 1.35rem;
            }
            .auth-header p {
                font-size: 1.05rem;
            }
            .logo-circle {
                width: 95px;
                height: 95px;
                font-size: 38px;
            }
            .logo-container {
                margin-bottom: 2rem;
            }
        }
        
        @media (min-width: 1200px) {
            .auth-header h2 {
                font-size: 2rem;
            }
            .auth-header h5 {
                font-size: 1.5rem;
            }
            .auth-header p {
                font-size: 1.1rem;
            }
            .logo-circle {
                width: 100px;
                height: 100px;
                font-size: 40px;
            }
            .logo-container {
                margin-bottom: 2.25rem;
            }
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
            width: 100%;
        }
        
        .form-control:focus {
            border-color: var(--drtc-orange);
            box-shadow: 0 0 0 0.2rem rgba(255, 140, 0, 0.25);
        }
        
        .form-label {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #374151;
        }
        
        /* Responsividad para form-controls */
        @media (max-width: 576px) {
            .form-control {
                height: 45px;
                padding: 10px 16px;
                font-size: 15px;
                border-radius: 8px;
            }
            .form-group {
                margin-bottom: 1.25rem;
            }
            .form-label {
                font-size: 13px;
            }
        }
        
        @media (min-width: 576px) and (max-width: 768px) {
            .form-control {
                height: 48px;
                padding: 11px 18px;
                font-size: 15px;
            }
            .form-label {
                font-size: 13.5px;
            }
        }
        
        @media (min-width: 768px) and (max-width: 992px) {
            .form-control {
                height: 52px;
                padding: 13px 22px;
                font-size: 16px;
            }
            .form-group {
                margin-bottom: 1.75rem;
            }
            .form-label {
                font-size: 14.5px;
            }
        }
        
        @media (min-width: 992px) and (max-width: 1200px) {
            .form-control {
                height: 55px;
                padding: 14px 24px;
                font-size: 17px;
            }
            .form-label {
                font-size: 15px;
            }
        }
        
        @media (min-width: 1200px) {
            .form-control {
                height: 58px;
                padding: 15px 26px;
                font-size: 17px;
                border-radius: 12px;
            }
            .form-group {
                margin-bottom: 2rem;
            }
            .form-label {
                font-size: 15.5px;
            }
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--drtc-orange), var(--drtc-dark-orange));
            border: none;
            height: 50px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--drtc-dark-orange), var(--drtc-orange));
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 140, 0, 0.4);
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
            font-size: 14px;
        }
        
        .auth-links {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e1e5e9;
        }
        
        .auth-links p {
            font-size: 14px;
            margin-bottom: 0.5rem;
        }
        
        .auth-links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .auth-links a:hover {
            color: #764ba2;
            transform: translateY(-1px);
        }
        
        /* Responsividad para checkboxes y links */
        @media (max-width: 576px) {
            .form-check-label {
                font-size: 13px;
            }
            .form-check-input {
                width: 1.1em;
                height: 1.1em;
            }
            .auth-links {
                margin-top: 1.25rem;
                padding-top: 1.25rem;
            }
            .auth-links p {
                font-size: 13px;
            }
            .auth-links a {
                font-size: 13px;
            }
        }
        
        @media (min-width: 576px) and (max-width: 768px) {
            .form-check-label {
                font-size: 13.5px;
            }
            .auth-links p {
                font-size: 13.5px;
            }
            .auth-links a {
                font-size: 13.5px;
            }
        }
        
        @media (min-width: 768px) and (max-width: 992px) {
            .form-check-label {
                font-size: 14.5px;
            }
            .form-check-input {
                width: 1.25em;
                height: 1.25em;
            }
            .auth-links {
                margin-top: 1.75rem;
                padding-top: 1.75rem;
            }
            .auth-links p {
                font-size: 14.5px;
            }
            .auth-links a {
                font-size: 14.5px;
            }
        }
        
        @media (min-width: 992px) and (max-width: 1200px) {
            .form-check-label {
                font-size: 15px;
            }
            .form-check-input {
                width: 1.3em;
                height: 1.3em;
            }
            .auth-links p {
                font-size: 15px;
            }
            .auth-links a {
                font-size: 15px;
            }
        }
        
        @media (min-width: 1200px) {
            .form-check-label {
                font-size: 15.5px;
            }
            .form-check-input {
                width: 1.35em;
                height: 1.35em;
            }
            .auth-links {
                margin-top: 2rem;
                padding-top: 2rem;
            }
            .auth-links p {
                font-size: 15.5px;
            }
            .auth-links a {
                font-size: 15.5px;
            }
        }
        
        .auth-links {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e1e5e9;
        }
        
        .auth-links p {
            font-size: 14px;
            margin-bottom: 0.5rem;
        }
        
        .auth-links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .auth-links a:hover {
            color: #764ba2;
            transform: translateY(-1px);
        }
        
        /* Responsividad para auth-links */
        @media (min-width: 768px) {
            .auth-links {
                margin-top: 2rem;
                padding-top: 2rem;
            }
            .auth-links p {
                font-size: 15px;
            }
            .auth-links a {
                font-size: 15px;
            }
        }
        
        @media (min-width: 1200px) {
            .auth-links {
                margin-top: 2.5rem;
                padding-top: 2.5rem;
            }
            .auth-links p {
                font-size: 16px;
            }
            .auth-links a {
                font-size: 16px;
            }
        }
        
        @media (min-width: 1400px) {
            .auth-links {
                margin-top: 3rem;
                padding-top: 3rem;
            }
            .auth-links p {
                font-size: 17px;
            }
            .auth-links a {
                font-size: 17px;
            }
        }
        
                }
        
        /* Navbar usuario */
        
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
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i> {{ auth()->user()->name }}
                        <span class="badge bg-secondary">{{ ucfirst(auth()->user()->role) }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" style="position: absolute; top: 100%; right: 0; z-index: 1055; min-width: 200px;">
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
