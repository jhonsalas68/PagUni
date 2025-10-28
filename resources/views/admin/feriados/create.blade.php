@extends('layouts.dashboard')

@section('title', 'Registrar Nuevo Feriado')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <h3 class="card-title mb-2 mb-md-0">
                        <i class="fas fa-calendar-plus me-2"></i>
                        <span class="d-none d-sm-inline">Registrar Nuevo Día No Laborable/Feriado</span>
                        <span class="d-sm-none">Nuevo Feriado</span>
                    </h3>
                    <a href="{{ route('admin.feriados.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>
                        <span class="d-none d-sm-inline">Volver a la Lista</span>
                        <span class="d-sm-none">Volver</span>
                    </a>
                </div>
            </div>

                <form method="POST" action="{{ route('admin.feriados.store') }}">
                    @csrf
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <h5><i class="icon fas fa-ban"></i> Error!</h5>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="fecha_inicio" class="form-label">
                                    Fecha de Inicio <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('fecha_inicio') is-invalid @enderror" 
                                       id="fecha_inicio" 
                                       name="fecha_inicio" 
                                       value="{{ old('fecha_inicio') }}" 
                                       min="{{ date('Y-m-d') }}"
                                       required>
                                @error('fecha_inicio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Seleccione la fecha de inicio del feriado
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="fecha_fin" class="form-label">
                                    Fecha de Fin <small class="text-muted">(Opcional)</small>
                                </label>
                                <input type="date" 
                                       class="form-control @error('fecha_fin') is-invalid @enderror" 
                                       id="fecha_fin" 
                                       name="fecha_fin" 
                                       value="{{ old('fecha_fin') }}">
                                @error('fecha_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Deje vacío para un día específico, o seleccione para un rango de fechas
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-12 col-lg-8">
                                <label for="descripcion" class="form-label">
                                    Descripción del Evento <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('descripcion') is-invalid @enderror" 
                                       id="descripcion" 
                                       name="descripcion" 
                                       value="{{ old('descripcion') }}" 
                                       maxlength="255"
                                       placeholder="Ej: Navidad, Receso de Invierno, Día del Trabajo..."
                                       required>
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Máximo 255 caracteres
                                </div>
                            </div>

                            <div class="col-12 col-lg-4">
                                <label for="tipo" class="form-label">
                                    Tipo de Día No Laborable <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('tipo') is-invalid @enderror" 
                                        id="tipo" 
                                        name="tipo" 
                                        required>
                                    <option value="">Seleccione un tipo</option>
                                    <option value="feriado" {{ old('tipo') == 'feriado' ? 'selected' : '' }}>
                                        Feriado
                                    </option>
                                    <option value="receso" {{ old('tipo') == 'receso' ? 'selected' : '' }}>
                                        Receso
                                    </option>
                                    <option value="asueto" {{ old('tipo') == 'asueto' ? 'selected' : '' }}>
                                        Asueto
                                    </option>
                                </select>
                                @error('tipo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Información adicional -->
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h5><i class="icon fas fa-info"></i> Información Importante:</h5>
                                    <ul class="mb-0">
                                        <li>Las fechas registradas quedarán marcadas como no lectivas</li>
                                        <li>No se programarán clases automáticamente en estos días</li>
                                        <li>No se esperará ni computará asistencia en días no laborables</li>
                                        <li>El sistema validará que no se superpongan con feriados existentes</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Vista previa de validación -->
                        <div id="validacion-preview" class="alert alert-warning" style="display: none;">
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Validando fechas...</h5>
                            <div id="validacion-content"></div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                            <div class="btn-group-responsive mb-2 mb-md-0">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i>
                                    Registrar Feriado
                                </button>
                                <a href="{{ route('admin.feriados.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i>
                                    Cancelar
                                </a>
                            </div>
                            <small class="text-muted">
                                <span class="text-danger">*</span> Campos obligatorios
                            </small>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    const descripcion = document.getElementById('descripcion');
    const validacionPreview = document.getElementById('validacion-preview');
    const validacionContent = document.getElementById('validacion-content');
    
    // Validar fechas en tiempo real
    if (fechaInicio) {
        fechaInicio.addEventListener('change', validarFechas);
    }
    if (fechaFin) {
        fechaFin.addEventListener('change', validarFechas);
    }

    function validarFechas() {
        const fechaInicioVal = fechaInicio.value;
        const fechaFinVal = fechaFin.value;
        
        if (!fechaInicioVal) return;
        
        // Validar que fecha fin sea posterior a fecha inicio
        if (fechaFinVal && fechaFinVal <= fechaInicioVal) {
            if (validacionPreview) {
                validacionPreview.style.display = 'block';
                validacionPreview.className = 'alert alert-danger';
                validacionContent.innerHTML = '<strong>Error:</strong> La fecha de fin debe ser posterior a la fecha de inicio.';
            }
            return;
        }
        
        // Mostrar validación exitosa
        if (fechaInicioVal && validacionPreview) {
            validacionPreview.style.display = 'block';
            validacionPreview.className = 'alert alert-success';
            if (fechaFinVal) {
                validacionContent.innerHTML = '<strong>Válido:</strong> Rango de fechas del ' + 
                    formatDate(fechaInicioVal) + ' al ' + formatDate(fechaFinVal);
            } else {
                validacionContent.innerHTML = '<strong>Válido:</strong> Día específico ' + formatDate(fechaInicioVal);
            }
        }
    }
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES');
    }
    
    // Actualizar contador de caracteres
    if (descripcion) {
        descripcion.addEventListener('input', function() {
            const current = this.value.length;
            const max = 255;
            const remaining = max - current;
            
            let color = 'text-muted';
            if (remaining < 50) color = 'text-warning';
            if (remaining < 20) color = 'text-danger';
            
            const formText = this.parentElement.querySelector('.form-text');
            if (formText) {
                formText.innerHTML = `<span class="${color}">${remaining} caracteres restantes</span>`;
            }
        });
    }
});
</script>
@endsection