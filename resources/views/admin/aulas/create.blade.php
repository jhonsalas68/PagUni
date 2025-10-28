@extends('layouts.dashboard')

@section('title', 'Nueva Aula')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Nueva Aula</h1>
            <a href="{{ route('admin.aulas.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información del Aula</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.aulas.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="codigo_aula" class="form-label">Código del Aula <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('codigo_aula') is-invalid @enderror" 
                                       id="codigo_aula" name="codigo_aula" value="{{ old('codigo_aula') }}" 
                                       placeholder="Ej: A101, LAB-B205" required>
                                @error('codigo_aula')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre del Aula <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                       id="nombre" name="nombre" value="{{ old('nombre') }}" 
                                       placeholder="Ej: Aula Magna, Laboratorio de Computación" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="capacidad" class="form-label">Capacidad <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('capacidad') is-invalid @enderror" 
                                       id="capacidad" name="capacidad" value="{{ old('capacidad') }}" 
                                       min="1" max="500" required>
                                @error('capacidad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="tipo_aula" class="form-label">Tipo de Aula <span class="text-danger">*</span></label>
                                <select class="form-select @error('tipo_aula') is-invalid @enderror" 
                                        id="tipo_aula" name="tipo_aula" required>
                                    <option value="">Seleccionar tipo...</option>
                                    <option value="aula" {{ old('tipo_aula') == 'aula' ? 'selected' : '' }}>Aula Regular</option>
                                    <option value="laboratorio" {{ old('tipo_aula') == 'laboratorio' ? 'selected' : '' }}>Laboratorio</option>
                                    <option value="auditorio" {{ old('tipo_aula') == 'auditorio' ? 'selected' : '' }}>Auditorio</option>
                                    <option value="sala_conferencias" {{ old('tipo_aula') == 'sala_conferencias' ? 'selected' : '' }}>Sala de Conferencias</option>
                                    <option value="biblioteca" {{ old('tipo_aula') == 'biblioteca' ? 'selected' : '' }}>Biblioteca</option>
                                </select>
                                @error('tipo_aula')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="edificio" class="form-label">Edificio <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('edificio') is-invalid @enderror" 
                                       id="edificio" name="edificio" value="{{ old('edificio') }}" 
                                       placeholder="Ej: Edificio A" required>
                                @error('edificio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="piso" class="form-label">Piso <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('piso') is-invalid @enderror" 
                                       id="piso" name="piso" value="{{ old('piso') }}" 
                                       min="1" max="20" required>
                                @error('piso')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                  id="descripcion" name="descripcion" rows="3" 
                                  placeholder="Descripción adicional del aula, equipamiento, etc.">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                        <select class="form-select @error('estado') is-invalid @enderror" 
                                id="estado" name="estado" required>
                            <option value="disponible" {{ old('estado', 'disponible') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                            <option value="ocupada" {{ old('estado') == 'ocupada' ? 'selected' : '' }}>Ocupada</option>
                            <option value="mantenimiento" {{ old('estado') == 'mantenimiento' ? 'selected' : '' }}>En Mantenimiento</option>
                            <option value="fuera_servicio" {{ old('estado') == 'fuera_servicio' ? 'selected' : '' }}>Fuera de Servicio</option>
                        </select>
                        @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="tiene_proyector" 
                                       name="tiene_proyector" value="1" {{ old('tiene_proyector') ? 'checked' : '' }}>
                                <label class="form-check-label" for="tiene_proyector">
                                    Tiene Proyector
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="tiene_computadoras" 
                                       name="tiene_computadoras" value="1" {{ old('tiene_computadoras') ? 'checked' : '' }}>
                                <label class="form-check-label" for="tiene_computadoras">
                                    Tiene Computadoras
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="tiene_aire_acondicionado" 
                                       name="tiene_aire_acondicionado" value="1" {{ old('tiene_aire_acondicionado') ? 'checked' : '' }}>
                                <label class="form-check-label" for="tiene_aire_acondicionado">
                                    Aire Acondicionado
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="acceso_discapacitados" 
                                       name="acceso_discapacitados" value="1" {{ old('acceso_discapacitados') ? 'checked' : '' }}>
                                <label class="form-check-label" for="acceso_discapacitados">
                                    Acceso para Discapacitados
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="{{ route('admin.aulas.index') }}" class="btn btn-secondary me-md-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Crear Aula
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Información</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Consejos:</strong>
                    <ul class="mb-0 mt-2">
                        <li>El código del aula debe ser único</li>
                        <li>Usa códigos descriptivos (A101, LAB-B205)</li>
                        <li>La capacidad debe ser realista</li>
                        <li>Especifica el tipo correcto para filtros</li>
                    </ul>
                </div>
                
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Importante:</strong>
                    Una vez creada, el aula estará disponible para asignar horarios inmediatamente.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection