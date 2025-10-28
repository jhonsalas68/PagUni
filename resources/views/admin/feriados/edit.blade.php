@extends('layouts.dashboard')

@section('title', 'Editar Feriado')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <h3 class="card-title mb-2 mb-md-0">
                        <i class="fas fa-edit me-2"></i>
                        <span class="d-none d-sm-inline">Editar Día No Laborable/Feriado</span>
                        <span class="d-sm-none">Editar Feriado</span>
                    </h3>
                    <a href="{{ route('admin.feriados.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>
                        <span class="d-none d-sm-inline">Volver a la Lista</span>
                        <span class="d-sm-none">Volver</span>
                    </a>
                </div>
            </div>

                <form method="POST" action="{{ route('admin.feriados.update', $feriado) }}">
                    @csrf
                    @method('PUT')
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

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_inicio">
                                        Fecha de Inicio <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('fecha_inicio') is-invalid @enderror" 
                                           id="fecha_inicio" 
                                           name="fecha_inicio" 
                                           value="{{ old('fecha_inicio', $feriado->fecha_inicio->format('Y-m-d')) }}" 
                                           required>
                                    @error('fecha_inicio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_fin">
                                        Fecha de Fin <small class="text-muted">(Opcional)</small>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('fecha_fin') is-invalid @enderror" 
                                           id="fecha_fin" 
                                           name="fecha_fin" 
                                           value="{{ old('fecha_fin', $feriado->fecha_fin ? $feriado->fecha_fin->format('Y-m-d') : '') }}">
                                    @error('fecha_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Deje vacío para un día específico
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="descripcion">
                                        Descripción del Evento <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('descripcion') is-invalid @enderror" 
                                           id="descripcion" 
                                           name="descripcion" 
                                           value="{{ old('descripcion', $feriado->descripcion) }}" 
                                           maxlength="255"
                                           required>
                                    @error('descripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tipo">
                                        Tipo de Día No Laborable <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('tipo') is-invalid @enderror" 
                                            id="tipo" 
                                            name="tipo" 
                                            required>
                                        <option value="feriado" {{ old('tipo', $feriado->tipo) == 'feriado' ? 'selected' : '' }}>
                                            Feriado
                                        </option>
                                        <option value="receso" {{ old('tipo', $feriado->tipo) == 'receso' ? 'selected' : '' }}>
                                            Receso
                                        </option>
                                        <option value="asueto" {{ old('tipo', $feriado->tipo) == 'asueto' ? 'selected' : '' }}>
                                            Asueto
                                        </option>
                                    </select>
                                    @error('tipo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="activo" 
                                               name="activo" 
                                               value="1"
                                               {{ old('activo', $feriado->activo) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="activo">
                                            Feriado Activo
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Desmarque para desactivar temporalmente este feriado
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Información del feriado actual -->
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h5><i class="icon fas fa-info"></i> Información del Feriado:</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Fechas:</strong> {{ $feriado->rango_fechas }}<br>
                                            <strong>Tipo:</strong> {{ $feriado->tipo_formateado }}<br>
                                            <strong>Estado:</strong> 
                                            <span class="badge badge-{{ $feriado->activo ? 'success' : 'secondary' }}">
                                                {{ $feriado->activo ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Creado:</strong> {{ $feriado->created_at->format('d/m/Y H:i') }}<br>
                                            <strong>Última modificación:</strong> {{ $feriado->updated_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save mr-1"></i>
                                    Actualizar Feriado
                                </button>
                                <a href="{{ route('admin.feriados.show', $feriado) }}" class="btn btn-info ml-2">
                                    <i class="fas fa-eye mr-1"></i>
                                    Ver Detalles
                                </a>
                                <a href="{{ route('admin.feriados.index') }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-times mr-1"></i>
                                    Cancelar
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                <small class="text-muted">
                                    <span class="text-danger">*</span> Campos obligatorios
                                </small>
                            </div>
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
        
        if (fechaFinVal && fechaInicioVal && fechaFinVal <= fechaInicioVal) {
            alert('La fecha de fin debe ser posterior a la fecha de inicio.');
            fechaFin.value = '';
        }
    }
});
</script>
@endsection