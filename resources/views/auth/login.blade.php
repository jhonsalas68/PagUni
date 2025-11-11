@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="login-container d-flex align-items-center justify-content-center">
    <div class="container">
        <!-- Logo Universidad -->
        <div class="text-center mb-4">
            <div class="university-header">
                <h2 class="text-white mb-2">Universidad Autónoma Gabriel René Moreno</h2>
                <h5 class="text-white opacity-75">Sistema de Gestión Académica</h5>
            </div>
        </div>

        <!-- Título Principal -->
        <div class="text-center mb-4">
            <h3 class="welcome-title">Bienvenido al Perfil</h3>
            <h5 class="select-title">Elige tu tipo de cuenta</h5>
        </div>

        <!-- Tarjetas de Selección -->
        <div class="row justify-content-center mb-4">
            <div class="col-md-10">
                <div class="row g-4">
                    <!-- Estudiante -->
                    <div class="col-md-4">
                        <div class="user-type-card" data-type="estudiante">
                            <div class="card-icon">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <h5 class="card-title">Estudiante</h5>
                            <p class="card-description">Acceso para estudiantes</p>
                        </div>
                    </div>

                    <!-- Docente -->
                    <div class="col-md-4">
                        <div class="user-type-card" data-type="profesor">
                            <div class="card-icon">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <h5 class="card-title">Docente</h5>
                            <p class="card-description">Acceso para docentes</p>
                        </div>
                    </div>

                    <!-- Administrador -->
                    <div class="col-md-4">
                        <div class="user-type-card" data-type="administrador">
                            <div class="card-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h5 class="card-title">Administrador</h5>
                            <p class="card-description">Acceso administrativo</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de Login -->
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="login-card" id="loginForm" style="display: none;">
                    <div class="login-header text-center p-4">
                        <div class="selected-icon mb-3">
                            <i class="fas fa-user" id="selectedIcon"></i>
                        </div>
                        <h4 class="mb-0" id="selectedTitle">Iniciar Sesión</h4>
                        <p class="mb-0 mt-2 opacity-75" id="selectedSubtitle"></p>
                    </div>
                    
                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ $errors->first() }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            
                            <input type="hidden" id="userType" name="user_type" value="">
                            
                            <div class="mb-3">
                                <label for="codigo" class="form-label">
                                    <i class="fas fa-user me-2"></i>Código de Usuario
                                </label>
                                <input type="text" 
                                       class="form-control @error('codigo') is-invalid @enderror" 
                                       id="codigo" 
                                       name="codigo" 
                                       value="{{ old('codigo') }}"
                                       placeholder="Ingrese su código"
                                       required>
                                <small class="form-text text-muted" id="codigoHelp"></small>
                                @error('codigo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Contraseña
                                </label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Ingrese su contraseña"
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-uagrm">
                                    <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="btnBack">
                                    <i class="fas fa-arrow-left me-2"></i>Volver
                                </button>
                            </div>
                        </form>

                        <div class="mt-3 text-center">
                            <a href="#" class="text-muted small">
                                <i class="fas fa-question-circle me-1"></i>¿Olvidaste tu contraseña?
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .university-header h2 {
        font-size: 1.8rem;
        font-weight: 600;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .university-header h5 {
        font-size: 1.1rem;
    }

    .welcome-title {
        color: #dc3545;
        font-weight: 700;
        font-size: 2.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    }

    .select-title {
        color: #0d6efd;
        font-weight: 600;
        font-size: 1.5rem;
    }

    .user-type-card {
        background: white;
        border-radius: 15px;
        padding: 2rem 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        border: 3px solid transparent;
        height: 100%;
    }

    .user-type-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        border-color: #0d6efd;
    }

    .user-type-card.active {
        border-color: #dc3545;
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(13, 110, 253, 0.1) 100%);
    }

    .card-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        color: #0d6efd;
        transition: all 0.3s ease;
    }

    .user-type-card:hover .card-icon {
        color: #dc3545;
        transform: scale(1.1);
    }

    .card-title {
        font-weight: 700;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .card-description {
        color: #666;
        font-size: 0.9rem;
        margin: 0;
    }

    .selected-icon {
        font-size: 3rem;
        color: white;
    }

    .login-card {
        animation: fadeInUp 0.5s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .welcome-title {
            font-size: 1.8rem;
        }
        
        .select-title {
            font-size: 1.2rem;
        }

        .user-type-card {
            padding: 1.5rem 1rem;
        }

        .card-icon {
            font-size: 3rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userTypeCards = document.querySelectorAll('.user-type-card');
        const loginForm = document.getElementById('loginForm');
        const btnBack = document.getElementById('btnBack');
        const selectedIcon = document.getElementById('selectedIcon');
        const selectedTitle = document.getElementById('selectedTitle');
        const selectedSubtitle = document.getElementById('selectedSubtitle');
        const codigoHelp = document.getElementById('codigoHelp');
        const codigoInput = document.getElementById('codigo');

        const userTypeConfig = {
            estudiante: {
                icon: 'fa-user-graduate',
                title: 'Acceso Estudiante',
                subtitle: 'Ingrese sus credenciales de estudiante',
                placeholder: 'Ej: ISC2024001, MATE2024001',
                help: 'Formato: [CARRERA][AÑO][NÚMERO]'
            },
            profesor: {
                icon: 'fa-chalkboard-teacher',
                title: 'Acceso Docente',
                subtitle: 'Ingrese sus credenciales de docente',
                placeholder: 'Ej: PROF001, PROF123',
                help: 'Formato: PROF###'
            },
            administrador: {
                icon: 'fa-user-tie',
                title: 'Acceso Administrador',
                subtitle: 'Ingrese sus credenciales administrativas',
                placeholder: 'Ej: ADM001, ADM123',
                help: 'Formato: ADM###'
            }
        };

        userTypeCards.forEach(card => {
            card.addEventListener('click', function() {
                const type = this.dataset.type;
                const config = userTypeConfig[type];

                // Actualizar UI
                selectedIcon.className = 'fas ' + config.icon;
                selectedTitle.textContent = config.title;
                selectedSubtitle.textContent = config.subtitle;
                codigoInput.placeholder = config.placeholder;
                codigoHelp.textContent = config.help;

                // Mostrar formulario
                loginForm.style.display = 'block';
                loginForm.scrollIntoView({ behavior: 'smooth', block: 'center' });

                // Marcar tarjeta activa
                userTypeCards.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
            });
        });

        btnBack.addEventListener('click', function() {
            loginForm.style.display = 'none';
            userTypeCards.forEach(c => c.classList.remove('active'));
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
</script>
@endsection
