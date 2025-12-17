<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="format-detection" content="telephone=no">
    <meta name="mobile-web-app-capable" content="yes">
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
    <link href="/css/responsive.css" rel="stylesheet">
    <link href="/css/mobile-tables.css" rel="stylesheet">
    <link href="/css/components.css" rel="stylesheet">
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

        /* === CHAT SIDEBAR STYLES === */
        
        /* Botón de chat en esquina superior derecha */
        .chat-top-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #dc3545 0%, #0d6efd 100%);
            border: none;
            color: white;
            font-size: 1.2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .chat-top-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            color: white;
        }
        
        .chat-notification-dot {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 10px;
            height: 10px;
            background: #dc3545;
            border-radius: 50%;
            border: 2px solid white;
        }
        
        /* Botón flotante de chat (mantener como backup) */
        .chat-float-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #dc3545 0%, #0d6efd 100%);
            border: none;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            transition: all 0.3s ease;
            display: none; /* Oculto por defecto, usar solo el de arriba */
            align-items: center;
            justify-content: center;
        }
        
        .chat-float-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            color: white;
        }
        
        /* Barra lateral de chat */
        .chat-sidebar {
            position: fixed;
            top: 0;
            right: 0;
            width: 400px;
            height: 100vh;
            background: white;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1060;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        
        .chat-sidebar.show {
            transform: translateX(0);
        }
        
        /* Header del chat sidebar */
        .chat-sidebar-header {
            background: linear-gradient(135deg, #dc3545 0%, #0d6efd 100%);
            color: white;
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: between;
        }
        
        .chat-sidebar-header h5 {
            flex-grow: 1;
            margin: 0;
        }
        
        /* Contenido del chat */
        .chat-sidebar-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        /* Lista de conversaciones */
        .chat-conversations {
            flex: 1;
            overflow-y: auto;
            padding: 1rem 0;
        }
        
        .conversation-item-sidebar {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        
        .conversation-item-sidebar:hover {
            background-color: #f8f9fa;
        }
        
        .conversation-item-sidebar.active {
            background-color: #e3f2fd;
            border-left: 3px solid #2196f3;
        }
        
        /* Área de chat */
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .chat-header {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            background: #f8f9fa;
        }
        
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            background: #f8f9fa;
        }
        
        .chat-input {
            padding: 1rem;
            border-top: 1px solid #eee;
            background: white;
        }
        
        /* Footer del chat */
        .chat-sidebar-footer {
            padding: 1rem;
            border-top: 1px solid #eee;
            background: #f8f9fa;
        }
        
        /* Overlay del chat */
        .chat-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1050;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .chat-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        
        /* Responsive para chat */
        @media (max-width: 768px) {
            .chat-sidebar {
                width: 100%;
            }
            
            .chat-top-btn {
                top: 15px;
                right: 15px;
                width: 45px;
                height: 45px;
                font-size: 1.1rem;
            }
            
            .chat-float-btn {
                bottom: 80px; /* Evitar conflicto con navbar móvil */
            }
        }
        
        /* Estilos para mensajes en sidebar */
        .message-item {
            margin-bottom: 1rem;
            display: flex;
        }
        
        .message-item.own {
            justify-content: flex-end;
        }
        
        .message-bubble {
            max-width: 75%;
            padding: 0.5rem 0.75rem;
            border-radius: 1rem;
            position: relative;
        }
        
        .message-bubble.own {
            background: #007bff;
            color: white;
        }
        
        .message-bubble.other {
            background: #e9ecef;
            color: #333;
        }
        
        .message-time {
            font-size: 0.75rem;
            opacity: 0.7;
            margin-top: 0.25rem;
        }
        
        .message-sender {
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            opacity: 0.8;
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
                            <a class="nav-link {{ request()->routeIs('admin.cargas-academicas.generador') ? 'active' : '' }}" href="{{ route('admin.cargas-academicas.generador') }}">
                                <i class="fas fa-magic"></i> Generar Cargas Aut.
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
                            <a class="nav-link {{ request()->routeIs('admin.horarios.generador.*') ? 'active' : '' }}" href="{{ route('admin.horarios.generador.index') }}">
                                <i class="fas fa-magic"></i> Generador Aut.
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
                            <a class="nav-link {{ request()->routeIs('profesor.calificaciones.*') ? 'active' : '' }}" href="{{ route('profesor.calificaciones.index') }}">
                                <i class="fas fa-star"></i> Calificaciones
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
                            <a class="nav-link {{ request()->routeIs('estudiante.calificaciones.*') ? 'active' : '' }}" href="{{ route('estudiante.calificaciones.index') }}">
                                <i class="fas fa-star"></i> Mis Calificaciones
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

    <!-- Botón de chat en esquina superior derecha -->
    <button class="chat-top-btn" id="chat-top-btn" type="button" title="Mensajes">
        <i class="fas fa-comments"></i>
        <span class="chat-notification-dot d-none" id="chat-top-notification"></span>
    </button>

    <!-- Barra lateral de chat -->
    <div class="chat-sidebar" id="chat-sidebar">
        <div class="chat-sidebar-header">
            <h5 class="mb-0">
                <i class="fas fa-comments me-2"></i>Mensajes
            </h5>
            <button class="btn btn-sm btn-outline-light" id="close-chat-sidebar">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="chat-sidebar-content">
            <!-- Lista de conversaciones -->
            <div class="chat-conversations" id="chat-conversations">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2 text-muted">Cargando conversaciones...</p>
                </div>
            </div>
            
            <!-- Área de chat -->
            <div class="chat-area d-none" id="chat-area">
                <div class="chat-header" id="chat-header-sidebar">
                    <button class="btn btn-sm btn-outline-secondary me-2" id="back-to-conversations-sidebar">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <div class="flex-grow-1" id="chat-title-sidebar">
                        Chat
                    </div>
                    <button class="btn btn-sm btn-outline-danger" id="close-chat-conversation">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="chat-messages" id="chat-messages-sidebar">
                    <!-- Mensajes se cargarán aquí -->
                </div>
                
                <div class="chat-input">
                    <div class="recipient-info mb-2 d-none" id="recipient-info-sidebar">
                        <small class="text-muted">
                            <i class="fas fa-paper-plane me-1"></i>
                            Enviando mensaje <span id="recipient-text-sidebar">a...</span>
                        </small>
                    </div>
                    <form id="message-form-sidebar" class="d-flex gap-2">
                        <input type="hidden" id="current-conversation-id-sidebar">
                        <input type="text" class="form-control" id="message-input-sidebar" placeholder="Escribe un mensaje..." autocomplete="off">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Botón para nuevo chat -->
        <div class="chat-sidebar-footer">
            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#newChatModal">
                <i class="fas fa-plus me-2"></i>Nuevo Mensaje
            </button>
        </div>
    </div>

    <!-- Overlay para chat -->
    <div class="chat-overlay" id="chat-overlay"></div>

    <!-- Modal de nuevo chat -->
    <div class="modal fade" id="newChatModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title font-weight-bold">Nuevo Mensaje</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Tabs -->
                    <ul class="nav nav-pills mb-3 nav-fill" id="searchTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active btn-sm small" id="direct-tab" data-bs-toggle="pill" data-bs-target="#direct-search" type="button">Búsqueda Directa</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link btn-sm small" id="subject-tab" data-bs-toggle="pill" data-bs-target="#subject-search" type="button">Por Materia</button>
                        </li>
                        @if(session('user_type') === 'profesor')
                        <li class="nav-item" role="presentation">
                            <button class="nav-link btn-sm small" id="group-tab" data-bs-toggle="pill" data-bs-target="#group-create" type="button">Crear Grupo</button>
                        </li>
                        @endif
                    </ul>

                    <div class="tab-content" id="searchTabContent">
                        <!-- Direct Search -->
                        <div class="tab-pane fade show active" id="direct-search" role="tabpanel">
                            <div class="mb-3">
                                <label class="form-label small text-muted">Filtrar por tipo:</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="roleFilter" id="roleAll" value="all" checked autocomplete="off">
                                    <label class="btn btn-outline-secondary btn-sm" for="roleAll">Todos</label>

                                    <input type="radio" class="btn-check" name="roleFilter" id="roleDocente" value="profesor" autocomplete="off">
                                    <label class="btn btn-outline-secondary btn-sm" for="roleDocente">Docentes</label>

                                    <input type="radio" class="btn-check" name="roleFilter" id="roleAlumno" value="estudiante" autocomplete="off">
                                    <label class="btn btn-outline-secondary btn-sm" for="roleAlumno">Alumnos</label>
                                </div>
                            </div>
                            <div class="position-relative">
                                 <input type="text" class="form-control" id="user-search-input" placeholder="Escribe un nombre...">
                                 <i class="fas fa-search position-absolute text-muted" style="right: 15px; top: 12px;"></i>
                            </div>
                        </div>

                        <!-- Subject Search -->
                        <div class="tab-pane fade" id="subject-search" role="tabpanel">
                             <div class="mb-3">
                                <label class="form-label small text-muted">Materia:</label>
                                <select class="form-select" id="materia-select">
                                    <option value="">Cargando materias...</option>
                                </select>
                            </div>
                             <div class="position-relative">
                                 <input type="text" class="form-control" id="materia-user-search" placeholder="Buscar alumno en esta materia..." disabled>
                            </div>
                        </div>

                        @if(session('user_type') === 'profesor')
                        <!-- Group Creation -->
                        <div class="tab-pane fade" id="group-create" role="tabpanel">
                            <form id="group-create-form">
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Materia:</label>
                                    <select class="form-select" id="group-materia-select" required>
                                        <option value="">-- Selecciona una Materia --</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Nombre del Grupo:</label>
                                    <input type="text" class="form-control" id="group-title" placeholder="Ej: Programación I - Grupo A" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Descripción (opcional):</label>
                                    <textarea class="form-control" id="group-description" rows="2" placeholder="Descripción del grupo..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-users me-2"></i>Crear Grupo con Todos los Alumnos
                                </button>
                            </form>
                            <div id="group-preview" class="mt-3 d-none">
                                <h6 class="small text-muted">VISTA PREVIA:</h6>
                                <div class="alert alert-info small">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <span id="group-preview-text"></span>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="mt-3">
                        <h6 class="small text-muted font-weight-bold">RESULTADOS</h6>
                        <div id="search-results" class="list-group list-group-flush border rounded overflow-auto" style="max-height: 250px; min-height: 50px;">
                            <p class="text-center text-muted small my-3">Empieza a escribir para buscar...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón de instalación PWA -->
    <button id="pwa-install-btn" style="display: none;">
        <i class="fas fa-download"></i> Instalar App
    </button>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/pwa-handler.js"></script>
    <script src="/js/responsive-tables.js"></script>
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

        // === CHAT SIDEBAR FUNCTIONALITY ===
        let chatSidebarOpen = false;
        let currentChatConversation = null;
        let chatActiveChannel = null;
        
        function toggleChatSidebar() {
            const chatSidebar = document.getElementById('chat-sidebar');
            const chatOverlay = document.getElementById('chat-overlay');
            
            if (!chatSidebar || !chatOverlay) return;
            
            if (chatSidebarOpen) {
                chatSidebar.classList.remove('show');
                chatOverlay.classList.remove('show');
                document.body.style.paddingRight = '';
                chatSidebarOpen = false;
            } else {
                chatSidebar.classList.add('show');
                chatOverlay.classList.add('show');
                document.body.style.paddingRight = '0';
                chatSidebarOpen = true;
                
                // Cargar conversaciones si no están cargadas
                loadChatConversations();
            }
        }
        
        function closeChatSidebar() {
            const chatSidebar = document.getElementById('chat-sidebar');
            const chatOverlay = document.getElementById('chat-overlay');
            
            if (chatSidebar && chatOverlay) {
                chatSidebar.classList.remove('show');
                chatOverlay.classList.remove('show');
                document.body.style.paddingRight = '';
                chatSidebarOpen = false;
            }
        }
        
        function loadChatConversations() {
            const conversationsContainer = document.getElementById('chat-conversations');
            if (!conversationsContainer) return;
            
            // Mostrar loading
            conversationsContainer.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2 text-muted">Cargando conversaciones...</p>
                </div>
            `;
            
            // Cargar conversaciones via AJAX
            fetch('/chat')
                .then(response => response.text())
                .then(html => {
                    // Extraer solo las conversaciones del HTML
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const conversations = doc.querySelectorAll('.conversation-item');
                    
                    if (conversations.length === 0) {
                        conversationsContainer.innerHTML = `
                            <div class="text-center py-4">
                                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No tienes conversaciones aún</p>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newChatModal">
                                    <i class="fas fa-plus me-1"></i>Iniciar Chat
                                </button>
                            </div>
                        `;
                        return;
                    }
                    
                    let conversationsHtml = '';
                    conversations.forEach(conv => {
                        const id = conv.dataset.id;
                        const title = conv.querySelector('h6').textContent.trim();
                        const lastMessage = conv.querySelector('.last-message').textContent.trim();
                        const time = conv.querySelector('small').textContent.trim();
                        
                        conversationsHtml += `
                            <div class="conversation-item-sidebar" data-id="${id}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="mb-1">${title}</h6>
                                    <small class="text-muted">${time}</small>
                                </div>
                                <p class="mb-0 text-truncate text-muted small">${lastMessage}</p>
                            </div>
                        `;
                    });
                    
                    conversationsContainer.innerHTML = conversationsHtml;
                    
                    // Agregar event listeners
                    document.querySelectorAll('.conversation-item-sidebar').forEach(item => {
                        item.addEventListener('click', function() {
                            const id = this.dataset.id;
                            openChatConversation(id);
                        });
                    });
                })
                .catch(error => {
                    console.error('Error loading conversations:', error);
                    conversationsContainer.innerHTML = `
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                            <p class="text-muted">Error al cargar conversaciones</p>
                            <button class="btn btn-outline-primary btn-sm" onclick="loadChatConversations()">
                                <i class="fas fa-redo me-1"></i>Reintentar
                            </button>
                        </div>
                    `;
                });
        }
        
        function openChatConversation(conversationId) {
            const conversationsDiv = document.getElementById('chat-conversations');
            const chatArea = document.getElementById('chat-area');
            
            if (!conversationsDiv || !chatArea) return;
            
            // Ocultar lista, mostrar chat
            conversationsDiv.classList.add('d-none');
            chatArea.classList.remove('d-none');
            
            currentChatConversation = conversationId;
            
            // Cargar mensajes
            loadChatMessages(conversationId);
        }
        
        function loadChatMessages(conversationId) {
            const messagesContainer = document.getElementById('chat-messages-sidebar');
            const chatTitle = document.getElementById('chat-title-sidebar');
            
            if (!messagesContainer || !chatTitle) return;
            
            // Mostrar loading
            messagesContainer.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando mensajes...</span>
                    </div>
                </div>
            `;
            
            // Cargar mensajes via AJAX
            fetch(`/chat/${conversationId}`)
                .then(response => response.json())
                .then(data => {
                    const conversation = data.conversation;
                    const messages = data.messages;
                    const currentUserId = data.current_user_id;
                    
                    // Actualizar título
                    updateChatTitleSidebar(conversation);
                    
                    // Mostrar mensajes
                    displayChatMessages(messages, currentUserId, conversation);
                    
                    // Configurar input
                    document.getElementById('current-conversation-id-sidebar').value = conversationId;
                    
                    // Scroll al final
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                })
                .catch(error => {
                    console.error('Error loading messages:', error);
                    messagesContainer.innerHTML = `
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                            <p class="text-muted mt-2">Error al cargar mensajes</p>
                        </div>
                    `;
                });
        }
        
        function updateChatTitleSidebar(conversation) {
            const chatTitle = document.getElementById('chat-title-sidebar');
            const recipientInfo = document.getElementById('recipient-info-sidebar');
            const recipientText = document.getElementById('recipient-text-sidebar');
            
            if (!chatTitle) return;
            
            if (conversation.type === 'private') {
                const otherParticipant = conversation.participants.find(p => 
                    p.participant_id != {{ session('user_id') }}
                );
                
                if (otherParticipant && otherParticipant.participant) {
                    const name = `${otherParticipant.participant.nombre} ${otherParticipant.participant.apellido}`;
                    chatTitle.textContent = name;
                    
                    if (recipientText) {
                        recipientText.textContent = `a ${name}`;
                        recipientInfo.classList.remove('d-none');
                    }
                }
            } else if (conversation.type === 'group') {
                chatTitle.textContent = conversation.title;
                
                if (recipientText) {
                    recipientText.textContent = `al grupo "${conversation.title}"`;
                    recipientInfo.classList.remove('d-none');
                }
            }
        }
        
        function displayChatMessages(messages, currentUserId, conversation) {
            const messagesContainer = document.getElementById('chat-messages-sidebar');
            if (!messagesContainer) return;
            
            let messagesHtml = '';
            
            messages.forEach(msg => {
                const isMe = msg.sender_id == currentUserId;
                const senderName = msg.sender ? `${msg.sender.nombre} ${msg.sender.apellido}` : 'Usuario';
                const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                
                messagesHtml += `
                    <div class="message-item ${isMe ? 'own' : ''}">
                        <div class="message-bubble ${isMe ? 'own' : 'other'}">
                            ${!isMe ? `<div class="message-sender">${senderName}</div>` : ''}
                            <div>${msg.content}</div>
                            <div class="message-time">${time}</div>
                        </div>
                    </div>
                `;
            });
            
            messagesContainer.innerHTML = messagesHtml;
        }
        
        function backToChatConversations() {
            const conversationsDiv = document.getElementById('chat-conversations');
            const chatArea = document.getElementById('chat-area');
            
            if (conversationsDiv && chatArea) {
                conversationsDiv.classList.remove('d-none');
                chatArea.classList.add('d-none');
                currentChatConversation = null;
            }
        }
        
        // Event listeners para chat
        document.addEventListener('DOMContentLoaded', function() {
            // Botón superior derecho
            const chatTopBtn = document.getElementById('chat-top-btn');
            if (chatTopBtn) {
                chatTopBtn.addEventListener('click', toggleChatSidebar);
            }
            
            // Cerrar chat
            const closeChatBtn = document.getElementById('close-chat-sidebar');
            if (closeChatBtn) {
                closeChatBtn.addEventListener('click', closeChatSidebar);
            }
            
            // Overlay
            const chatOverlay = document.getElementById('chat-overlay');
            if (chatOverlay) {
                chatOverlay.addEventListener('click', closeChatSidebar);
            }
            
            // Botón atrás en chat
            const backBtn = document.getElementById('back-to-conversations-sidebar');
            if (backBtn) {
                backBtn.addEventListener('click', backToChatConversations);
            }
            
            // Cerrar conversación
            const closeConvBtn = document.getElementById('close-chat-conversation');
            if (closeConvBtn) {
                closeConvBtn.addEventListener('click', backToChatConversations);
            }
            
            // Form de mensaje
            const messageForm = document.getElementById('message-form-sidebar');
            if (messageForm) {
                messageForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const input = document.getElementById('message-input-sidebar');
                    const conversationId = document.getElementById('current-conversation-id-sidebar').value;
                    
                    if (!input.value.trim() || !conversationId) return;
                    
                    // Enviar mensaje
                    fetch('/chat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            conversation_id: conversationId,
                            content: input.value.trim()
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Recargar mensajes
                        loadChatMessages(conversationId);
                        input.value = '';
                    })
                    .catch(error => {
                        console.error('Error sending message:', error);
                        alert('Error al enviar mensaje');
                    });
                });
            }
            
            // Cerrar con Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && chatSidebarOpen) {
                    if (currentChatConversation) {
                        backToChatConversations();
                    } else {
                        closeChatSidebar();
                    }
                }
            });
            
            // === MODAL DE NUEVO CHAT ===
            initializeNewChatModal();
        });
        
        function initializeNewChatModal() {
            const newChatModal = document.getElementById('newChatModal');
            const userSearchInput = document.getElementById('user-search-input');
            const materiaSearchInput = document.getElementById('materia-user-search');
            const searchResults = document.getElementById('search-results');
            const materiaSelect = document.getElementById('materia-select');
            let searchTimeout = null;

            if (!newChatModal) return;

            // Load options on modal open
            newChatModal.addEventListener('shown.bs.modal', function () {
                if (userSearchInput) userSearchInput.focus();
                loadMaterias();
                // Trigger initial search
                performSearch('', 'all', null);
                
                @if(session('user_type') === 'profesor')
                loadProfessorMaterias();
                @endif
            });

            function loadMaterias() {
                if (!materiaSelect || materiaSelect.options.length > 1) return; // Already loaded

                fetch('/chat/users/options')
                    .then(res => res.json())
                    .then(data => {
                        materiaSelect.innerHTML = '<option value="">-- Selecciona una Materia --</option>';
                        data.materias.forEach(m => {
                            materiaSelect.innerHTML += `<option value="${m.id}">${m.nombre} (${m.codigo})</option>`;
                        });
                    })
                    .catch(error => {
                        console.error('Error loading materias:', error);
                        materiaSelect.innerHTML = '<option value="">Error al cargar materias</option>';
                    });
            }

            // Direct Search Events
            if (userSearchInput) {
                userSearchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        const query = this.value;
                        const role = document.querySelector('input[name="roleFilter"]:checked')?.value || 'all';
                        performSearch(query, role, null);
                    }, 300);
                });
            }

            document.querySelectorAll('input[name="roleFilter"]').forEach(el => {
                el.addEventListener('change', function() {
                    const query = userSearchInput ? userSearchInput.value : '';
                    performSearch(query, this.value, null);
                });
            });

            // Subject Search Events
            if (materiaSelect) {
                materiaSelect.addEventListener('change', function() {
                    const materiaId = this.value;
                    if (materiaSearchInput) {
                        materiaSearchInput.disabled = !materiaId;
                    }
                    if(materiaId) {
                        performSearch('', 'estudiante', materiaId); // Fetch all students in course initially
                    } else {
                        if (searchResults) searchResults.innerHTML = '';
                    }
                });
            }

            if (materiaSearchInput) {
                materiaSearchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        const query = this.value;
                        const materiaId = materiaSelect ? materiaSelect.value : '';
                        if(materiaId) performSearch(query, 'estudiante', materiaId);
                    }, 300);
                });
            }

            function performSearch(query, role, materiaId) {
                if (!searchResults) return;
                
                searchResults.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div></div>';

                let url = `/chat/users/search?role=${role}`;
                if(query) url += `&query=${encodeURIComponent(query)}`;
                if(materiaId) url += `&materia=${materiaId}`;

                fetch(url)
                    .then(res => res.json())
                    .then(users => {
                        if(users.length === 0) {
                            searchResults.innerHTML = '<p class="text-center text-muted small my-3">No se encontraron usuarios.</p>';
                            return;
                        }

                        let html = '';
                        users.forEach(user => {
                            html += `
                                <button class="list-group-item list-group-item-action d-flex align-items-center mb-1 border-0 user-select-item" 
                                    data-id="${user.id}" data-type="${user.type}">
                                    <div class="bg-secondary rounded-circle text-white d-flex align-items-center justify-content-center me-3" 
                                         style="width: 40px; height: 40px; font-weight: bold;">
                                        ${user.initials}
                                    </div>
                                    <div class="flex-grow-1 text-start">
                                        <h6 class="mb-0 text-truncate text-dark" style="max-width: 250px;">${user.name}</h6>
                                        <small class="text-muted">${user.role_label}</small>
                                    </div>
                                    <i class="fas fa-comment text-primary opacity-0 icon-hover"></i>
                                </button>
                            `;
                        });
                        searchResults.innerHTML = html;

                        // Add click listeners to new items
                        document.querySelectorAll('.user-select-item').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const recipientId = this.dataset.id;
                                const recipientType = this.dataset.type;
                                startConversation(recipientId, recipientType);
                            });
                        });
                    })
                    .catch(error => {
                        console.error('Error searching users:', error);
                        searchResults.innerHTML = '<p class="text-center text-muted small my-3">Error al buscar usuarios.</p>';
                    });
            }

            function startConversation(recipientId, recipientType) {
                fetch('/chat/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        recipient_id: recipientId,
                        recipient_type: recipientType
                    })
                }).then(response => response.json())
                .then(conv => {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(newChatModal);
                    if (modal) modal.hide();
                    
                    // Recargar conversaciones y abrir la nueva
                    loadChatConversations();
                    setTimeout(() => {
                        openChatConversation(conv.id);
                    }, 500);
                }).catch(err => {
                    console.error(err);
                    alert('Error al iniciar conversación');
                });
            }

            @if(session('user_type') === 'profesor')
            // Funcionalidad para crear grupos (solo profesores)
            const groupForm = document.getElementById('group-create-form');
            const groupMateriaSelect = document.getElementById('group-materia-select');
            const groupPreview = document.getElementById('group-preview');
            const groupPreviewText = document.getElementById('group-preview-text');

            // Cargar materias del profesor en el selector de grupos
            function loadProfessorMaterias() {
                if (!groupMateriaSelect) return;
                
                fetch('/chat/users/options')
                    .then(res => res.json())
                    .then(data => {
                        groupMateriaSelect.innerHTML = '<option value="">-- Selecciona una Materia --</option>';
                        data.materias.forEach(m => {
                            groupMateriaSelect.innerHTML += `<option value="${m.id}">${m.nombre} (${m.codigo})</option>`;
                        });
                    })
                    .catch(error => {
                        console.error('Error loading professor materias:', error);
                        groupMateriaSelect.innerHTML = '<option value="">Error al cargar materias</option>';
                    });
            }

            // Mostrar vista previa del grupo
            if (groupMateriaSelect && groupPreview && groupPreviewText) {
                groupMateriaSelect.addEventListener('change', function() {
                    if (this.value) {
                        const selectedOption = this.options[this.selectedIndex];
                        const materiaName = selectedOption.text;
                        
                        // Obtener número de estudiantes en la materia
                        fetch(`/chat/users/search?materia=${this.value}&role=estudiante`)
                            .then(res => res.json())
                            .then(students => {
                                groupPreviewText.textContent = `Se creará un grupo para "${materiaName}" con ${students.length} estudiantes inscritos.`;
                                groupPreview.classList.remove('d-none');
                            })
                            .catch(error => {
                                console.error('Error loading students preview:', error);
                                groupPreview.classList.add('d-none');
                            });
                    } else {
                        groupPreview.classList.add('d-none');
                    }
                });
            }

            // Manejar creación de grupo
            if (groupForm) {
                groupForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const materiaId = groupMateriaSelect ? groupMateriaSelect.value : '';
                    const title = document.getElementById('group-title')?.value || '';
                    const description = document.getElementById('group-description')?.value || '';
                    
                    if (!materiaId || !title) {
                        alert('Por favor completa los campos requeridos');
                        return;
                    }
                    
                    // Mostrar loading
                    const submitBtn = groupForm.querySelector('button[type="submit"]');
                    const originalText = submitBtn ? submitBtn.innerHTML : '';
                    if (submitBtn) {
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creando Grupo...';
                        submitBtn.disabled = true;
                    }
                    
                    fetch('/chat/create-group', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            materia_id: materiaId,
                            title: title,
                            description: description
                        })
                    }).then(response => response.json())
                    .then(result => {
                        alert(`¡Grupo creado exitosamente! Se agregaron ${result.students_added} estudiantes.`);
                        
                        // Cerrar modal y recargar
                        const modal = bootstrap.Modal.getInstance(newChatModal);
                        if (modal) modal.hide();
                        loadChatConversations();
                        
                    }).catch(err => {
                        console.error(err);
                        alert('Error al crear el grupo: ' + (err.response?.data?.error || 'Error desconocido'));
                    }).finally(() => {
                        if (submitBtn) {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }
                    });
                });
            }
            @endif
        }
        
        // === SISTEMA DE ESTADO EN LÍNEA ===
        function updateOnlineStatus(status = 'online') {
            fetch('/chat/status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status: status })
            }).catch(err => console.error('Error updating status:', err));
        }

        // Actualizar estado cada 30 segundos
        setInterval(() => {
            updateOnlineStatus();
        }, 30000);

        // Actualizar estado al cargar la página
        updateOnlineStatus();

        // Actualizar estado cuando la ventana pierde/gana foco
        window.addEventListener('focus', () => updateOnlineStatus('online'));
        window.addEventListener('blur', () => updateOnlineStatus('away'));

        // Actualizar estado antes de cerrar la página
        window.addEventListener('beforeunload', () => {
            navigator.sendBeacon('/chat/status', JSON.stringify({ status: 'offline' }));
        });
        
        // === NOTIFICACIONES DE CHAT ===
        function updateChatNotifications() {
            // Aquí se puede implementar la lógica para mostrar notificaciones
            // Por ahora, solo actualizamos el badge si hay mensajes no leídos
            const topDot = document.getElementById('chat-top-notification');
            
            // Esta función se puede expandir para obtener el conteo real de mensajes no leídos
            // fetch('/chat/unread-count').then(...)
        }
        
        // Actualizar notificaciones periódicamente
        setInterval(updateChatNotifications, 60000); // Cada minuto
    </script>
    
    @yield('scripts')
</body>
</html>