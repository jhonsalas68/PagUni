@extends('layouts.dashboard')

@section('title', 'Editar Periodo de Inscripción')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>✏️ Editar Periodo de Inscripción</h2>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.periodos-inscripcion.update', $periodo->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Periodo *</label>
                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                           id="nombre" name="nombre" value="{{ old('nombre', $periodo->nombre) }}" required>
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="periodo_academico" class="form-label">Periodo Académico *</label>
                    <input type="text" class="form-control @error('periodo_academico') is-invalid @enderror" 
                           id="periodo_academico" name="periodo_academico" 
                           value="{{ old('periodo_academico', $periodo->periodo_academico) }}" 
                           placeholder="Ej: 2024-2" required>
                    @error('periodo_academico')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha de Inicio *</label>
                        <input type="datetime-local" class="form-control @error('fecha_inicio') is-invalid @enderror" 
                               id="fecha_inicio" name="fecha_inicio" 
                               value="{{ old('fecha_inicio', $periodo->fecha_inicio->format('Y-m-d\TH:i')) }}" required>
                        @error('fecha_inicio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="fecha_fin" class="form-label">Fecha de Fin *</label>
                        <input type="datetime-local" class="form-control @error('fecha_fin') is-invalid @enderror" 
                               id="fecha_fin" name="fecha_fin" 
                               value="{{ old('fecha_fin', $periodo->fecha_fin->format('Y-m-d\TH:i')) }}" required>
                        @error('fecha_fin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                              id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $periodo->descripcion) }}</textarea>
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">💾 Actualizar</button>
                    <a href="{{ route('admin.periodos-inscripcion.index') }}" class="btn btn-secondary">
                        ❌ Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
