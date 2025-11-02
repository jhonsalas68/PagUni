@extends('layouts.dashboard')

@section('title', 'Dashboard Profesor - Simple')

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
                    <i class="fas fa-chalkboard-teacher"></i> Dashboard Profesor (Versión Simple)
                </h1>
            </div>
        </div>
    </div>

    <!-- Debug Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <h5>Información de Debug:</h5>
                <ul>
                    <li><strong>Fecha:</strong> {{ $hoy->format('d/m/Y') }}</li>
                    <li><strong>Día de la semana:</strong> {{ $hoy->dayOfWeek === 0 ? 7 : $hoy->dayOfWeek }}</li>
                    <li><strong>Horarios hoy:</strong> {{ $horariosHoy->count() }}</li>
                    <li><strong>Asistencias hoy:</strong> {{ $asistenciasHoy->count() }}</li>
                    <li><strong>Horarios semana:</strong> {{ $horariosSemana->flatten()->count() }}</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Clases de Hoy - Versión Simple -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-day"></i> Mis Clases de Hoy - {{ $hoy->format('d/m/Y') }}
                    </h6>
                </div>
                <div class="card-body">
                    @if($horariosHoy->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Horario</th>
                                        <th>Materia</th>
                                        <th>Aula</th>
                                        <th>Grupo</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($horariosHoy as $horario)
                                        @php
                                            $asistencia = $asistenciasHoy->get($horario->id);
                                        @endphp
                                        <tr>
                                            <td>{{ $horario->id }}</td>
                                            <td>{{ $horario->hora_inicio }} - {{ $horario->hora_fin }}</td>
                                            <td>
                                                @if($horario->cargaAcademica && $horario->cargaAcademica->grupo && $horario->cargaAcademica->grupo->materia)
                                                    {{ $horario->cargaAcademica->grupo->materia->nombre }}
                                                @else
                                                    <span class="text-danger">Error en relación</span>
                                                @endif
                                            </td>
                                            <td>{{ $horario->aula->codigo_aula ?? 'N/A' }}</td>
                                            <td>{{ $horario->cargaAcademica->grupo->identificador ?? 'N/A' }}</td>
                                            <td>
                                                @if($asistencia)
                                                    <span class="badge bg-{{ $asistencia->estado_color }}">
                                                        {{ $asistencia->estado_texto }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">Sin Registro</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-primary btn-sm" onclick="generarQRSimple({{ $horario->id }})">
                                                    <i class="fas fa-qrcode"></i> Generar QR
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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

    <!-- Horario Semanal - Versión Simple -->
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
                                    <th>ID</th>
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
                                        <td>{{ $horario->id }}</td>
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
                                        <td colspan="7" class="text-center text-muted">No tienes horarios asignados</td>
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
<div class="modal fade" id="modalQRSimple" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Generar QR</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="modalidadSimple" class="form-label">Modalidad de la Clase</label>
                    <select class="form-select" id="modalidadSimple" required>
                        <option value="">Selecciona la modalidad</option>
                        <option value="presencial">Presencial</option>
                        <option value="virtual">Virtual</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="confirmarQRSimple()">Generar QR</button>
            </div>
        </div>
    </div>
</div>

<script>
let horarioSeleccionadoSimple = null;

function generarQRSimple(horarioId) {
    horarioSeleccionadoSimple = horarioId;
    document.getElementById('modalidadSimple').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('modalQRSimple'));
    modal.show();
}

function confirmarQRSimple() {
    const modalidad = document.getElementById('modalidadSimple').value;
    
    if (!modalidad) {
        alert('Debes seleccionar la modalidad de la clase');
        return;
    }

    const url = '{{ route("profesor.generar-qr") }}';
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            horario_id: horarioSeleccionadoSimple,
            modalidad: modalidad
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cerrar modal
            bootstrap.Modal.getInstance(document.getElementById('modalQRSimple')).hide();
            
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