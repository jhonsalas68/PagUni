@extends('layouts.dashboard')

@section('title', 'Panel de Control de Asistencias')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
.estado-presente { background-color: #d4edda !important; }
.estado-ausente { background-color: #f8d7da !important; }
.estado-en-clase { background-color: #cce5ff !important; }
.estado-tardanza { background-color: #fff3cd !important; }
.estado-justificado { background-color: #d1ecf1 !important; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-chart-line"></i> Panel de Control de Asistencias
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <input type="date" class="form-control" id="fechaPanel" 
                               value="{{ $fecha ?? date('Y-m-d') }}" onchange="cambiarFecha()">
                    </div>
                    <div class="btn-group me-2">
                        <button class="btn btn-outline-primary" onclick="actualizarTiempoReal()">
                            <i class="fas fa-sync-alt"></i> Actualizar
                        </button>
                        <button class="btn btn-outline-success" onclick="toggleAutoRefresh()">
                            <i class="fas fa-play" id="iconAutoRefresh"></i> Auto-refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas del Día -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Clases</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['total_clases'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Presentes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['profesores_presentes'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Ausentes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['profesores_ausentes'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Tardanzas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['tardanzas'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Justificados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['justificados'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Virtual</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['clases_virtuales'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-video fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estado Actual de Profesores -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users"></i> Estado Actual de Profesores - {{ isset($fechaCarbon) ? $fechaCarbon->format('d/m/Y') : date('d/m/Y') }}
                    </h6>
                    <div class="text-muted small">
                        Última actualización: <span id="ultimaActualizacion">{{ now()->format('H:i:s') }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($estadoProfesores->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="tablaEstados">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Profesor</th>
                                        <th>Materia</th>
                                        <th>Horario</th>
                                        <th>Aula</th>
                                        <th>Estado</th>
                                        <th>Modalidad</th>
                                        <th>Entrada</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($estadoProfesores as $estado)
                                        @php
                                            $claseEstado = '';
                                            switch($estado['estado_actual']) {
                                                case 'presente':
                                                case 'completado':
                                                    $claseEstado = 'estado-presente';
                                                    break;
                                                case 'ausente':
                                                    $claseEstado = 'estado-ausente';
                                                    break;
                                                case 'en_clase':
                                                    $claseEstado = 'estado-en-clase';
                                                    break;
                                                case 'tardanza':
                                                    $claseEstado = 'estado-tardanza';
                                                    break;
                                                case 'justificado':
                                                    $claseEstado = 'estado-justificado';
                                                    break;
                                            }
                                        @endphp
                                        
                                        <tr class="{{ $claseEstado }}" data-horario-id="{{ $estado['horario']->id }}">
                                            <td>
                                                <strong>{{ $estado['profesor']->nombre ?? 'N/A' }} {{ $estado['profesor']->apellido ?? '' }}</strong>
                                            </td>
                                            <td>{{ $estado['horario']->cargaAcademica->grupo->materia->nombre ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ $estado['horario']->hora_inicio }} - {{ $estado['horario']->hora_fin }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $estado['horario']->aula->codigo_aula ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $estadoTexto = '';
                                                    $estadoColor = 'secondary';
                                                    switch($estado['estado_actual']) {
                                                        case 'presente':
                                                        case 'completado':
                                                            $estadoTexto = 'Presente';
                                                            $estadoColor = 'success';
                                                            break;
                                                        case 'ausente':
                                                            $estadoTexto = 'Ausente';
                                                            $estadoColor = 'danger';
                                                            break;
                                                        case 'en_clase':
                                                            $estadoTexto = 'En Clase';
                                                            $estadoColor = 'primary';
                                                            break;
                                                        case 'tardanza':
                                                            $estadoTexto = 'Tardanza';
                                                            $estadoColor = 'warning';
                                                            break;
                                                        case 'justificado':
                                                            $estadoTexto = 'Justificado';
                                                            $estadoColor = 'info';
                                                            break;
                                                        case 'programado':
                                                            $estadoTexto = 'Programado';
                                                            $estadoColor = 'secondary';
                                                            break;
                                                        case 'en_horario_sin_registro':
                                                            $estadoTexto = 'Sin Registro';
                                                            $estadoColor = 'warning';
                                                            break;
                                                    }
                                                @endphp
                                                <span class="badge bg-{{ $estadoColor }}">{{ $estadoTexto }}</span>
                                            </td>
                                            <td>
                                                @if($estado['asistencia'] && $estado['asistencia']->modalidad)
                                                    <span class="badge bg-{{ $estado['asistencia']->modalidad === 'presencial' ? 'primary' : 'info' }}">
                                                        {{ ucfirst($estado['asistencia']->modalidad) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($estado['asistencia'] && $estado['asistencia']->hora_entrada)
                                                    {{ $estado['asistencia']->hora_entrada }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($estado['estado_actual'] === 'ausente' || $estado['estado_actual'] === 'en_horario_sin_registro')
                                                    <button class="btn btn-warning btn-sm" 
                                                            onclick="mostrarModalJustificar({{ $estado['profesor']->id ?? 0 }}, {{ $estado['horario']->id }}, '{{ $estado['profesor']->nombre ?? '' }} {{ $estado['profesor']->apellido ?? '' }}', '{{ $estado['horario']->cargaAcademica->grupo->materia->nombre ?? 'N/A' }}')">
                                                        <i class="fas fa-edit"></i> Justificar
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-between align-items-center mt-3 px-3">
                            <div class="text-muted">
                                Mostrando {{ $estadoProfesores->firstItem() ?? 0 }} a {{ $estadoProfesores->lastItem() ?? 0 }} 
                                de {{ $estadoProfesores->total() }} clases
                            </div>
                            <div>
                                {{ $estadoProfesores->appends(['fecha' => $fecha])->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">No hay clases programadas para hoy</h4>
                            <p class="text-muted">No se encontraron horarios para la fecha seleccionada.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Justificar desde Panel -->
<div class="modal fade" id="modalJustificarPanel" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-edit"></i> Justificar Asistencia (Administrador)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6 class="text-primary" id="infoProfesorPanel">Información del profesor</h6>
                    <div id="detallesClasePanel" class="text-muted small"></div>
                </div>

                <div class="mb-3">
                    <label for="tipoJustificacionPanel" class="form-label">Tipo de Justificación</label>
                    <select class="form-select" id="tipoJustificacionPanel" required>
                        <option value="">Selecciona el tipo</option>
                        <option value="medica">Médica</option>
                        <option value="personal">Personal</option>
                        <option value="academica">Académica</option>
                        <option value="administrativa">Administrativa</option>
                        <option value="otra">Otra</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="justificacionTextoPanel" class="form-label">Justificación</label>
                    <textarea class="form-control" id="justificacionTextoPanel" rows="4" 
                              placeholder="Describe el motivo de la justificación..." maxlength="500" required></textarea>
                    <div class="form-text">Máximo 500 caracteres</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" onclick="enviarJustificacionPanel()">
                    <i class="fas fa-save"></i> Registrar Justificación
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let profesorSeleccionado = null;
let horarioSeleccionado = null;
let autoRefreshInterval = null;
let autoRefreshActivo = false;

function cambiarFecha() {
    const fecha = document.getElementById('fechaPanel').value;
    if (fecha) {
        const url = new URL(window.location);
        url.searchParams.set('fecha', fecha);
        window.location.href = url.toString();
    }
}

function actualizarTiempoReal() {
    const fecha = document.getElementById('fechaPanel').value;
    
    fetch(`{{ route('admin.panel-asistencia.tiempo-real') }}?fecha=${fecha}`)
        .then(response => response.json())
        .then(data => {
            // Actualizar hora
            document.getElementById('ultimaActualizacion').textContent = data.hora_actual;
            
            // Actualizar tabla (implementación simplificada)
            console.log('Datos actualizados:', data);
            
            // Aquí podrías actualizar la tabla dinámicamente
            // Por simplicidad, recargamos la página
            if (autoRefreshActivo) {
                setTimeout(() => location.reload(), 100);
            }
        })
        .catch(error => {
            console.error('Error actualizando:', error);
        });
}

function toggleAutoRefresh() {
    const icono = document.getElementById('iconAutoRefresh');
    
    if (autoRefreshActivo) {
        clearInterval(autoRefreshInterval);
        autoRefreshActivo = false;
        icono.className = 'fas fa-play';
    } else {
        autoRefreshInterval = setInterval(actualizarTiempoReal, 30000); // 30 segundos
        autoRefreshActivo = true;
        icono.className = 'fas fa-pause';
    }
}

function mostrarModalJustificar(profesorId, horarioId, nombreProfesor, materia) {
    profesorSeleccionado = profesorId;
    horarioSeleccionado = horarioId;
    
    document.getElementById('infoProfesorPanel').textContent = nombreProfesor;
    document.getElementById('detallesClasePanel').innerHTML = `
        <i class="fas fa-book"></i> ${materia}<br>
        <i class="fas fa-calendar-day"></i> {{ isset($fechaCarbon) ? $fechaCarbon->format('d/m/Y') : date('d/m/Y') }}
    `;
    
    document.getElementById('tipoJustificacionPanel').value = '';
    document.getElementById('justificacionTextoPanel').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('modalJustificarPanel'));
    modal.show();
}

function enviarJustificacionPanel() {
    const tipo = document.getElementById('tipoJustificacionPanel').value;
    const justificacion = document.getElementById('justificacionTextoPanel').value;
    const fecha = document.getElementById('fechaPanel').value;
    
    if (!tipo || !justificacion.trim()) {
        alert('Todos los campos son obligatorios');
        return;
    }

    const boton = document.querySelector('[onclick="enviarJustificacionPanel()"]');
    boton.disabled = true;
    boton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

    fetch('{{ route("admin.panel-asistencia.justificar") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            profesor_id: profesorSeleccionado,
            horario_id: horarioSeleccionado,
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
            bootstrap.Modal.getInstance(document.getElementById('modalJustificarPanel')).hide();
            alert('Justificación registrada exitosamente');
            location.reload();
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

// Auto-scroll al inicio al cambiar de página
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.search.includes('page=')) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});
</script>

<style>
/* Estilos para la paginación Bootstrap */
.pagination {
    margin: 0;
}
.pagination .page-link {
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
    color: #0d6efd;
    background-color: #fff;
    border: 1px solid #dee2e6;
}
.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: #fff;
}
.pagination .page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}
.pagination .page-link:hover {
    color: #0a58ca;
    background-color: #e9ecef;
    border-color: #dee2e6;
}
</style>
@endsection