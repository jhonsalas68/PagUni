<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title', 'Dashboard - Sistema Universitario')</title>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#007bff">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SGU">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/icons/icon-32x32.png">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @yield('head')
    
    <style>
        /* Reset básico */
        * {
            box-sizing: border-box;
        }
        
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        /* Navbar móvil */
        .mobile-navbar {
            display: block;
            background: linear-gradient(135deg, #dc3545 0%, #0d6efd 100%);
            color: white;
            padding: 1rem;
            position: sticky;
            top: 0;
            z-index: 1030;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .mobile-toggle {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.25rem;
        }
        
        .mobile-toggle:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            height: 100vh;
            background: linear-gradient(135deg, #dc3545 0%, #0d6efd 100%);
            z-index: 1050;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .sidebar.show {
            transform: translateX(0);
        }
        
        /* Overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        
        /* Contenido principal */
        .main-content {
            width: 100%;
            min-height: 100vh;
        }
        
        .content-wrapper {
            padding: 1rem;
            width: 100%;
        }
        
        /* Desktop */
        @media (min-width: 768px) {
            .mobile-navbar {
                display: none;
            }
            
            .sidebar {
                position: fixed;
                transform: translateX(0);
                width: 280px;
                flex-shrink: 0;
            }
            
            .main-content {
                margin-left: 280px;
                width: calc(100% - 280px);
            }
            
            .dashboard-wrapper {
                display: flex;
            }
            
            .sidebar-overlay {
                display: none;
            }
        }
        
        /* Navegación sidebar */
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            margin: 0.25rem 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }
        
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }
        
        .sidebar .nav-link i {
            margin-right: 0.75rem;
            width: 1.25rem;
            text-align: center;
        }
        
        /* Logout button styling */
        .sidebar form {
            margin: 0;
            padding: 0;
            width: 100%;
        }
        
        .sidebar .nav-link.logout-btn {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            margin: 0.25rem 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            border: none;
            background: transparent;
            width: calc(100% - 1rem);
            text-align: left;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 400;
        }
        
        .sidebar .nav-link.logout-btn:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-link.logout-btn:focus {
            outline: none;
            box-shadow: none;
        }
        
        .sidebar .nav-link.logout-btn i {
            margin-right: 0.75rem;
            width: 1.25rem;
            text-align: center;
        }
        
        /* Logo */
        .logo-uagrm-sidebar {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-weight: bold;
            color: white;
        }
        
        .logo-top {
            font-size: 14px;
            line-height: 1;
        }
        
        .logo-bottom {
            font-size: 12px;
            line-height: 1;
        }
        
        /* Tablas responsivas */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 0.5rem;
            box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.05);
        }
        
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }
        
        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .table-responsive::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* Cards */
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        /* Touch friendly */
        @media (hover: none) and (pointer: coarse) {
            .sidebar .nav-link {
                padding: 1rem;
                margin: 0.125rem 0.5rem;
            }
            
            .btn {
                padding: 0.75rem 1rem;
                min-height: 44px;
            }
            
            .form-control, .form-select {
                padding: 0.75rem;
                min-height: 44px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar móvil -->
    <nav class="mobile-navbar d-md-none">
        <div class="d-flex justify-content-between align-items-center">
            <button class="mobile-toggle" id="mobileToggle" type="button">
                <i class="fas fa-bars"></i>
            </button>
            <a class="navbar-brand text-white text-decoration-none" href="#">
                <strong>SGU</strong>
            </a>
            <div class="text-end">
                <small class="d-block">{{ session('user_name') }}</small>
                <span class="badge bg-light text-dark">{{ ucfirst(session('user_type')) }}</span>
            </div>
        </div>
    </nav>
    
    <!-- Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Wrapper -->
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="position-sticky pt-3">
                <div class="text-center mb-4">
                    <div class="logo-uagrm-sidebar mb-2">
                        <div class="logo-top">UA</div>
                        <div class="logo-bottom">GRM</div>
                    </div>
                    <h6 class="text-white mb-1">Sistema Académico</h6>
                    <small class="text-white-50 d-block">{{ session('user_name') }}</small>
                    <span class="badge bg-light text-dark mt-1">{{ ucfirst(session('user_type')) }}</span>
                </div>
                
                <ul class="nav flex-column">
                    @if(session('user_type') == 'administrador')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.docentes.*') ? 'active' : '' }}" href="{{ route('admin.docentes.index') }}">
                                <i class="fas fa-chalkboard-teacher"></i> Docentes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.facultades.*') ? 'active' : '' }}" href="{{ route('admin.facultades.index') }}">
                                <i class="fas fa-building"></i> Facultades
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.carreras.*') ? 'active' : '' }}" href="{{ route('admin.carreras.index') }}">
                                <i class="fas fa-graduation-cap"></i> Carreras
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.materias.*') ? 'active' : '' }}" href="{{ route('admin.materias.index') }}">
                                <i class="fas fa-book"></i> Materias
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.grupos.*') ? 'active' : '' }}" href="{{ route('admin.grupos.index') }}">
                                <i class="fas fa-users"></i> Grupos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.cargas-academicas.*') ? 'active' : '' }}" href="{{ route('admin.cargas-academicas.index') }}">
                                <i class="fas fa-chalkboard-teacher"></i> Cargas Académicas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.estudiantes.*') ? 'active' : '' }}" href="{{ route('admin.estudiantes.index') }}">
                                <i class="fas fa-user-graduate"></i> Estudiantes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.horarios.*') ? 'active' : '' }}" href="{{ route('admin.horarios.index') }}">
                                <i class="fas fa-calendar-alt"></i> Horarios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.aulas.*') ? 'active' : '' }}" href="{{ route('admin.aulas.index') }}">
                                <i class="fas fa-door-closed"></i> Aulas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.periodos-academicos.*') ? 'active' : '' }}" href="{{ route('admin.periodos-academicos.index') }}">
                                <i class="fas fa-calendar-check"></i> Periodos Académicos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.feriados.*') ? 'active' : '' }}" href="{{ route('admin.feriados.index') }}">
                                <i class="fas fa-calendar-times"></i> Feriados
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.justificaciones.*') ? 'active' : '' }}" href="{{ route('admin.justificaciones.index') }}">
                                <i class="fas fa-file-medical"></i> Justificaciones
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}" href="{{ route('reportes.index') }}">
                                <i class="fas fa-chart-bar"></i> Reportes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.panel-asistencia') ? 'active' : '' }}" href="{{ route('admin.panel-asistencia') }}">
                                <i class="fas fa-tv"></i> Panel Asistencia
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('consulta.aulas.*') ? 'active' : '' }}" href="{{ route('consulta.aulas.index') }}">
                                <i class="fas fa-search"></i> Consultar Aulas
                            </a>
                        </li>
                    @elseif(session('user_type') == 'profesor')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('profesor.dashboard') ? 'active' : '' }}" href="{{ route('profesor.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('profesor.mi-horario') ? 'active' : '' }}" href="{{ route('profesor.mi-horario') }}">
                                <i class="fas fa-calendar"></i> Mi Horario
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('profesor.historial-asistencias') ? 'active' : '' }}" href="{{ route('profesor.historial-asistencias') }}">
                                <i class="fas fa-history"></i> Historial Asistencias
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}" href="{{ route('reportes.index') }}">
                                <i class="fas fa-chart-bar"></i> Reportes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('consulta.aulas.*') ? 'active' : '' }}" href="{{ route('consulta.aulas.index') }}">
                                <i class="fas fa-search"></i> Consultar Aulas
                            </a>
                        </li>
                    @elseif(session('user_type') == 'estudiante')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('estudiante.dashboard') ? 'active' : '' }}" href="{{ route('estudiante.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('consulta.aulas.*') ? 'active' : '' }}" href="{{ route('consulta.aulas.index') }}">
                                <i class="fas fa-search"></i> Consultar Aulas
                            </a>
                        </li>
                    @endif
                    
                    <li class="nav-item mt-3">
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                            @csrf
                            <button type="submit" class="nav-link logout-btn">
                                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Contenido principal -->
        <main class="main-content">
            <div class="content-wrapper">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/history-navigation.js"></script>
    <script src="/js/pagination-scroll.js"></script>
    
    <script>
        // Variables
        let sidebarOpen = false;
        
        // Funciones del sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (!sidebar || !overlay) return;
            
            if (sidebarOpen) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
                sidebarOpen = false;
            } else {
                sidebar.classList.add('show');
                overlay.classList.add('show');
                document.body.style.overflow = 'hidden';
                sidebarOpen = true;
            }
        }
        
        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (sidebar && overlay) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
                sidebarOpen = false;
            }
        }
        
        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            const mobileToggle = document.getElementById('mobileToggle');
            const overlay = document.getElementById('sidebarOverlay');
            const sidebar = document.getElementById('sidebar');
            
            // Click en botón móvil
            if (mobileToggle) {
                mobileToggle.addEventListener('click', toggleSidebar);
            }
            
            // Click en overlay
            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }
            
            // Cerrar con Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && sidebarOpen) {
                    closeSidebar();
                }
            });
            
            // Auto-cerrar en enlaces móviles
            if (sidebar) {
                const navLinks = sidebar.querySelectorAll('.nav-link:not(.logout-btn)');
                navLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth < 768) {
                            setTimeout(closeSidebar, 100);
                        }
                    });
                });
            }
            
            // Manejar logout
            const logoutForm = document.getElementById('logout-form');
            if (logoutForm) {
                logoutForm.addEventListener('submit', function(e) {
                    // Prevenir múltiples envíos
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cerrando sesión...';
                    }
                });
            }
            
            // Responsive
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768 && sidebarOpen) {
                    closeSidebar();
                }
            });
        });
        
        // PWA Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('SW registrado:', registration.scope);
                    })
                    .catch(function(error) {
                        console.log('SW error:', error);
                    });
            });
        }
        
        // Prevenir acceso con botón atrás después de logout
        window.history.pushState(null, "", window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, "", window.location.href);
        };
        
        // Verificar sesión periódicamente
        setInterval(function() {
            fetch('{{ route("login") }}', {
                method: 'HEAD',
                cache: 'no-cache'
            }).then(response => {
                if (response.redirected) {
                    window.location.href = '{{ route("login") }}';
                }
            }).catch(() => {
                // Ignorar errores de red
            });
        }, 300000); // Verificar cada 5 minutos
    </script>
    
    @yield('scripts')
</body>
</html>