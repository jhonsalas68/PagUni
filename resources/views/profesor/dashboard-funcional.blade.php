@extends('layouts.dashboard')

@section('title', 'Dashboard Profesor')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-chalkboard-teacher"></i> Dashboard Profesor
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="{{ route('profesor.historial-asistencias') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-history"></i> Historial
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Clases Hoy</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['clases_hoy'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Asistidas Hoy</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['clases_asistidas_hoy'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pendientes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['clases_pendientes_hoy'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Materias</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['total_materias'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mis Clases de Hoy -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-day"></i> Mis Clases de Hoy - {{ $hoy->format('d/m/Y') }}
                    </h6>
                    <div class="dropdown no-arrow">
                        <button class="btn btn-sm btn-outline-primary" onclick="location.reload()">
                            <i class="fas fa-sync-alt"></i> Actualizar
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($horariosHoy->count() > 0)
                        <div class="row">
                            @foreach($horariosHoy as $horario)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card border-left-primary h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title text-primary mb-0">
                                                    {{ $horario->cargaAcademica->grupo->materia->nombre ?? 'Materia' }}
                                                </h6>
                                                @php
                                                    $asistencia = $asistenciasHoy->get($horario->id);
                                                @endphp
                                                @if($asistencia)
                                                    <span class="badge bg-{{ $asistencia->estado_color }}">
                                                        {{ $asistencia->estado_texto }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">Sin Registro</span>
                                                @endif
                                            </div>
                                            
                                            <div class="mb-3">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i> {{ $horario->hora_inicio }} - {{ $horario->hora_fin }}<br>
                                                    <i class="fas fa-door-open"></i> {{ $horario->aula->codigo_aula ?? 'N/A' }}<br>
                                                    <i class="fas fa-users"></i> {{ $horario->cargaAcademica->grupo->identificador ?? 'N/A' }}
                                                </small>
                                            </div>

                                            @if($asistencia)
                                                <div class="mb-3">
                                                    @if($asistencia->hora_entrada)
                                                        <small class="text-success">
                                                            <i class="fas fa-sign-in-alt"></i> Entrada: {{ $asistencia->hora_entrada }}
                                                        </small><br>
                                                    @endif
                                                    @if($asistencia->hora_salida)
                                                        <small class="text-info">
                                                            <i class="fas fa-sign-out-alt"></i> Salida: {{ $asistencia->hora_salida }}
                                                        </small><br>
                                                    @endif
                                                    @if($asistencia->modalidad)
                                                        <small class="text-muted">
                                                            <i class="fas fa-laptop"></i> {{ ucfirst($asistencia->modalidad) }}
                                                        </small>
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="d-grid">
                                                <button class="btn btn-primary btn-sm" onclick="generarQRSimple({{ $horario->id }})">
                                                    <i class="fas fa-qrcode"></i> Generar QR
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No tienes clases programadas para hoy</h5>
                            <p class="text-muted">¡Disfruta tu día libre!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Horario Semanal -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-week"></i> Mi Horario Semanal
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Día</th>
                                    <th>Horario</th>
                                    <th>Materia</th>
                                    <th>Aula</th>
                                    <th>Grupo</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
                                @endphp
                                @forelse($horariosSemana->flatten() as $horario)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">{{ $dias[$horario->dia_semana] ?? 'N/A' }}</span>
                                        </td>
                                        <td>{{ $horario->hora_inicio }} - {{ $horario->hora_fin }}</td>
                                        <td>{{ $horario->cargaAcademica->grupo->materia->nombre ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $horario->aula->codigo_aula ?? 'N/A' }}</span>
                                        </td>
                                        <td>{{ $horario->cargaAcademica->grupo->identificador ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ ucfirst($horario->tipo_clase) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No tienes horarios asignados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Simple para QR -->
<div class="modal fade" id="modalQRFuncional" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Generar Código QR</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="modalidadFuncional" class="form-label">Modalidad de la Clase</label>
                    <select class="form-select" id="modalidadFuncional" required>
                        <option value="">Selecciona la modalidad</option>
                        <option value="presencial">Presencial</option>
                        <option value="virtual">Virtual</option>
                    </select>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    El código QR será válido por 30 minutos.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="confirmarQRFuncional()">Generar QR</button>
            </div>
        </div>
    </div>
</div>

<script>
let horarioSeleccionadoFuncional = null;

function generarQRSimple(horarioId) {
    console.log('Generando QR para horario:', horarioId);
    horarioSeleccionadoFuncional = horarioId;
    document.getElementById('modalidadFuncional').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('modalQRFuncional'));
    modal.show();
}

function confirmarQRFuncional() {
    const modalidad = document.getElementById('modalidadFuncional').value;
    
    if (!modalidad) {
        alert('Debes seleccionar la modalidad de la clase');
        return;
    }

    const url = '{{ route("profesor.generar-qr") }}';
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    console.log('Enviando petición a:', url);
    console.log('Datos:', { horario_id: horarioSeleccionadoFuncional, modalidad: modalidad });

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            horario_id: horarioSeleccionadoFuncional,
            modalidad: modalidad
        })
    })
    .then(response => {
        console.log('Respuesta recibida:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Datos de respuesta:', data);
        
        if (data.success) {
            // Cerrar modal
            bootstrap.Modal.getInstance(document.getElementById('modalQRFuncional')).hide();
            
            // Abrir QR en nueva ventana
            const qrVistaUrl = `{{ url('/profesor/qr-vista') }}/${data.data.qr_token}`;
            window.open(qrVistaUrl, '_blank', 'width=800,height=900,scrollbars=yes,resizable=yes');
            
            // Mostrar mensaje de éxito
            alert('QR generado exitosamente');
            
            // Recargar página
            setTimeout(() => location.reload(), 1000);
        } else {
            alert('Error: ' + (data.error || 'Error al generar QR'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión: ' + error.message);
    });
}
</script>
@endsection