<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard - Sistema Universitario')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @yield('head')
    <style>
        /* Responsive Sidebar */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #dc3545 0%, #0d6efd 100%);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            width: 250px;
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }
        
        .sidebar.show {
            transform: translateX(0);
        }
        
        @media (min-width: 768px) {
            .sidebar {
                position: relative;
                transform: translateX(0);
                width: auto;
            }
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            border-radius: 0.5rem;
            transition: all 0.3s;
            white-space: nowrap;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }
        
        /* Mobile menu toggle */
        .mobile-menu-toggle {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            background: rgba(220, 53, 69, 0.9);
            border: none;
            border-radius: 0.5rem;
            color: white;
            padding: 0.5rem;
            display: block;
        }
        
        @media (min-width: 768px) {
            .mobile-menu-toggle {
                display: none;
            }
        }
        
        /* Overlay for mobile */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            display: none;
        }
        
        .sidebar-overlay.show {
            display: block;
        }
        
        /* Main content responsive */
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
            padding-top: 1rem;
            margin-left: 0;
            transition: margin-left 0.3s ease-in-out;
        }
        
        @media (min-width: 768px) {
            .main-content {
                padding-top: 0;
            }
        }
        
        /* Cards responsive */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 1.5rem;
        }
        
        /* Responsive table wrapper */
        .table-responsive {
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        /* Responsive buttons */
        .btn-group-responsive {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        
        @media (min-width: 576px) {
            .btn-group-responsive {
                flex-direction: row;
            }
        }
        
        /* Responsive form controls */
        .form-control, .form-select {
            margin-bottom: 0.5rem;
        }
        
        @media (min-width: 576px) {
            .form-control, .form-select {
                margin-bottom: 0;
            }
        }
        
        /* Utility classes */
        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }
        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }
        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }
        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }
        
        /* Logo responsive */
        .logo-uagrm-sidebar {
            font-family: 'Arial Black', Arial, sans-serif;
            font-weight: 900;
            line-height: 0.8;
        }
        
        .logo-uagrm-sidebar .logo-top,
        .logo-uagrm-sidebar .logo-bottom {
            font-size: 1.5rem;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        @media (min-width: 576px) {
            .logo-uagrm-sidebar .logo-top,
            .logo-uagrm-sidebar .logo-bottom {
                font-size: 1.8rem;
            }
        }
        
        /* Info boxes responsive */
        .info-box {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 1rem;
        }
        
        .info-box-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 1rem;
            flex-shrink: 0;
        }
        
        .info-box-content {
            flex: 1;
            min-width: 0;
        }
        
        .info-box-text {
            display: block;
            font-size: 0.875rem;
            color: #6c757d;
            margin-bottom: 0.25rem;
        }
        
        .info-box-number {
            display: block;
            font-size: 1.25rem;
            font-weight: 600;
            color: #495057;
        }
        
        /* Mobile text adjustments */
        @media (max-width: 767px) {
            .card-title {
                font-size: 1.1rem;
            }
            
            .btn {
                font-size: 0.875rem;
                padding: 0.5rem 0.75rem;
            }
            
            .table {
                font-size: 0.875rem;
            }
            
            .badge {
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile menu toggle -->
    <button class="mobile-menu-toggle d-md-none" id="mobileMenuToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Sidebar overlay for mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 sidebar" id="sidebar">
                <div class="position-sticky pt-3">
                    <!-- Close button for mobile -->
                    <div class="d-md-none text-end p-2">
                        <button class="btn btn-link text-white" id="closeSidebar">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="text-center mb-4">
                        <div class="logo-uagrm-sidebar mb-2">
                            <div class="logo-top">UA</div>
                            <div class="logo-bottom">GRM</div>
                        </div>
                        <h6 class="text-white mb-1 d-none d-sm-block">Sistema Académico</h6>
                        <h6 class="text-white mb-1 d-sm-none">Sistema</h6>
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
                                    <i class="fas fa-chalkboard-teacher"></i> Gestión de Docentes
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
                                <a class="nav-link {{ request()->routeIs('admin.aulas.*') ? 'active' : '' }}" href="{{ route('admin.aulas.index') }}">
                                    <i class="fas fa-door-closed"></i> Aulas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.horarios.*') ? 'active' : '' }}" href="{{ route('admin.horarios.index') }}">
                                    <i class="fas fa-calendar-alt"></i> Horarios
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.feriados.*') ? 'active' : '' }}" href="{{ route('admin.feriados.index') }}">
                                    <i class="fas fa-calendar-times"></i> Feriados
                                </a>
                            </li>
                        @elseif(session('user_type') == 'profesor')
                            <li class="nav-item">
                                <a class="nav-link active" href="{{ route('profesor.dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-chalkboard"></i> Mis Clases
                                </a>
                            </li>
                        @elseif(session('user_type') == 'estudiante')
                            <li class="nav-item">
                                <a class="nav-link active" href="{{ route('estudiante.dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-chart-line"></i> Mis Notas
                                </a>
                            </li>
                        @endif
                        
                        <li class="nav-item mt-3">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link text-start w-100 text-white-50">
                                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
                <div class="container-fluid px-2 px-md-4">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const closeSidebar = document.getElementById('closeSidebar');
            
            function openSidebar() {
                sidebar.classList.add('show');
                sidebarOverlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
            
            function closeSidebarFunc() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
                document.body.style.overflow = '';
            }
            
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', openSidebar);
            }
            
            if (closeSidebar) {
                closeSidebar.addEventListener('click', closeSidebarFunc);
            }
            
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', closeSidebarFunc);
            }
            
            // Close sidebar when clicking on nav links on mobile
            const navLinks = sidebar.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        closeSidebarFunc();
                    }
                });
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    closeSidebarFunc();
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>