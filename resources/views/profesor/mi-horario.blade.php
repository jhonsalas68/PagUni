@extends('layouts.dashboard')

@section('title', 'Mi Horario')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-calendar-week"></i> Mi Horario de Clases
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="{{ route('profesor.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Dashboard
                        </a>
                        <button class="btn btn-outline-primary" onclick="window.print()">
                            <i class="fas fa-print"></i> Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Horario Semanal -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-alt"></i> Horario Semanal Completo
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $dias = [
                            1 => 'Lunes', 
                            2 => 'Martes', 
                            3 => 'Miércoles', 
                            4 => 'Jueves', 
                            5 => 'Viernes', 
                            6 => 'Sábado', 
                            7 => 'Domingo'
                        ];
                    @endphp

                    @if($horariosPorDia->count() > 0)
                        @foreach($dias as $numeroDia => $nombreDia)
                            @if($horariosPorDia->has($numeroDia))
                                <div class="mb-4">
                                    <h5 class="text-primary border-bottom pb-2">
                                        <i class="fas fa-calendar-day"></i> {{ $nombreDia }}
                                    </h5>
                                    
                                    <div class="row">
                                        @foreach($horariosPorDia[$numeroDia] as $horario)
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card border-left-primary h-100">
                                                    <div class="card-body">
                                                        <h6 class="card-title text-primary mb-2">
                                                            {{ $horario->cargaAcademica->grupo->materia->nombre ?? 'N/A' }}
                                                        </h6>
                                                        
                                                        <div class="mb-2">
                                                            <small class="text-muted">
                                                                <i class="fas fa-clock text-primary"></i> 
                                                                <strong>{{ $horario->hora_inicio }} - {{ $horario->hora_fin }}</strong>
                                                            </small>
                                                        </div>
                                                        
                                                        <div class="mb-2">
                                                            <small class="text-muted">
                                                                <i class="fas fa-door-open text-info"></i> 
                                                                Aula: <strong>{{ $horario->aula->codigo_aula ?? 'N/A' }}</strong>
                                                            </small>
                                                        </div>
                                                        
                                                        <div class="mb-2">
                                                            <small class="text-muted">
                                                                <i class="fas fa-users text-success"></i> 
                                                                Grupo: <strong>{{ $horario->cargaAcademica->grupo->identificador ?? 'N/A' }}</strong>
                                                            </small>
                                                        </div>
                                                        
                                                        <div class="mb-2">
                                                            <span class="badge bg-secondary">
                                                                {{ ucfirst($horario->tipo_clase) }}
                                                            </span>
                                                        </div>

                                                        <div class="mt-3">
                                                            <button class="btn btn-outline-warning btn-sm w-100" 
                                                                    onclick="mostrarModalJustificar({{ $horario->id }}, '{{ $horario->cargaAcademica->grupo->materia->nombre ?? 'N/A' }}', '{{ $nombreDia }}', '{{ $horario->hora_inicio }} - {{ $horario->hora_fin }}')">
                                                                <i class="fas fa-edit"></i> Justificar Falta
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">No tienes horarios asignados</h4>
                            <p class="text-muted">Contacta al administrador para que te asigne horarios de clase.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen Estadístico -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Clases</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $horariosPorDia->flatten()->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Materias</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $horariosPorDia->flatten()->pluck('cargaAcademica.grupo.materia.nombre')->unique()->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Aulas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $horariosPorDia->flatten()->pluck('aula.codigo_aula')->unique()->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-door-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Días Activos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $horariosPorDia->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Justificar Falta -->
<div class="modal fade" id="modalJustificar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-edit"></i> Justificar Falta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6 class="text-primary" id="infoClaseJustificar">Información de la clase</h6>
                    <div id="detallesClaseJustificar" class="text-muted small"></div>
                </div>
                
                <div class="mb-3">
                    <label for="fechaFalta" class="form-label">Fecha de la Falta</label>
                    <input type="date" class="form-control" id="fechaFalta" required>
                </div>

                <div class="mb-3">
                    <label for="tipoJustificacion" class="form-label">Tipo de Justificación</label>
                    <select class="form-select" id="tipoJustificacion" required>
                        <option value="">Selecciona el tipo</option>
                        <option value="medica">Médica</option>
                        <option value="personal">Personal</option>
                        <option value="academica">Académica</option>
                        <option value="administrativa">Administrativa</option>
                        <option value="otra">Otra</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="justificacionTexto" class="form-label">Justificación</label>
                    <textarea class="form-control" id="justificacionTexto" rows="4" 
                              placeholder="Describe el motivo de la falta..." maxlength="500" required></textarea>
                    <div class="form-text">Máximo 500 caracteres</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" onclick="enviarJustificacion()">
                    <i class="fas fa-save"></i> Registrar Justificación
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let horarioSeleccionadoJustificar = null;

function mostrarModalJustificar(horarioId, materia, dia, horario) {
    horarioSeleccionadoJustificar = horarioId;
    
    document.getElementById('infoClaseJustificar').textContent = materia;
    document.getElementById('detallesClaseJustificar').innerHTML = `
        <i class="fas fa-calendar-day"></i> ${dia}<br>
        <i class="fas fa-clock"></i> ${horario}
    `;
    
    // Establecer fecha por defecto (hoy)
    document.getElementById('fechaFalta').value = new Date().toISOString().split('T')[0];
    document.getElementById('tipoJustificacion').value = '';
    document.getElementById('justificacionTexto').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('modalJustificar'));
    modal.show();
}

function enviarJustificacion() {
    const fecha = document.getElementById('fechaFalta').value;
    const tipo = document.getElementById('tipoJustificacion').value;
    const justificacion = document.getElementById('justificacionTexto').value;
    
    if (!fecha || !tipo || !justificacion.trim()) {
        alert('Todos los campos son obligatorios');
        return;
    }

    const boton = document.querySelector('[onclick="enviarJustificacion()"]');
    boton.disabled = true;
    boton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

    fetch('{{ route("profesor.justificar-asistencia") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            horario_id: horarioSeleccionadoJustificar,
            fecha: fecha,
            tipo_justificacion: tipo,
            justificacion: justificacion
        })
    })
    .then(response => response.json())
    .then(data => {
        boton.disabled = false;
        boton.innerHTML = '<i class="fas fa-save"></i> Registrar Justificación';
        
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalJustificar')).hide();
            alert('Justificación registrada exitosamente');
        } else {
            alert('Error: ' + (data.error || 'Error al registrar justificación'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        boton.disabled = false;
        boton.innerHTML = '<i class="fas fa-save"></i> Registrar Justificación';
        alert('Error de conexión: ' + error.message);
    });
}
</script>

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
@media print {
    .btn-toolbar, .modal { display: none !important; }
    .card { border: 1px solid #000 !important; }
}
</style>
@endsection
@endsection