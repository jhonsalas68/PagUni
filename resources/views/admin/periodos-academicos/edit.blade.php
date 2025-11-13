@extends('layouts.dashboard')

@section('title', 'Editar Periodo Académico')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <h1 class="h2 mb-0">
                <i class="fas fa-edit"></i> Editar Periodo: {{ $periodoAcademico->codigo }}
            </h1>
            <a href="{{ route('admin.periodos-academicos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>

<div class="card shadow">
    <div class="card-header">
        <h6 class="m-0 fw-bold text-primary">Información del Periodo</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.periodos-academicos.update', $periodoAcademico) }}">
            @csrf
            @method('PUT')

            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Nota:</strong> El código, año y semestre no se pueden modificar una vez creado el periodo.
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Código</label>
                        <input type="text" class="form-control" value="{{ $periodoAcademico->codigo }}" disabled>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Año</label>
                        <input type="text" class="form-control" value="{{ $periodoAcademico->anio }}" disabled>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Semestre</label>
                        <input type="text" class="form-control" 
                               value="{{ $periodoAcademico->semestre == 1 ? 'Primer Semestre' : 'Segundo Semestre' }}" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror" 
                               id="fecha_inicio" name="fecha_inicio" 
                               value="{{ old('fecha_inicio', $periodoAcademico->fecha_inicio->format('Y-m-d')) }}" required>
                        @error('fecha_inicio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fecha_fin" class="form-label">Fecha de Fin <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('fecha_fin') is-invalid @enderror" 
                               id="fecha_fin" name="fecha_fin" 
                               value="{{ old('fecha_fin', $periodoAcademico->fecha_fin->format('Y-m-d')) }}" required>
                        @error('fecha_fin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                        <select class="form-select @error('estado') is-invalid @enderror" 
                                id="estado" name="estado" required>
                            <option value="activo" {{ old('estado', $periodoAcademico->estado) == 'activo' ? 'selected' : '' }}>
                                Activo
                            </option>
                            <option value="inactivo" {{ old('estado', $periodoAcademico->estado) == 'inactivo' ? 'selected' : '' }}>
                                Inactivo
                            </option>
                            <option value="finalizado" {{ old('estado', $periodoAcademico->estado) == 'finalizado' ? 'selected' : '' }}>
                                Finalizado
                            </option>
                        </select>
                        @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Periodo Actual</label>
                        <div class="form-control" style="background-color: #f8f9fa;">
                            @if($periodoAcademico->es_actual)
                                <span class="badge bg-success">
                                    <i class="fas fa-check"></i> Este es el periodo actual
                                </span>
                            @else
                                <span class="text-muted">No es el periodo actual</span>
                            @endif
                        </div>
                        <small class="text-muted">Para cambiar, use el botón "Marcar como actual" en el listado</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                  id="observaciones" name="observaciones" rows="3" 
                                  maxlength="500">{{ old('observaciones', $periodoAcademico->observaciones) }}</textarea>
                        @error('observaciones')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.periodos-academicos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar Periodo
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
