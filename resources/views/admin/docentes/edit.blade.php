@extends('layouts.dashboard')

@section('title', 'Modificar Datos de Docente')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Modificar Datos de Docente</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.docentes.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Lista
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Editando: {{ $docente->nombre_completo }} 
                        <span class="badge bg-{{ $docente->estado == 'activo' ? 'success' : 'danger' }}">
                            {{ ucfirst($docente->estado) }}
                        </span>
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.docentes.update', $docente) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="codigo_docente" class="form-label">
                                    <i class="fas fa-id-card"></i> Código Único de Docente *
                                </label>
                                <input type="text" 
                                       class="form-control @error('codigo_docente') is-invalid @enderror" 
                                       id="codigo_docente" 
                                       name="codigo_docente" 
                                       value="{{ old('codigo_docente', $docente->codigo_docente) }}"
                                       required>
                                @error('codigo_docente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Este código debe ser único en el sistema</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i> Correo Electrónico *
                                </label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $docente->email) }}"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">
                                    <i class="fas fa-user"></i> Nombre *
                                </label>
                                <input type="text" 
                                       class="form-control @error('nombre') is-invalid @enderror" 
                                       id="nombre" 
                                       name="nombre" 
                                       value="{{ old('nombre', $docente->nombre) }}"
                                       required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="apellido" class="form-label">
                                    <i class="fas fa-user"></i> Apellido *
                                </label>
                                <input type="text" 
                                       class="form-control @error('apellido') is-invalid @enderror" 
                                       id="apellido" 
                                       name="apellido" 
                                       value="{{ old('apellido', $docente->apellido) }}"
                                       required>
                                @error('apellido')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cedula" class="form-label">
                                    <i class="fas fa-id-card-alt"></i> Cédula *
                                </label>
                                <input type="text" 
                                       class="form-control @error('cedula') is-invalid @enderror" 
                                       id="cedula" 
                                       name="cedula" 
                                       value="{{ old('cedula', $docente->cedula) }}"
                                       required>
                                @error('cedula')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label">
                                    <i class="fas fa-phone"></i> Teléfono
                                </label>
                                <input type="text" 
                                       class="form-control @error('telefono') is-invalid @enderror" 
                                       id="telefono" 
                                       name="telefono" 
                                       value="{{ old('telefono', $docente->telefono) }}">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="especialidad" class="form-label">
                                    <i class="fas fa-graduation-cap"></i> Especialidad *
                                </label>
                                <input type="text" 
                                       class="form-control @error('especialidad') is-invalid @enderror" 
                                       id="especialidad" 
                                       name="especialidad" 
                                       value="{{ old('especialidad', $docente->especialidad) }}"
                                       required>
                                @error('especialidad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tipo_contrato" class="form-label">
                                    <i class="fas fa-briefcase"></i> Tipo de Contrato *
                                </label>
                                <select class="form-select @error('tipo_contrato') is-invalid @enderror" 
                                        id="tipo_contrato" 
                                        name="tipo_contrato" 
                                        required>
                                    <option value="">Seleccione un tipo</option>
                                    <option value="tiempo_completo" {{ old('tipo_contrato', $docente->tipo_contrato) == 'tiempo_completo' ? 'selected' : '' }}>
                                        Tiempo Completo
                                    </option>
                                    <option value="medio_tiempo" {{ old('tipo_contrato', $docente->tipo_contrato) == 'medio_tiempo' ? 'selected' : '' }}>
                                        Medio Tiempo
                                    </option>
                                    <option value="catedra" {{ old('tipo_contrato', $docente->tipo_contrato) == 'catedra' ? 'selected' : '' }}>
                                        Cátedra
                                    </option>
                                </select>
                                @error('tipo_contrato')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i> Nueva Contraseña
                            </label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Dejar en blanco para mantener la contraseña actual. Mínimo 6 caracteres si se cambia.</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.docentes.index') }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection