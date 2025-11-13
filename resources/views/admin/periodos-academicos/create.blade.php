@extends('layouts.dashboard')

@section('title', 'Nuevo Periodo Académico')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <h1 class="h2 mb-0">
                <i class="fas fa-calendar-plus"></i> Nuevo Periodo Académico
            </h1>
            <a href="{{ route('admin.periodos-academicos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>

@if($errors->has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ $errors->first('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow">
    <div class="card-header">
        <h6 class="m-0 fw-bold text-primary">Información del Periodo</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.periodos-academicos.store') }}">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="anio" class="form-label">Año <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('anio') is-invalid @enderror" 
                               id="anio" name="anio" value="{{ old('anio', date('Y')) }}" 
                               min="2020" max="2050" required>
                        @error('anio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Año del periodo académico</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="semestre" class="form-label">Semestre <span class="text-danger">*</span></label>
                        <select class="form-select @error('semestre') is-invalid @enderror" 
                                id="semestre" name="semestre" required>
                            <option value="">Seleccionar...</option>
                            <option value="1" {{ old('semestre') == 1 ? 'selected' : '' }}>Primer Semestre</option>
                            <option value="2" {{ old('semestre') == 2 ? 'selected' : '' }}>Segundo Semestre</option>
                        </select>
                        @error('semestre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Semestre del año académico</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror" 
                               id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio') }}" required>
                        @error('fecha_inicio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fecha_fin" class="form-label">Fecha de Fin <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('fecha_fin') is-invalid @enderror" 
                               id="fecha_fin" name="fecha_fin" value="{{ old('fecha_fin') }}" required>
                        @error('fecha_fin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                  id="observaciones" name="observaciones" rows="3" 
                                  maxlength="500">{{ old('observaciones') }}</textarea>
                        @error('observaciones')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Información adicional sobre el periodo (opcional)</small>
                    </div>
                </div>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Nota:</strong> El código del periodo se generará automáticamente como <strong id="codigo-preview">YYYY-S</strong>
                <br>
                <small>Ejemplo: 2024-1 (Primer Semestre 2024), 2024-2 (Segundo Semestre 2024)</small>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.periodos-academicos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Periodo
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const anioInput = document.getElementById('anio');
    const semestreSelect = document.getElementById('semestre');
    const codigoPreview = document.getElementById('codigo-preview');

    function actualizarPreview() {
        const anio = anioInput.value || 'YYYY';
        const semestre = semestreSelect.value || 'S';
        codigoPreview.textContent = anio + '-' + semestre;
    }

    anioInput.addEventListener('input', actualizarPreview);
    semestreSelect.addEventListener('change', actualizarPreview);
    
    actualizarPreview();
});
</script>
@endsection
