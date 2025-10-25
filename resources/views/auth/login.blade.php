@extends('layouts.app')

@section('title', 'Iniciar Sesión - UAGRM')

@section('content')
<div class="login-container d-flex align-items-center justify-content-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card login-card">
                    <div class="card-header login-header text-center py-4">
                        <div class="university-logo mb-3">
                            <div class="logo-uagrm">
                                <div class="logo-top">UA</div>
                                <div class="logo-bottom">GRM</div>
                            </div>
                        </div>
                        <h4 class="mb-1">Sistema Académico</h4>
                        <p class="mb-0 small">Universidad Autónoma Gabriel René Moreno</p>
                    </div>
                    <div class="card-body p-4">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ $errors->first() }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="codigo" class="form-label fw-bold">
                                    Usuario
                                </label>
                                <input type="text" 
                                       class="form-control @error('codigo') is-invalid @enderror" 
                                       id="codigo" 
                                       name="codigo" 
                                       value="{{ old('codigo') }}" 
                                       placeholder="Ingrese su código de usuario"
                                       required>
                                @error('codigo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label fw-bold">
                                    Contraseña
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

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-uagrm btn-lg">
                                    Ingresar
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center">
                            <small class="text-muted">
                                <a href="#" class="text-decoration-none">¿Olvidó su contraseña?</a>
                            </small>
                        </div>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                Ejemplos de códigos:<br>
                                <strong>ADM001</strong> (Admin) • <strong>PROF001</strong> (Profesor) • <strong>ISC2024001</strong> (Estudiante)
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection