@extends('layouts.dashboard')

@section('title', 'Crear Periodo de Inscripción')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>➕ Crear Periodo de Inscripción</h2>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.periodos-inscripcion.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Periodo *</label>
                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                           id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="periodo_academico" class="form-label">Periodo Académico *</label>
                    <input type="text" class="form-control @error('periodo_academico') is-invalid @enderror" 
                           id="periodo_academico" name="periodo_academico" 
                           value="{{ old('periodo_academico') }}" 
                           placeholder="Ej: 2024-2" required>
                    @error('periodo_academico')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha de Inicio *</label>
                        <input type="datetime-local" class="form-control @error('fecha_inicio') is-invalid @enderror" 
                               id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio') }}" required>
                        @error('fecha_inicio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="fecha_fin" class="form-label">Fecha de Fin *</label>
                        <input type="datetime-local" class="form-control @error('fecha_fin') is-invalid @enderror" 
                               id="fecha_fin" name="fecha_fin" value="{{ old('fecha_fin') }}" required>
                        @error('fecha_fin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                              id="descripcion" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1" 
                           {{ old('activo') ? 'checked' : '' }}>
                    <label class="form-check-label" for="activo">
                        Activar inmediatamente
                    </label>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">💾 Guardar</button>
                    <a href="{{ route('admin.periodos-inscripcion.index') }}" class="btn btn-secondary">
                        ❌ Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
